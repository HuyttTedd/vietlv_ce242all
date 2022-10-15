<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Model;

use Exception;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\Swatch;
use Mageplaza\OrderAttributes\Api\AttributesRepositoryInterface;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use Psr\Log\LoggerInterface;
use Zend_Validate_File_Upload;

/**
 * Class AttributesRepository
 * @package Mageplaza\OrderAtributes\Api
 */
class AttributesRepository implements AttributesRepositoryInterface
{

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Zend_Validate_File_Upload
     */
    protected $fileUpload;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteAttributeFactory;

    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * AttributesRepository constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     * @param CartRepositoryInterface $cartRepository
     * @param Zend_Validate_File_Upload $fileUpload
     * @param UploaderFactory $uploaderFactory
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteFactory $quoteAttributeFactory
     * @param Media $swatchHelper
     * @param Filesystem $fileSystem
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        Data $helperData,
        CartRepositoryInterface $cartRepository,
        Zend_Validate_File_Upload $fileUpload,
        UploaderFactory $uploaderFactory,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteAttributeFactory $quoteAttributeFactory,
        Media $swatchHelper,
        Filesystem $fileSystem,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->helperData = $helperData;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->fileUpload = $fileUpload;
        $this->logger = $logger;
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteAttributeFactory = $quoteAttributeFactory;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }

        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        foreach ($collection->getItems() as $item) {
            if ($item->getAdditionalData()) {
                $additionalData = $this->helperData->jsonDecodeData($item->getAdditionalData());
                foreach ($additionalData as $key => $data) {
                    if (!empty($data['swatch_type']) && $data['swatch_type'] === Swatch::SWATCH_TYPE_VISUAL_IMAGE) {
                        $additionalData[$key]['swatch_value'] = $this->swatchHelper->getSwatchAttributeImage(
                            'swatch_thumb',
                            $data['swatch_value']
                        );
                    }
                }

                $item->setAdditionalData($this->helperData->jsonEncodeData($additionalData));
            }

            if ($item->getOptions()) {
                $options = $this->helperData->jsonDecodeData($item->getOptions());
                $result = [];
                if (!empty($options['option']['value'])) {
                    $result['options'] = $options['option']['value'];
                }

                if (!empty($options['default'])) {
                    $result['default'] = $options['default'];
                }
                $item->setOptions($this->helperData->jsonEncodeData($result));
            }
        }
        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @param string $attrCode
     * @param Uploader $uploader
     * @param array $files
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateFile($attrCode, $uploader, $files)
    {
        $attribute = $this->collectionFactory->create()->addFieldToFilter('attribute_code', $attrCode)->fetchItem();
        if (!$attribute || !$attribute->getId()) {
            throw new NoSuchEntityException(__('No such entity id!'));
        }

        $allowExtensions = $attribute->getAllowExtensions();
        if ($allowExtensions) {
            $extensions = array_map('trim', explode(',', $allowExtensions));
            $uploader->setAllowedExtensions($extensions);
        } elseif ($attribute->getFrontendInput() === 'image') {
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        }

        if ($attribute->getMaxFileSize() && $files[$attrCode]['size'] > $attribute->getMaxFileSize()) {
            throw new LocalizedException(
                __(
                    '%1 must be less than or equal to %2 bytes.',
                    $files[$attrCode]['name'],
                    $attribute->getMaxFileSize()
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function guestUpload($cartId)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->upload($quoteIdMask->getQuoteId());
    }

    /**
     * @inheritDoc
     */
    public function upload($cartId)
    {
        try {
            $quote = $this->cartRepository->getActive($cartId);

            $files = $this->fileUpload->getFiles();
            if (empty($files)) {
                throw new LocalizedException(__('File is empty.'));
            }

            $attrCode = key($files);

            $result = $this->uploadFile($attrCode, $files);
            if (!$result->getError()) {
                $quoteAttribute = $this->quoteAttributeFactory->create()->load($quote->getId());

                $quoteAttribute->saveAttributeData(
                    $quote->getId(),
                    [$attrCode => $this->helperData->jsonEncodeData($result->getData())]
                );
            }

            return $result;
        } catch (Exception $e) {
            $result = [
                'error' => __($e->getMessage())
            ];

            return new FileResult($result);
        }
    }

    /**
     * @param string $attributeCode
     * @param array $files
     * @param bool $isReturnUrl
     *
     * @return FileResult
     */
    public function uploadFile($attributeCode, $files, $isReturnUrl = true)
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $files[$attributeCode]]);
            $this->validateFile($attributeCode, $uploader, $files);

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);

            $result = $uploader->save($mediaDirectory->getAbsolutePath($this->helperData->getBaseTmpMediaPath()));

            if ($isReturnUrl) {
                unset($result['tmp_name'], $result['path']);

                $result['url'] = $this->helperData->getTmpMediaUrl($result['file']);
            }
        } catch (Exception $e) {
            $this->logger->critical($e);
            $result = [
                'error' => __($e->getMessage())
            ];
        }

        return new FileResult($result);
    }
}
