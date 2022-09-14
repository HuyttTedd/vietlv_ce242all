<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Controller\Adminhtml\Image;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\Repository;
use Amasty\GiftCard\Controller\Adminhtml\AbstractImage;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends AbstractImage
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        Repository $repository,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $id = (int)$this->getRequest()->getParam(ImageInterface::IMAGE_ID);
            try {
                if ($id) {
                    $model = $this->repository->getById($id);
                } else {
                    $model = $this->repository->getEmptyImageModel();
                }
                $this->saveImage($model, $data);

                if ($this->getRequest()->getParam('back')) {
                    return $this->resultRedirectFactory->create()->setPath(
                        'amgcard/*/edit',
                        [ImageInterface::IMAGE_ID => $model->getId()]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $this->saveFormDataAndRedirect($data, $id);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the image data. Please review the error log.')
                );

                return $this->saveFormDataAndRedirect($data, $id);
            }
        }

        return $this->resultRedirectFactory->create()->setPath('amgcard/*/');
    }

    /**
     * @param ImageInterface $model
     * @param array $data
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function saveImage(ImageInterface $model, array $data)
    {
        if (isset($data['image'][0])) {
            $data[ImageInterface::IMAGE_PATH] = $data['image'][0]['name'];
            unset($data['image']);
        }

        if (isset($data['image_elements'])) {
            $imageElements = [];

            foreach ($data['image_elements'] as $name => $elementData) {
                $imageElement = $this->repository->getEmptyImageElementModel();
                $imageElement->setData($elementData);
                $imageElement->setName($name);
                $imageElements[$name] = $imageElement;
            }
            unset($data['image_elements']);
            $model->setImageElements($imageElements);
        }

        $model->addData($data);
        $model->setIsUserUpload(false);
        $this->repository->save($model);

        $this->messageManager->addSuccessMessage('The Image has been saved.');
        $this->dataPersistor->clear(\Amasty\GiftCard\Model\Image\Image::DATA_PERSISTOR_KEY);
    }

    /**
     * @param array $data
     * @param int $id
     *
     * @return Redirect
     */
    private function saveFormDataAndRedirect(array $data, int $id): Redirect
    {
        $this->dataPersistor->set(\Amasty\GiftCard\Model\Image\Image::DATA_PERSISTOR_KEY, $data);

        $resultRedirect = $this->resultRedirectFactory->create();
        if (!empty($id)) {
            $resultRedirect->setPath('amgcard/*/edit', [ImageInterface::IMAGE_ID => $id]);
        } else {
            $resultRedirect->setPath('amgcard/*/create');
        }

        return $resultRedirect;
    }
}
