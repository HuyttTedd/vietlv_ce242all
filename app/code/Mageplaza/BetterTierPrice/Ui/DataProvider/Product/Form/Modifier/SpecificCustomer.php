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
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Config\Model\ResourceModel\Config\Data as ConfigValueResource;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;

/**
 * Class SpecificCustomer
 * @package Mageplaza\BetterTierPrice\Ui\DataProvider\Product\Form\Modifier
 */
class SpecificCustomer extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @type array
     */
    protected $_meta;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var ConfigValue
     */
    protected $configValue;

    /**
     * @var ConfigValueResource
     */
    protected $configValueResource;

    /**
     * @var ProductPriceOptions
     */
    private $productPriceOptions;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * SpecificCustomer constructor.
     *
     * @param DirectoryHelper $directoryHelper
     * @param StoreManagerInterface $storeManager
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param RequestInterface $request
     * @param UrlInterface $url
     * @param ConfigValue $configValue
     * @param ConfigValueResource $configValueResource
     * @param ProductPriceOptions $productPriceOptions
     * @param Data $helperData
     */
    public function __construct(
        DirectoryHelper $directoryHelper,
        StoreManagerInterface $storeManager,
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        RequestInterface $request,
        UrlInterface $url,
        ConfigValue $configValue,
        ConfigValueResource $configValueResource,
        ProductPriceOptions $productPriceOptions,
        Data $helperData
    ) {
        $this->locator             = $locator;
        $this->arrayManager        = $arrayManager;
        $this->request             = $request;
        $this->storeManager        = $storeManager;
        $this->directoryHelper     = $directoryHelper;
        $this->url                 = $url;
        $this->configValue         = $configValue;
        $this->configValueResource = $configValueResource;
        $this->productPriceOptions = $productPriceOptions;
        $this->helperData          = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param array $meta
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta)
    {
        $this->_meta   = $meta;
        $tierPricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE,
            $this->_meta,
            null,
            'children'
        );
        if ($tierPricePath && $this->helperData->isEnabled($this->getStore()->getId())) {
            $this->customizeSpecificCustomerField();
            $this->customizeDiscountFixed();
            $this->customizeTierGroup();
        } else {
            $fieldPath   = $this->arrayManager->findPath('container_mp_specific_customer', $this->_meta);
            $this->_meta = $this->arrayManager->remove($fieldPath, $this->_meta);
            $fieldPath   = $this->arrayManager->findPath('container_mp_tier_group', $this->_meta);
            $this->_meta = $this->arrayManager->remove($fieldPath, $this->_meta);
        }

        return $this->_meta;
    }

    /**
     * Check tier_price attribute scope is global
     *
     * @return bool
     */
    protected function isScopeGlobal()
    {
        return $this->locator->getProduct()
            ->getResource()
            ->getAttribute('mp_specific_customer')
            ->isScopeGlobal();
    }

    /**
     * Get websites list
     *
     * @return array
     */
    protected function getWebsites()
    {
        $websites = [
            [
                'label' => __('All Websites') . ' [' . $this->directoryHelper->getBaseCurrencyCode() . ']',
                'value' => 0,
            ]
        ];
        /** @var Product $product */
        $product = $this->locator->getProduct();

        if (!$this->isScopeGlobal() && $product->getStoreId()) {
            /** @var Website $website */
            $website = $this->getStore()->getWebsite();

            $websites[] = [
                'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                'value' => $website->getId(),
            ];
        } elseif (!$this->isScopeGlobal()) {
            $websitesList      = $this->storeManager->getWebsites();
            $productWebsiteIds = $product->getWebsiteIds();
            foreach ($websitesList as $website) {
                /** @var Website $website */
                if (!in_array($website->getId(), $productWebsiteIds, false)) {
                    continue;
                }
                $websites[] = [
                    'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                    'value' => $website->getId(),
                ];
            }
        }

        return $websites;
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getDefaultWebsite()
    {
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return $this->storeManager->getStore($this->locator->getProduct()->getStoreId())->getWebsiteId();
        }

        return 0;
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     */
    protected function isAllowChangeWebsite()
    {
        return !(!$this->isShowWebsiteColumn() || $this->locator->getProduct()->getStoreId());
    }

    /**
     * Show group prices grid website column
     *
     * @return bool
     */
    protected function isShowWebsiteColumn()
    {
        return !($this->isScopeGlobal() || $this->storeManager->isSingleStoreMode());
    }

    /**
     * Show website column and switcher for group price table
     *
     * @return bool
     */
    protected function isMultiWebsites()
    {
        return !$this->storeManager->isSingleStoreMode();
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getSpecificCustomerStructure()
    {
        $priceTypeOptions = $this->productPriceOptions->toOptionArray();
        $firstOption      = $priceTypeOptions ? current($priceTypeOptions) : null;

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'       => 'dynamicRows',
                        'label'               => __('Tier Price for Specific Customer(s)'),
                        'renderDefaultRecord' => false,
                        'recordTemplate'      => 'record',
                        'dataScope'           => '',
                        'dndConfig'           => [
                            'enabled' => false,
                        ],
                        'additionalClasses'   => 'mp-specific-customer',
                        'disabled'            => false,
                        'sortOrder'           => 55,
                    ],
                ],
            ],
            'children'  => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate'    => true,
                                'is_collection' => true,
                                'component'     => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope'     => '',
                            ],
                        ],
                    ],
                    'children'  => [
                        'website_id'   => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType'      => Text::NAME,
                                        'formElement'   => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope'     => 'website_id',
                                        'label'         => __('Website'),
                                        'options'       => $this->getWebsites(),
                                        'value'         => $this->getDefaultWebsite(),
                                        'visible'       => $this->isMultiWebsites(),
                                        'disabled'      =>
                                            $this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite(),
                                        'sortOrder'     => 10,
                                    ],
                                ],
                            ],
                        ],
                        'customer_id'  => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'       => Input::NAME,
                                        'componentType'     => Field::NAME,
                                        'dataType'          => Text::NAME,
                                        'dataScope'         => 'customer_id',
                                        'additionalClasses' => 'mp-specific-customer-id',
                                        'label'             => __('Customer Id'),
                                        'sortOrder'         => 15,
                                        'visible'           => false
                                    ],
                                ],
                            ],
                        ],
                        'customer'     => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'       => Input::NAME,
                                        'componentType'     => Field::NAME,
                                        'dataType'          => Text::NAME,
                                        'dataScope'         => 'customer',
                                        'label'             => __('Customer'),
                                        'customersGridUrl'  => $this->url->getUrl('mptierprice/product/customersgrid'),
                                        'additionalClasses' => 'mp-specific-customer-customer',
                                        'component'         =>
                                            'Mageplaza_BetterTierPrice/js/form/components/specific-customer',
                                        'template'          =>
                                            'Mageplaza_BetterTierPrice/ui/form/components/specific-customer',
                                        'sortOrder'         => 20
                                    ],
                                ],
                            ],
                        ],
                        'price_qty'    => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Number::NAME,
                                        'label'         => __('Quantity'),
                                        'dataScope'     => 'price_qty',
                                        'sortOrder'     => 30,
                                        'validation'    => [
                                            'required-entry'             => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-digits'            => true
                                        ]
                                    ],
                                ],
                            ],
                        ],
                        'price_value'  => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType'     => Container::NAME,
                                        'formElement'       => Container::NAME,
                                        'dataType'          => Price::NAME,
                                        'component'         => 'Magento_Ui/js/form/components/group',
                                        'label'             => __('Price'),
                                        'enableLabel'       => true,
                                        'dataScope'         => '',
                                        'additionalClasses' => 'control-grouped',
                                        'sortOrder'         => 40,
                                    ],
                                ],
                            ],
                            'children'  => [
                                'value_type' => [
                                    'arguments' => [
                                        'data' => [
                                            'options' => $priceTypeOptions,
                                            'config'  => [
                                                'componentType' => Field::NAME,
                                                'formElement'   => Select::NAME,
                                                'dataType'      => 'text',
                                                'component'     => 'Magento_Catalog/js/tier-price/value-type-select',
                                                'prices'        => [
                                                    ProductPriceOptions::VALUE_FIXED    => '${ $.parentName }.'
                                                        . ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE,
                                                    ProductPriceOptions::VALUE_PERCENT  => '${ $.parentName }.'
                                                        . ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PERCENTAGE_VALUE,
                                                    ProductPriceOptions::DISCOUNT_FIXED => '${ $.parentName }.'
                                                        . ProductPriceOptions::DISCOUNT_FIXED
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'componentType' => Field::NAME,
                                                'formElement'   => Input::NAME,
                                                'dataType'      => Price::NAME,
                                                'addbefore'     => $this->getStore()
                                                    ->getBaseCurrency()
                                                    ->getCurrencySymbol(),
                                                'validation'    => [
                                                    'validate-number'          => true,
                                                    'validate-zero-or-greater' => true
                                                ],
                                                'visible'       => $firstOption
                                                    && $firstOption['value'] === ProductPriceOptions::VALUE_FIXED,
                                            ],
                                        ],
                                    ],
                                ],
                                ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PERCENTAGE_VALUE => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'componentType' => Field::NAME,
                                                'formElement'   => Input::NAME,
                                                'dataType'      => Price::NAME,
                                                'addbefore'     => '%',
                                                'validation'    => [
                                                    'validate-number'     => true,
                                                    'less-than-equals-to' => 100
                                                ],
                                                'visible'       => $firstOption
                                                    && $firstOption['value'] === ProductPriceOptions::VALUE_PERCENT,
                                            ],
                                        ],
                                    ],
                                ],
                                ProductPriceOptions::DISCOUNT_FIXED => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'componentType' => Field::NAME,
                                                'formElement'   => Input::NAME,
                                                'dataType'      => Price::NAME,
                                                'addbefore'     => $this->getStore()
                                                    ->getBaseCurrency()
                                                    ->getCurrencySymbol(),
                                                'validation'    => [
                                                    'validate-number' => true,
                                                ],
                                                'visible'       => $firstOption
                                                    && $firstOption['value'] === ProductPriceOptions::DISCOUNT_FIXED,
                                            ],
                                        ],
                                    ],
                                ],
                                'price_calc' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'componentType' => Container::NAME,
                                                'component'     => 'Magento_Catalog/js/tier-price/percentage-processor',
                                                'visible'       => false
                                            ],
                                        ],
                                    ]
                                ]
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType'      => Text::NAME,
                                        'label'         => '',
                                        'sortOrder'     => 50,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Customize Discount Fixed field
     *
     * @return $this
     */
    protected function customizeDiscountFixed()
    {
        $tierPricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE,
            $this->_meta,
            null,
            'children'
        );
        if (!$tierPricePath) {
            return $this;
        }

        $pricePath = $this->arrayManager->findPath(
            'price_value',
            $this->_meta,
            $tierPricePath
        );

        $this->_meta = $this->arrayManager->merge(
            $this->arrayManager->slicePath($pricePath, 0, -1),
            $this->_meta,
            $this->getUpdatedTierPriceStructure()
        );

        return $this;
    }

    /**
     * @return array
     */
    private function getUpdatedTierPriceStructure()
    {
        $priceTypeOptions = $this->productPriceOptions->toOptionArray();
        $firstOption      = $priceTypeOptions ? current($priceTypeOptions) : null;

        return [
            'price_value' => [
                'children' => [
                    'value_type' => [
                        'arguments' => [
                            'data' => [
                                'options' => $priceTypeOptions,
                                'config'  => [
                                    'prices' => [
                                        ProductPriceOptions::DISCOUNT_FIXED => '${ $.parentName }.'
                                            . ProductPriceOptions::DISCOUNT_FIXED
                                    ],
                                ],
                            ],
                        ],
                    ],
                    ProductPriceOptions::DISCOUNT_FIXED => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'name'          => '${ $.parentName }.mp_discount_fixed',
                                    'componentType' => Field::NAME,
                                    'formElement'   => Input::NAME,
                                    'dataType'      => Price::NAME,
                                    'addbefore'     => $this->getStore()
                                        ->getBaseCurrency()
                                        ->getCurrencySymbol(),
                                    'validation'    => [
                                        'validate-number' => true,
                                    ],
                                    'visible'       => $firstOption
                                        && $firstOption['value'] === ProductPriceOptions::DISCOUNT_FIXED,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Customize Tier price for specific customer field
     *
     * @return $this
     * @throws NoSuchEntityException
     */
    protected function customizeSpecificCustomerField()
    {
        $fieldPath = $this->arrayManager->findPath('mp_specific_customer', $this->_meta, null, 'children');
        if (!$fieldPath) {
            return $this;
        }

        $this->_meta           = $this->arrayManager->merge(
            $fieldPath,
            $this->_meta,
            $this->getSpecificCustomerStructure()
        );
        $specificContainerPath = $this->arrayManager->findPath(
            'container_mp_specific_customer',
            $this->_meta,
            null,
            'children'
        );
        $sortPath              = $this->arrayManager->findPath('sortOrder', $this->_meta, $specificContainerPath);
        $this->_meta           = $this->arrayManager->replace(
            $sortPath,
            $this->_meta,
            $this->getTierPriceSortOrder() + 5
        );

        return $this;
    }

    /**
     * Customize Tier Group field
     *
     * @return $this
     */
    protected function customizeTierGroup()
    {
        $fieldPath = $this->arrayManager->findPath('container_mp_tier_group', $this->_meta);
        if (!$fieldPath) {
            return $this;
        }

        $tierPricePath                      = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE,
            $this->_meta,
            null,
            'children'
        );
        $this->_meta                        = $this->arrayManager->replace(
            $fieldPath,
            $this->_meta,
            $this->getTierGroupStructure($fieldPath)
        );
        $this->_meta['mp_tier_group_modal'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'isTemplate'    => false,
                        'componentType' => Modal::NAME,
                        'component'     => 'Mageplaza_BetterTierPrice/js/modal/modal-component',
                        'dataScope'     => '',
                        'provider'      => 'product_form.product_form_data_source',
                        'onCancel'      => 'closeModal',
                        'options'       => [
                            'title'   => __('New Tier Group'),
                            'buttons' => [
                                [
                                    'text'    => __('Add'),
                                    'class'   => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => '${ $.name }',
                                            'actionName' => 'actionDone'
                                        ],
                                    ]
                                ],
                            ],
                        ],
                    ]
                ]
            ],
            'children' => [
                'mp_tier_group_modal_container' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'     => 'fieldset',
                                'label'             => '',
                                'collapsible'       => false,
                                'dataScope'         => 'data.product',
                                'opened'            => true,
                                'additionalClasses' => 'tier-group-container mp-specific-customer'
                            ],
                        ],
                    ],
                    'children'  => [
                        'mp_tier_group_name' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Text::NAME,
                                        'label'         => __('Group Name'),
                                        'dataScope'     => 'mp_tier_group_name',
                                        'sortOrder'     => 30,
                                        'validation'    => [
                                            'required-entry' => true,
                                        ],
                                        'disabled'      => true,
                                    ],
                                ],
                            ],
                        ],
                        'mp_tier_group_price_value' => $this->arrayManager->get($tierPricePath, $this->_meta)
                    ],
                ],
            ]
        ];
        $this->_meta['mp_tier_group_modal_edit'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'isTemplate'    => false,
                        'componentType' => Modal::NAME,
                        'component'     => 'Mageplaza_BetterTierPrice/js/modal/modal-component',
                        'dataScope'     => '',
                        'provider'      => 'product_form.product_form_data_source',
                        'onCancel'      => 'closeModal',
                        'options'       => [
                            'title'   => __('Edit Tier Group'),
                            'buttons' => [
                                [
                                    'text'    => __('Save'),
                                    'class'   => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => '${ $.name }',
                                            'actionName' => 'actionDone'
                                        ],
                                    ]
                                ],
                                [
                                    'text'    => __('Delete'),
                                    'class'   => 'action-secondary',
                                    'actions' => [
                                        [
                                            'targetName' => '${ $.name }',
                                            'actionName' => 'actionDelete'
                                        ],
                                    ]
                                ],
                            ],
                        ],
                    ]
                ]
            ],
            'children'  => [
                'mp_tier_group_modal_container' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'     => 'fieldset',
                                'label'             => '',
                                'collapsible'       => false,
                                'dataScope'         => 'data.product',
                                'opened'            => true,
                                'additionalClasses' => 'tier-group-container mp-specific-customer'
                            ],
                        ],
                    ],
                    'children'  => [
                        'mp_tier_group_index' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Hidden::NAME,
                                        'visible'       => false,
                                        'label'         => __('Group Name'),
                                        'dataScope'     => 'mp_tier_group_edit_index',
                                        'sortOrder'     => 30,
                                    ],
                                ],
                            ],
                        ],
                        'mp_tier_group_name' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Text::NAME,
                                        'label'         => __('Group Name'),
                                        'dataScope'     => 'mp_tier_group_edit_name',
                                        'sortOrder'     => 30,
                                        'validation'    => [
                                            'required-entry' => true,
                                        ],
                                        'disabled'      => true,
                                    ],
                                ],
                            ],
                        ],
                        'mp_tier_group_price_value' => $this->arrayManager->get($tierPricePath, $this->_meta) ?: []
                    ],
                ],

            ]
        ];

        return $this;
    }

    /**
     * @return int|string
     */
    private function getTierPriceSortOrder()
    {
        $tierPricePath          = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE,
            $this->_meta,
            null,
            'children'
        );
        $tierPriceSortOrderPath = $this->arrayManager->findPath('sortOrder', $this->_meta, $tierPricePath);

        return $this->arrayManager->get($tierPriceSortOrderPath, $this->_meta);
    }

    /**
     * @param string $fieldPath
     *
     * @return mixed|null
     */
    protected function getTierGroupStructure($fieldPath)
    {
        $tierPriceSortOrder                                                     = $this->getTierPriceSortOrder();
        $tierGroupStructure                                                     = $this->arrayManager->get(
            $fieldPath,
            $this->_meta
        );
        $tierGroupStructure['arguments']['data']['config']['dataType']          = Container::NAME;
        $tierGroupStructure['arguments']['data']['config']['formElement']       = Container::NAME;
        $tierGroupStructure['arguments']['data']['config']['sortOrder']         = $tierPriceSortOrder - 5;
        $tierGroupStructure['arguments']['data']['config']['component']         = 'Magento_Ui/js/form/components/group';
        $tierGroupStructure['arguments']['data']['config']['breakLine']         = false;
        $tierGroupStructure['arguments']['data']['config']['additionalClasses'] = 'mp-specific-customer';
        $tierGroupStructure['arguments']['data']['config']['label']             = __('Tier Group');
        $configValue                                                            = $this->configValue;
        $this->configValueResource->load($configValue, 'mp_tier_price/general/tier_group', 'path');

        $tierGroup = $configValue->getValue() ? Data::jsonDecode($configValue->getValue()) : [
            [
                'label' => __('Please Select'),
                'value' => ' '
            ]
        ];
        $tierGroup = array_filter($tierGroup);
        $tierGroupStructure['children'] = [
            'mp_tier_group_data' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'        => Select::NAME,
                            'componentType'      => Field::NAME,
                            'dataType'           => Text::NAME,
                            'disableLabel'       => true,
                            'component'          => 'Mageplaza_BetterTierPrice/js/form/components/tier-group',
                            'updateTierGroupUrl' => $this->url->getUrl('mptierprice/product/savetiergroup'),
                            'options'            => $tierGroup,
                            'value'              => '',
                            'sortOrder'          => 1,
                        ],
                    ]
                ]
            ],
            'mp_add_new_tier_group' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'        => Container::NAME,
                            'componentType'      => Container::NAME,
                            'component'          => 'Magento_Ui/js/form/components/button',
                            'template'           => 'ui/form/components/button/container',
                            'actions'            => [
                                [
                                    'targetName' => 'product_form.product_form.mp_tier_group_modal',
                                    'actionName' => 'toggleModal',
                                ]
                            ],
                            'buttonClasses'      => 'action-primary',
                            'title'              => __('Add New'),
                            'additionalForGroup' => true,
                            'additionalClasses'  => 'admin__field-small',
                            'provider'           => false,
                            'source'             => 'product_details',
                            'sortOrder'          => 3,
                            'displayArea'        => 'insideGroup'
                        ],
                    ]
                ]
            ],
            'mp_edit_tier_group' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement'        => Container::NAME,
                            'componentType'      => Container::NAME,
                            'component'          => 'Magento_Ui/js/form/components/button',
                            'template'           => 'ui/form/components/button/container',
                            'actions'            => [
                                [
                                    'targetName' => 'product_form.product_form.mp_tier_group_modal_edit',
                                    'actionName' => 'toggleModal',
                                ]
                            ],
                            'title'              => __('Edit'),
                            'additionalClasses'  => 'admin__field-small',
                            'additionalForGroup' => true,
                            'provider'           => false,
                            'source'             => 'product_details',
                            'sortOrder'          => 2,
                            'displayArea'        => 'insideGroup'

                        ],
                    ]
                ]
            ]
        ];

        return $tierGroupStructure;
    }

    /**
     * Retrieve store
     *
     * @return StoreInterface
     */
    protected function getStore()
    {
        return $this->locator->getStore();
    }
}
