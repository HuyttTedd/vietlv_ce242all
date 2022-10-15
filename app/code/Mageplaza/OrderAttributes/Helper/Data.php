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

namespace Mageplaza\OrderAttributes\Helper;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Form\Filter\FilterInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Config as CarrierConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\OrderFactory;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;

/**
 * Class Data
 * @package Mageplaza\OrderAttributes\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH  = 'mporderattributes';
    const TEMPLATE_MEDIA_PATH = 'mageplaza/order_attributes';

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CarrierConfig
     */
    protected $carrierConfig;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var array
     */
    protected $optionsInvalid = [];

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     * @param CarrierConfig $carrierConfig
     * @param CollectionFactory $collectionFactory
     * @param Repository $repository
     * @param Json $json
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        CarrierConfig $carrierConfig,
        CollectionFactory $collectionFactory,
        Repository $repository,
        Json $json,
        OrderFactory $orderFactory
    ) {
        $this->customerSession   = $customerSession;
        $this->carrierConfig     = $carrierConfig;
        $this->collectionFactory = $collectionFactory;
        $this->repository        = $repository;
        $this->json              = $json;
        $this->orderFactory      = $orderFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param array $value
     *
     * @return bool|false|string
     */
    public function jsonEncodeData($value)
    {
        try {
            return $this->json->serialize($value);
        } catch (Exception $e) {
            return '{}';
        }
    }

    /**
     * @param string $value
     *
     * @return array
     */
    public function jsonDecodeData($value)
    {
        try {
            return $this->json->unserialize($value);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isDisabled($store = null)
    {
        return !$this->isEnabled($store);
    }

    /**
     * @return array
     */
    public function getInputType()
    {
        $inputTypes = [
            'text'               => [
                'label'         => __('Text Field'),
                'backend_type'  => 'varchar',
                'field_type'    => 'text',
                'default_value' => 'text',
                'component'     => 'Magento_Ui/js/form/element/abstract',
                'elementTmpl'   => 'ui/form/element/input'
            ],
            'textarea'           => [
                'label'         => __('Text Area'),
                'backend_type'  => 'text',
                'field_type'    => 'textarea',
                'default_value' => 'textarea',
                'component'     => 'Magento_Ui/js/form/element/textarea',
                'elementTmpl'   => 'ui/form/element/textarea'
            ],
            'date'               => [
                'label'         => __('Date'),
                'backend_type'  => 'datetime',
                'field_type'    => 'date',
                'default_value' => 'date',
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/date',
                'elementTmpl'   => 'ui/form/element/date'
            ],
            'boolean'            => [
                'label'         => __('Yes/No'),
                'backend_type'  => 'int',
                'field_type'    => 'select',
                'default_value' => 'yesno',
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/select',
                'elementTmpl'   => 'ui/form/element/select',
            ],
            'select'             => [
                'label'         => __('Dropdown'),
                'backend_type'  => 'varchar',
                'field_type'    => 'select',
                'default_value' => false,
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/select',
                'elementTmpl'   => 'ui/form/element/select'
            ],
            'multiselect'        => [
                'label'         => __('Multiple-Select'),
                'backend_type'  => 'varchar',
                'field_type'    => 'multiselect',
                'default_value' => false,
                'component'     => 'Magento_Ui/js/form/element/multiselect',
                'elementTmpl'   => 'ui/form/element/multiselect'
            ],
            'select_visual'      => [
                'label'         => __('Single-Select With Image'),
                'backend_type'  => 'varchar',
                'field_type'    => 'select',
                'default_value' => false,
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/select',
                'elementTmpl'   => 'Mageplaza_OrderAttributes/form/element/radio-visual',
            ],
            'multiselect_visual' => [
                'label'         => __('Multiple-Select With Image'),
                'backend_type'  => 'varchar',
                'field_type'    => 'multiselect',
                'default_value' => false,
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/checkboxes',
                'elementTmpl'   => 'Mageplaza_OrderAttributes/form/element/checkbox-visual',
            ],
            'image'              => [
                'label'         => __('Media Image'),
                'backend_type'  => 'text',
                'field_type'    => 'file',
                'default_value' => false,
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/file-uploader',
                'elementTmpl'   => 'ui/form/element/uploader/uploader',
            ],
            'file'               => [
                'label'         => __('Single File Attachment'),
                'backend_type'  => 'text',
                'field_type'    => 'file',
                'default_value' => false,
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/file-uploader',
                'elementTmpl'   => 'ui/form/element/uploader/uploader',
            ],
            'textarea_visual'    => [
                'label'         => __('Content'),
                'backend_type'  => 'text',
                'field_type'    => 'content',
                'default_value' => 'content',
                'component'     => 'Mageplaza_OrderAttributes/js/form/element/textarea',
                'elementTmpl'   => 'ui/form/element/textarea',
            ]
        ];

        return $inputTypes;
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getDefaultValueByInput($inputType)
    {
        $inputTypes = $this->getInputType();
        if (isset($inputTypes[$inputType])) {
            $value = $inputTypes[$inputType]['default_value'];
            if ($value) {
                return 'default_value_' . $value;
            }
        }

        return false;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getBackendTypeByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['backend_type'])) {
            return $inputTypes[$inputType]['backend_type'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getFieldTypeByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['field_type'])) {
            return $inputTypes[$inputType]['field_type'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getComponentByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['component'])) {
            return $inputTypes[$inputType]['component'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getElementTmplByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['elementTmpl'])) {
            return $inputTypes[$inputType]['elementTmpl'];
        }

        return null;
    }

    /**
     * @param null $storeId
     * @param null $groupId
     *
     * @return Attribute[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getFilteredAttributes($storeId = null, $groupId = null)
    {
        return $this->getOrderAttributesCollection($storeId, $groupId);
    }

    /**
     * @param null|string|int $storeId
     * @param null|string|int $groupId
     * @param array $filters
     * @param bool $isCheckVisible
     *
     * @return array|Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getOrderAttributesCollection($storeId, $groupId, $isCheckVisible = true, $filters = [])
    {
        $result = [];

        $attributes = $this->collectionFactory->create();
        if ($filters) {
            foreach ($filters as $field => $cond) {
                $attributes->addFieldToFilter($field, $cond);
            }
        }

        $items = $attributes->getItems();
        if (!$isCheckVisible) {
            return $items;
        }

        foreach ($items as $attribute) {
            /**
             * @var Attribute $attribute
             */
            if ($this->isVisible($attribute, $storeId, $groupId)) {
                $result[] = $attribute;
            }
        }

        return $result;
    }

    /**
     * @param Attribute $attribute
     * @param string|null $storeId
     * @param string|null $groupId
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isVisible($attribute, $storeId, $groupId)
    {
        $storeId = $storeId ?: $this->getScopeId();
        $groupId = $groupId ?: $this->getGroupId();
        $stores  = $attribute->getStoreId() ?: 0;
        $stores  = explode(',', $stores);
        $groups  = $attribute->getCustomerGroup() ?: 0;
        $groups  = explode(',', $groups);

        $isVisibleStore = in_array(0, $stores) || in_array($storeId, $stores);
        $isVisibleGroup = (!$groupId && $this->isAdmin()) ?: in_array($groupId, $groups);

        return $isVisibleStore && $isVisibleGroup && $attribute->getPosition();
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getScopeId()
    {
        $scopeStore = $this->_request->getParam(ScopeInterface::SCOPE_STORE);
        $scopeId    = $scopeStore ?: $this->storeManager->getStore()->getId();

        if ($website = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            $defaultStore = $this->storeManager->getWebsite($website)->getDefaultStore();
            if ($defaultStore) {
                $scopeId = $defaultStore->getId();
            }
        }

        return $scopeId;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getGroupId();
        }

        return 0;
    }

    /**
     * @param string $attrCode
     * @param int|string $value
     *
     * @return bool
     * @throws InputException
     */
    public function validateBoolean($attrCode, $value)
    {
        if (!in_array($value, ['0', '1', 0, 1, true, false], true)) {
            throw new InputException(__('%1 invalid', $attrCode));
        }

        return true;
    }

    /**
     * @param string $date
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isValidDate($date)
    {
        if (!date_create($date)) {
            throw new InputException(__('Invalid date'));
        }

        return true;
    }

    /**
     * @param string $fileUpload
     * @param string $fileDb
     * @param string $attrCode
     *
     * @return bool
     * @throws InputException
     */
    public function validateFile($fileUpload, $fileDb, $attrCode)
    {
        $fileUploadDecode = $this->jsonDecodeData($fileUpload);
        $fileDbDecode     = $this->jsonDecodeData($fileDb);
        $fields           = ['file', 'name', 'size', 'url'];

        foreach ($fields as $field) {
            $fieldUpload = isset($fileUploadDecode[$field]) ? $fileUploadDecode[$field] : '';
            $fieldDb     = isset($fileDbDecode[$field]) ? $fileDbDecode[$field] : '';
            if ($field === 'size') {
                $fieldUpload = (int) $fieldUpload;
                $fieldDb     = (int) $fieldDb;
            }
            if (!$fieldDb || !$fieldUpload || ($fieldDb !== $fieldUpload)) {
                throw new InputException(
                    __('Something went wrong while uploading file (attribute %1)', $attrCode)
                );
            }
        }

        return true;
    }

    /**
     * @param string|int $storeId
     * @param array $attributeSubmit
     *
     * @return array
     */
    public function prepareAttributes($storeId, $attributeSubmit)
    {
        $attributes = $this->collectionFactory->create();
        $result     = [];
        $storeId    = (int) $storeId;
        foreach ($attributes->getItems() as $attribute) {
            $attrCode      = $attribute->getAttributeCode();
            $frontendInput = $attribute->getFrontendInput();

            if (!isset($attributeSubmit[$attrCode])) {
                continue;
            }

            if (!$attributeSubmit[$attrCode] && $attributeSubmit[$attrCode] !== '0') {
                $result[$attrCode] = '';
                continue;
            }

            $result[$attrCode . '_label'] = $this->prepareLabel($attribute, $storeId);

            $value             = $attributeSubmit[$attrCode];
            $result[$attrCode] = $value;
            switch ($frontendInput) {
                case 'boolean':
                    $result[$attrCode . '_option'] = $this->prepareBoolValue($value);
                    break;
                case 'select':
                case 'multiselect':
                case 'select_visual':
                case 'multiselect_visual':
                    $options = $this->prepareOptionValue($attribute->getOptions(), $value, $storeId);

                    $result[$attrCode . '_option'] = $options;
                    break;
                case 'date':
                    $result[$attrCode] = $this->prepareDateValue($value);
                    break;
                case 'image':
                case 'file':
                    $result[$attrCode . '_name'] = $this->prepareFileName($value);
                    $result[$attrCode . '_url']  = $this->prepareFileValue($frontendInput, $value);
                    break;
            }
        }

        return $result;
    }

    /**
     * @param $attribute
     * @param $storeId
     *
     * @return mixed
     */
    public function prepareLabel($attribute, $storeId)
    {
        $labels = $this->jsonDecodeData($attribute->getLabels());

        return !empty($labels[$storeId]) ? $labels[$storeId] : $attribute->getFrontendLabel();
    }

    /**
     * @param $quote
     * @param $attributeSubmit
     * @param $quoteAttribute
     *
     * @return array
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateAttributes($quote, $attributeSubmit, $quoteAttribute)
    {
        $attributes = $this->collectionFactory->create();
        $result     = [];
        $storeId    = $quote->getStoreId() ?: 0;
        foreach ($attributes->getItems() as $attribute) {
            if ($this->isVisible($attribute, $storeId, $quote->getCustomerGroupId())) {
                $attrCode      = $attribute->getAttributeCode();
                $frontendInput = $attribute->getFrontendInput();

                if (!isset($attributeSubmit[$attrCode])) {
                    continue;
                }

                if ($attributeSubmit[$attrCode] === '' && $attribute->getIsRequired()) {
                    throw new InputException(__('%1 is required'));
                }

                if (!$attributeSubmit[$attrCode] && $attributeSubmit[$attrCode] !== '0') {
                    $result[$attrCode] = '';
                    continue;
                }

                $result[$attrCode . '_label'] = $this->prepareLabel($attribute, $storeId);

                $value             = $attributeSubmit[$attrCode];
                $value             = is_array($value) ? implode(',', $value) : $value;
                $result[$attrCode] = $value;
                switch ($frontendInput) {
                    case 'boolean':
                        $this->validateBoolean($attrCode, $value);
                        $result[$attrCode . '_option'] = $this->prepareBoolValue($value);
                        break;
                    case 'select':
                    case 'multiselect':
                    case 'select_visual':
                    case 'multiselect_visual':
                        $options = $this->prepareOptionValue($attribute->getOptions(), $value, $storeId);
                        if ($this->getOptionsInvalid()) {
                            throw new InputException(
                                __('Invalid options %1. Details: %1 ', implode($this->getOptionsInvalid()), $attrCode)
                            );
                        }
                        $result[$attrCode . '_option'] = $options;

                        break;
                    case 'date':
                        if ($this->isValidDate($value)) {
                            $result[$attrCode] = $this->prepareDateValue($value);
                        }
                        break;
                    case 'image':
                    case 'file':
                        $this->validateFile($value, $quoteAttribute->getData($attrCode), $attrCode);
                        $result[$attrCode . '_name'] = $this->prepareFileName($value);
                        $result[$attrCode . '_url']  = $this->prepareFileValue($frontendInput, $value);
                        break;
                }
            }
        }

        if ($result) {
            $quoteAttribute->saveAttributeData($quote->getId(), $result);
        }

        return $result;
    }

    /**
     * Check the current page is OSC
     *
     * @return bool
     */
    public function isOscPage()
    {
        $moduleEnable = $this->isModuleOutputEnabled('Mageplaza_Osc');
        $isOscModule  = ($this->_request->getRouteName() === 'onestepcheckout');

        return $moduleEnable && $isOscModule;
    }

    /**
     * Get all shipping methods
     *
     * @return array
     */
    public function getShippingMethods()
    {
        $activeCarriers = $this->carrierConfig->getAllCarriers();
        $methods        = [];

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            if ($carrierCode === 'temando') {
                continue;
            }
            $options       = [];
            $carrierTitle  = '';
            $allowedMethod = $carrierModel->getAllowedMethods();
            if (is_array($allowedMethod)) {
                foreach ($allowedMethod as $methodCode => $method) {
                    $code      = $carrierCode . '_' . $methodCode;
                    $options[] = [
                        'value' => $code,
                        'label' => $method
                    ];
                }

                $carrierTitle = $carrierModel->getConfigData('title');
            }

            $methods[] = [
                'value' => $options,
                'label' => $carrierTitle
            ];
        }

        return $methods;
    }

    /**
     * @param AbstractModel $object
     * @param string $type
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function applyFilter(AbstractModel $object, $type = 'output')
    {
        $attributes = $this->getOrderAttributesCollection(
            null,
            null,
            false,
            [
                'input_filter' => ['neq' => 'NULL']
            ]
        );

        foreach ($attributes as $attribute) {
            $value = $object->getData($attribute->getAttributeCode());
            if ($value) {
                $filter = $this->getFilterClass($attribute->getInputFilter());
                if ($type === 'input') {
                    $value = $filter->inputFilter($value);
                } else {
                    $value = $filter->outputFilter($value);
                }

                $object->setData($attribute->getAttributeCode(), $value);
            }
        }
    }

    /**
     * Return Input/Output Filter Class
     *
     * @param $filterCode
     *
     * @return FilterInterface
     */
    protected function getFilterClass($filterCode)
    {
        $filterClass = 'Magento\Framework\Data\Form\Filter\\' . ucfirst($filterCode);

        return new $filterClass();
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return self::TEMPLATE_MEDIA_PATH . '/tmp';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseTmpMediaPath();
    }

    /**
     * @param string $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Move file from temporary directory into base directory
     *
     * @param $file
     *
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function moveTemporaryFile($file)
    {
        /** @var Filesystem $fileSystem */
        $fileSystem     = $this->getObject(Filesystem::class);
        $directoryRead  = $fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $directoryWrite = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);

        $path    = $this->getBaseTmpMediaPath() . $file['file'];
        $newName = Uploader::getNewFileName($directoryRead->getAbsolutePath($path));
        $newPath = self::TEMPLATE_MEDIA_PATH . Uploader::getDispretionPath($newName);

        if (!$directoryWrite->create($newPath)) {
            throw new LocalizedException(
                __('Unable to create directory %1.', $newPath)
            );
        }

        if (!$directoryWrite->isWritable($newPath)) {
            throw new LocalizedException(
                __('Destination folder is not writable or does not exists.')
            );
        }

        $directoryWrite->renameFile($path, $newPath . '/' . $newName);

        return Uploader::getDispretionPath($newName) . '/' . $newName;
    }

    /**
     * @param $value
     *
     * @return Phrase
     */
    public function prepareBoolValue($value)
    {
        return $value ? __('Yes') : __('No');
    }

    /**
     * @param $options
     * @param $values
     * @param $storeId
     *
     * @return string
     */
    public function prepareOptionValue($options, $values, $storeId)
    {
        $this->optionsInvalid = [];

        $options = $this->jsonDecodeData($options);
        $result  = [];

        switch (true) {
            case isset($options['option']['value']):
                $options = $options['option']['value'];
                break;
            case isset($options['optionvisual']['value']):
                $options = $options['optionvisual']['value'];
                break;
        }

        foreach (explode(',', $values) as $value) {
            if ($value && isset($options[$value])) {
                $option   = $options[$value];
                $result[] = !empty($option[$storeId]) ? $option[$storeId] : $option[0];
            } else {
                $this->optionsInvalid[] = $value;
            }
        }

        return implode(', ', $result);
    }

    /**
     * @return array
     */
    public function getOptionsInvalid()
    {
        return $this->optionsInvalid;
    }

    /**
     * @param $value
     *
     * @return string|null
     */
    public function prepareDateValue($value)
    {
        return $value ? date('M d, Y', strtotime($value)) : null;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function prepareFileName($value)
    {
        return substr($value, strrpos($value, '/') + 1);
    }

    /**
     * @param $frontendInput
     * @param $value
     *
     * @return string
     */
    public function prepareFileValue($frontendInput, $value)
    {
        $param = '/' . $frontendInput . '/' . $this->urlEncoder->encode($value);

        return $this->_urlBuilder->getUrl('mporderattributes/viewfile/index' . $param);
    }

    /**
     * @return bool|string
     */
    public function getTinymceConfig()
    {
        if ($this->versionCompare('2.3.0')) {
            $config = [
                'tinymce4' => [
                    'toolbar'     => 'formatselect | bold italic underline | alignleft aligncenter alignright | '
                        . 'bullist numlist | link table charmap',
                    'plugins'     => implode(
                        ' ',
                        [
                            'advlist',
                            'autolink',
                            'lists',
                            'link',
                            'charmap',
                            'media',
                            'noneditable',
                            'table',
                            'contextmenu',
                            'paste',
                            'code',
                            'help',
                            'table'
                        ]
                    ),
                    'content_css' => $this->repository->getUrl('mage/adminhtml/wysiwyg/tiny_mce/themes/ui.css')
                ]
            ];

            return $this->jsonEncodeData($config);
        }

        return false;
    }

    /**
     * @param Order|OrderInterface $order
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function addDataToOrder($order)
    {
        if ($this->isEnabled($order->getStoreId())) {
            $orderAttributeModel = $this->orderFactory->create();
            $orderAttributeModel->load($order->getId());
            if ($orderAttributeModel->getId()) {
                $result = $this->prepareAttributes($order->getStoreId(), $orderAttributeModel->getData());
                $order->addData($result);
            }
        }
    }
}
