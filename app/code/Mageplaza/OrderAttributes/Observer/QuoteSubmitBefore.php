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

namespace Mageplaza\OrderAttributes\Observer;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\AttributesRepository;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory;
use Zend_Validate_Exception;
use Zend_Validate_File_Upload;

/**
 * Class QuoteSubmitBefore
 * @package Mageplaza\OrderAttributes\Observer
 */
class QuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var string
     */
    protected $scope = 'mpOrderAttributes';

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Zend_Validate_File_Upload
     */
    protected $fileUpload;

    /**
     * @var AttributesRepository
     */
    protected $attributeRepository;

    /**
     * QuoteSubmitBefore constructor.
     *
     * @param QuoteFactory $quoteFactory
     * @param Data $helperData
     * @param RequestInterface $request
     * @param Zend_Validate_File_Upload $fileUpload
     * @param AttributesRepository $attributeRepository
     */
    public function __construct(
        QuoteFactory $quoteFactory,
        Data $helperData,
        RequestInterface $request,
        Zend_Validate_File_Upload $fileUpload,
        AttributesRepository $attributeRepository
    ) {
        $this->helperData          = $helperData;
        $this->request             = $request;
        $this->quoteFactory        = $quoteFactory;
        $this->fileUpload          = $fileUpload;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param Observer $observer
     *
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Zend_Validate_Exception
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var AbstractExtensibleModel|OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $quoteAttribute = $this->quoteFactory->create()->load($quote->getId());

        $result = [];

        if ($quoteAttribute->getId() && $this->helperData->isArea(Area::AREA_GRAPHQL)) {
            $result = $this->helperData->prepareAttributes($quote->getStoreId(), $quoteAttribute->getData());
        } else {
            if ($this->helperData->isArea(Area::AREA_ADMINHTML)) {
                $this->processAttributesFromAdmin($quote, $quoteAttribute);
            }

            $quoteSubmitAttributes = $quote->getMpOrderAttributes();
            if ($quoteSubmitAttributes) {
                $result = $this->helperData->validateAttributes($quote, $quoteSubmitAttributes, $quoteAttribute);
            }
        }

        if ($result) {
            $order->addData($result);
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param QuoteAttribute $quoteAttribute
     *
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function processAttributesFromAdmin($quote, $quoteAttribute)
    {
        $data = $this->request->getPostValue();
        if (isset($data[$this->scope])) {
            foreach ($data[$this->scope] as &$datum) {
                if (is_array($datum)) {
                    $datum = implode(',', $datum);
                }
            }

            $fileUpload = $this->fileUpload->getFiles();
            if (!empty($fileUpload[$this->scope])) {
                $files = $this->formatFilesArray($fileUpload[$this->scope]);
                foreach ($files as $attrCode => $file) {
                    // skip case when no file is chosen
                    if (empty($file['tmp_name'])) {
                        continue;
                    }

                    $result = $this->attributeRepository->uploadFile($attrCode, $files);
                    if ($result->getError()) {
                        throw new LocalizedException(__($result->getError()));
                    }

                    $fileData = $this->helperData->jsonEncodeData($result->getData());
                    $quoteAttribute->setData($attrCode, $fileData);
                    $data[$this->scope][$attrCode] = $fileData;
                }
            }

            $quote->setMpOrderAttributes($data[$this->scope]);
        }
    }

    /**
     * Format files array for multiple uploading files
     *
     * @param array $files
     *
     * @return array
     */
    protected function formatFilesArray($files)
    {
        $result = [];

        foreach ($files as $key => $value) {
            foreach ($value as $index => $item) {
                $result[$index][$key] = $item;
            }
        }

        return $result;
    }
}
