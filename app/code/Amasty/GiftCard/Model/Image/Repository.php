<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Api\Data\ImageInterfaceFactory;
use Amasty\GiftCard\Api\Data\ImageElementsInterface;
use Amasty\GiftCard\Api\Data\ImageBakingInfoInterfaceFactory;
use Amasty\GiftCard\Api\ImageRepositoryInterface;
use Amasty\GiftCard\Model\Image\ResourceModel\CollectionFactory;
use Amasty\GiftCard\Model\Image\ResourceModel\ImageElementsCollectionFactory;
use Amasty\GiftCard\Model\Image\ResourceModel\Image;
use Amasty\GiftCard\Model\Image\ResourceModel\ImageElements;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements ImageRepositoryInterface
{
    /**
     * @var ImageInterfaceFactory
     */
    private $imageFactory;

    /**
     * @var Image
     */
    private $resource;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * @var ImageElements
     */
    private $imageElementsResource;

    /**
     * @var ImageElementsCollectionFactory
     */
    private $imageElementsCollectionFactory;

    /**
     * Model storage
     * @var ImageInterface[]
     */
    private $images = [];

    public function __construct(
        ImageInterfaceFactory $imageFactory,
        Image $resource,
        ImageElements $imageElementsResource,
        CollectionFactory $collectionFactory,
        ImageElementsCollectionFactory $imageElementsCollectionFactory,
        FileUpload $fileUpload
    ) {
        $this->imageFactory = $imageFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->fileUpload = $fileUpload;
        $this->imageElementsResource = $imageElementsResource;
        $this->imageElementsCollectionFactory = $imageElementsCollectionFactory;
    }

    public function getById(int $id): ImageInterface
    {
        if (!isset($this->images[$id])) {
            /** @var ImageInterface $image */
            $image = $this->imageFactory->create();
            $this->resource->load($image, $id);

            if (!$image->getImageId()) {
                throw new NoSuchEntityException(__('Image with specified ID "%1" not found.', $id));
            }

            $imageElementsCollection = $this->imageElementsCollectionFactory->create();
            $imageElementsCollection->addFieldToFilter(
                ImageElementsInterface::IMAGE_ID,
                $image->getImageId()
            );
            $imageElements = [];

            /** @var ImageElementsInterface $imageElement */
            foreach ($imageElementsCollection->getItems() as $imageElement) {
                $imageElements[$imageElement->getName()] = $imageElement;
            }
            $image->setImageElements($imageElements);

            $this->images[$id] = $image;
        }

        return $this->images[$id];
    }

    public function save(ImageInterface $image): ImageInterface
    {
        try {
            if ($image->getId()) {
                $image = $this->getById($image->getImageId())->addData($image->getData());
            }
            $this->resource->save($image);

            if ($image->getImagePath()) {
                $this->fileUpload->saveFromTemp(
                    $image->getImagePath(),
                    $image->isUserUpload()
                );
            }

            $imageElementsCollection = $this->imageElementsCollectionFactory->create()->addFieldToFilter(
                ImageElementsInterface::IMAGE_ID,
                $image->getImageId()
            );
            $imageElementsCollection->walk('delete');
            foreach ($image->getImageElements() as $imageElement) {
                $imageElement->setImageId($image->getImageId());
                $this->imageElementsResource->save($imageElement);
            }
            unset($this->images[$image->getImageId()]);
        } catch (\Exception $e) {
            if ($image->getImageId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save Image  %1. Error: %2',
                        [$image->getImageId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new Image. Error: %1', $e->getMessage()));
        }

        return $image;
    }

    public function delete(ImageInterface $image): bool
    {
        if ($this->isLastImage()) {
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove Image because it is last Image.'
                )
            );
        }
        $imagePath = $image->getImagePath();
        $isUser = $image->isUserUpload();

        try {
            $this->resource->delete($image);
        } catch (\Exception $e) {
            if ($image->getImageId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove Image with ID %1.Error: %2',
                        [$image->getImageId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unagle to delete image. Error: %1', $e->getMessage()));
        }
        $this->fileUpload->deleteImage($imagePath, $isUser);

        unset($this->images[$image->getImageId()]);

        return true;
    }

    public function deleteById(int $id): bool
    {
        $image = $this->getById($id);

        return $this->delete($image);
    }

    public function getList(): array
    {
        return $this->collectionFactory->create()->getItems();
    }

    public function getEmptyImageModel(): ImageInterface
    {
        return $this->imageFactory->create();
    }

    public function getEmptyImageElementModel(): ImageElementsInterface
    {
        return $this->imageElementsCollectionFactory->create()->getNewEmptyItem();
    }

    private function isLastImage(): bool
    {
        return (int)$this->collectionFactory->create()->getSize() <= 1;
    }
}
