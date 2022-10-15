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
use Magento\Catalog\Model\Config\Source\ProductPriceOptionsInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Config\Model\ResourceModel\Config\Data as ConfigValueResource;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager as ModuleManager;
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
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;

/**
 * Class TierPrice
 * @package Mageplaza\BetterTierPrice\Ui\DataProvider\Product\Form\Modifier
 */
class TierPrice extends AbstractModifier
{
    /**
     * @var ProductPriceOptions
     */
    private $productPriceOptions;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ModuleManager
     * @since 101.0.0
     */
    protected $moduleManager;

    /**
     * @var GroupSourceInterface
     */
    private $customerGroupSource;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var ConfigValue
     */
    protected $configValue;

    /**
     * @var ConfigValueResource
     */
    protected $configValueResource;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var array
     */
    private $meta;

    /**
     * @param ProductPriceOptions $productPriceOptions
     * @param ArrayManager $arrayManager
     * @param LocatorInterface $locator
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     * @param DirectoryHelper $directoryHelper
     * @param RequestInterface $request
     * @param ModuleManager $moduleManager
     * @param GroupSourceInterface $customerGroupSource
     * @param GroupManagementInterface $groupManagement
     * @param ConfigValue $configValue
     * @param ConfigValueResource $configValueResource
     * @param Data $helperData
     */
    public function __construct(
        ProductPriceOptions $productPriceOptions,
        ArrayManager $arrayManager,
        LocatorInterface $locator,
        UrlInterface $url,
        StoreManagerInterface $storeManager,
        DirectoryHelper $directoryHelper,
        RequestInterface $request,
        ModuleManager $moduleManager,
        GroupSourceInterface $customerGroupSource,
        GroupManagementInterface $groupManagement,
        ConfigValue $configValue,
        ConfigValueResource $configValueResource,
        Data $helperData
    ) {
        $this->productPriceOptions = $productPriceOptions;
        $this->arrayManager        = $arrayManager;
        $this->locator             = $locator;
        $this->url                 = $url;
        $this->storeManager        = $storeManager;
        $this->directoryHelper     = $directoryHelper;
        $this->request             = $request;
        $this->moduleManager       = $moduleManager;
        $this->customerGroupSource = $customerGroupSource;
        $this->groupManagement     = $groupManagement;
        $this->configValue         = $configValue;
        $this->configValueResource = $configValueResource;
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
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->helperData->isEnabled($this->getStore()->getId())) {
            return $meta;
        }
        $this->meta                             = $meta;
        $groupCode                              = 'product_details';
        $this->meta[$groupCode]                 = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label'         => __('Tier Price'),
                        'collapsible'   => false,
                        'dataScope'     => self::DATA_SCOPE_PRODUCT,
                        'sortOrder'     => 10,
                        'provider'      => 'mp_tier_price_update.tier_price_data_source',
                    ]
                ]
            ],
            'children'  => [
                'mp_specific_customer'    => $this->getSpecificCustomerStructure(),
                'tier_price'              => $this->getTierPriceStructure(),
                'container_mp_tier_group' => $this->getTierGroupStructure(),
            ],
        ];
        $tierPricePath                          = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_TIER_PRICE,
            $this->meta,
            null,
            'children'
        );
        $this->meta['mp_tier_group_modal']      = $this->getNewTierGroupModalStructure($tierPricePath);
        $this->meta['mp_tier_group_modal_edit'] = $this->getEditTierGroupModalStructure($tierPricePath);

        return $this->meta;
    }

    /**
     * @param $tierPricePath
     *
     * @return array
     */
    private function getEditTierGroupModalStructure($tierPricePath)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'sortOrder'              => 30,
                        'isTemplate'             => false,
                        'componentType'          => Modal::NAME,
                        'component'              => 'Mageplaza_BetterTierPrice/js/modal/modal-component',
                        'dataScope'              => '',
                        'provider'               => 'mp_tier_price_update.tier_price_data_source',
                        'onCancel'               => 'closeModal',
                        'tierGroupContainerName' =>
                            'mp_tier_price_update.mp_tier_price_update.product_details.container_mp_tier_group',
                        'options'                => [
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
                                'additionalClasses' => 'tier-group-container'
                            ],
                        ],
                    ],
                    'children'  => [
                        'mp_tier_group_index'       => [
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
                                        'validation'    => [
                                            'required-entry' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'mp_tier_group_name'        => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Text::NAME,
                                        'label'         => __('Group Name'),
                                        'dataScope'     => 'mp_tier_group_edit_name',
                                        'sortOrder'     => 10,
                                        'validation'    => [
                                            'required-entry' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'mp_tier_group_price_value' => $this->arrayManager->get($tierPricePath, $this->meta)
                    ],
                ],

            ]
        ];
    }

    /**
     * @param $tierPricePath
     *
     * @return array
     */
    private function getNewTierGroupModalStructure($tierPricePath)
    {
        $tierPrice                                           = $this->arrayManager->get($tierPricePath, $this->meta);
        $tierPrice['arguments']['data']['config']['isModal'] = true;

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'sortOrder'              => 20,
                        'isTemplate'             => false,
                        'componentType'          => Modal::NAME,
                        'component'              => 'Mageplaza_BetterTierPrice/js/modal/modal-component',
                        'dataScope'              => '',
                        'provider'               => 'mp_tier_price_update.tier_price_data_source',
                        'onCancel'               => 'closeModal',
                        'tierGroupContainerName' =>
                            'mp_tier_price_update.mp_tier_price_update.product_details.container_mp_tier_group',
                        'options'                => [
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
                                'additionalClasses' => 'tier-group-container'
                            ],
                        ],
                    ],
                    'children'  => [
                        'mp_tier_group_name'        => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Text::NAME,
                                        'label'         => __('Group Name'),
                                        'dataScope'     => 'mp_tier_group_name',
                                        'provider'      => 'mp_tier_price_update.tier_price_data_source',
                                        'sortOrder'     => 10,
                                        'validation'    => [
                                            'required-entry' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'mp_tier_group_price_value' => $tierPrice
                    ],
                ],

            ]
        ];
    }

    /**
     * @return array
     */
    protected function getTierGroupStructure()
    {
        $configValue = $this->configValue;
        $this->configValueResource->load($configValue, 'mp_tier_price/general/tier_group', 'path');
        $tierGroup          = $configValue->getValue() ? Data::jsonDecode($configValue->getValue())
            : [['label' => __('Please Select'), 'value' => ' ']];
        $tierGroup          = empty($tierGroup) ? [['label' => __('Please Select'), 'value' => ' ']] : $tierGroup;
        $tierGroup          = array_filter($tierGroup);
        $tierGroupStructure = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataType'      => Container::NAME,
                        'formElement'   => Container::NAME,
                        'componentType' => Container::NAME,
                        'sortOrder'     => 10,
                        'component'     => 'Magento_Ui/js/form/components/group',
                        'breakLine'     => false,
                        'label'         => __('Tier Group'),
                        'template'      => 'Mageplaza_BetterTierPrice/ui/group/group',
                    ]
                ]
            ],
            'children'  => [
                'mp_tier_group_data'    => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement'        => Select::NAME,
                                'componentType'      => Field::NAME,
                                'dataType'           => Text::NAME,
                                'disabled'           => true,
                                'dataScope'          => 'mp_tier_group_data',
                                'component'          => 'Mageplaza_BetterTierPrice/js/form/components/tier-group',
                                'updateTierGroupUrl' => $this->url->getUrl('mptierprice/product/savetiergroup'),
                                'options'            => $tierGroup,
                                'sortOrder'          => 1,
                                'tierPriceName'      =>
                                    'mp_tier_price_update.mp_tier_price_update.product_details.tier_price',
                                'editModalContainer' =>
                                    'mp_tier_price_update.mp_tier_price_update.mp_tier_group_modal_edit.mp_tier_group_modal_container'
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
                                        'targetName' => 'mp_tier_price_update.mp_tier_price_update.mp_tier_group_modal',
                                        'actionName' => 'toggleModal',
                                    ]
                                ],
                                'buttonClasses'      => 'action-primary',
                                'title'              => __('Add New'),
                                'additionalForGroup' => true,
                                'disabled'           => true,
                                'additionalClasses'  => 'admin__field-small',
                                'provider'           => 'mp_tier_price_update.tier_price_data_source',
                                'source'             => 'product_details',
                                'sortOrder'          => 3,
                                'displayArea'        => 'insideGroup'
                            ],
                        ]
                    ]
                ],
                'mp_edit_tier_group'    => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement'        => Container::NAME,
                                'componentType'      => Container::NAME,
                                'component'          => 'Magento_Ui/js/form/components/button',
                                'template'           => 'ui/form/components/button/container',
                                'actions'            => [
                                    [
                                        'targetName' =>
                                            'mp_tier_price_update.mp_tier_price_update.mp_tier_group_modal_edit',
                                        'actionName' => 'toggleModal',
                                    ]
                                ],
                                'title'              => __('Edit'),
                                'additionalClasses'  => 'admin__field-small',
                                'additionalForGroup' => true,
                                'provider'           => 'mp_tier_price_update.tier_price_data_source',
                                'source'             => 'product_details',
                                'sortOrder'          => 2,
                                'displayArea'        => 'insideGroup'
                            ],
                        ]
                    ]
                ]
            ]
        ];

        return $tierGroupStructure;
    }

    /**
     * Check tier_price attribute scope is global
     *
     * @return bool
     */
    protected function isScopeGlobal()
    {
        return false;
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
     * Get websites list
     *
     * @return array
     * @throws NoSuchEntityException
     */
    protected function getWebsites()
    {
        $websites = [
            [
                'label' => __('All Websites') . ' [' . $this->directoryHelper->getBaseCurrencyCode() . ']',
                'value' => 0,
            ]
        ];

        if (!$this->isScopeGlobal() && $this->storeManager->getStore()->getId()) {
            /** @var Website $website */
            $website = $this->getStore()->getWebsite();

            if ($website->getId()) {
                $websites[] = [
                    'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                    'value' => $website->getId(),
                ];
            }
        } elseif (!$this->isScopeGlobal()) {
            $websitesList = $this->storeManager->getWebsites();
            foreach ($websitesList as $website) {
                /** @var Website $website */
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
            return $this->getStore()->getWebsiteId();
        }

        return 0;
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     * @throws NoSuchEntityException
     */
    protected function isAllowChangeWebsite()
    {
        return !(
            !$this->isShowWebsiteColumn()
            || $this->getStore()->getId()
        );
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
     * Get tier price dynamic rows structure
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getTierPriceStructure()
    {
        $priceTypeOptions = $this->productPriceOptions->toOptionArray();
        $firstOption      = $priceTypeOptions ? current($priceTypeOptions) : null;

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'       => 'dynamicRows',
                        'component'           => 'Magento_Catalog/js/components/dynamic-rows-tier-price',
                        'label'               => __('Customer Group Price'),
                        'renderDefaultRecord' => false,
                        'recordTemplate'      => 'record',
                        'dataScope'           => '',
                        'dndConfig'           => [
                            'enabled' => false,
                        ],
                        'disabled'            => false,
                        'required'            => false,
                        'sortOrder'           => 20,
                        'additionalClasses'   => 'mp-specific-customer',
                        'provider'            => 'mp_tier_price_update.tier_price_data_source',
                        'template'            => 'Mageplaza_BetterTierPrice/ui/dynamic-rows/templates/default',
                        'isSpecificCustomers' => false,
                        'isModal'             => false
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
                                        'provider'      => 'mp_tier_price_update.tier_price_data_source',

                                    ],
                                ],
                            ],
                        ],
                        'cust_group'   => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement'   => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType'      => Text::NAME,
                                        'dataScope'     => 'cust_group',
                                        'label'         => __('Customer Group'),
                                        'options'       => $this->getCustomerGroups(),
                                        'value'         => $this->getDefaultCustomerGroup(),
                                        'sortOrder'     => 20,
                                        'provider'      => 'mp_tier_price_update.tier_price_data_source',

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
                                            'validate-digits'            => true,
                                        ],
                                        'provider'      => 'mp_tier_price_update.tier_price_data_source',
                                        'template'      => 'Mageplaza_BetterTierPrice/ui/form/element/input-require',
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
                                        'provider'          => 'mp_tier_price_update.tier_price_data_source'
                                    ],
                                ],
                            ],
                            'children'  => [
                                ProductAttributeInterface::CODE_TIER_PRICE_FIELD_VALUE_TYPE       => [
                                    'arguments' => [
                                        'data' => [
                                            'options' => $priceTypeOptions,
                                            'config'  => [
                                                'componentType' => Field::NAME,
                                                'formElement'   => Select::NAME,
                                                'dataType'      => 'text',
                                                'component'     => 'Magento_Catalog/js/tier-price/value-type-select',
                                                'provider'      => 'mp_tier_price_update.tier_price_data_source',
                                                'prices'        => [
                                                    ProductPriceOptionsInterface::VALUE_FIXED   => '${ $.parentName }.'
                                                        . ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE,
                                                    ProductPriceOptionsInterface::VALUE_PERCENT => '${ $.parentName }.'
                                                        . ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PERCENTAGE_VALUE,
                                                    ProductPriceOptions::DISCOUNT_FIXED         => '${ $.parentName }.'
                                                        . ProductPriceOptions::DISCOUNT_FIXED
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE            => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'provider' => 'mp_tier_price_update.tier_price_data_source',

                                                'componentType' => Field::NAME,
                                                'formElement'   => Input::NAME,
                                                'dataType'      => Price::NAME,
                                                'label'         => __('Price'),
                                                'enableLabel'   => true,
                                                'dataScope'     => 'price',
                                                'addbefore'     => $this->getStore()
                                                    ->getBaseCurrency()
                                                    ->getCurrencySymbol(),
                                                'sortOrder'     => 40,
                                                'validation'    => [
                                                    'required-entry'             => true,
                                                    'validate-greater-than-zero' => true,
                                                    'validate-number'            => true,
                                                ],
                                                'imports'       => [
                                                    'priceValue' => '${ $.provider }:data.product.price',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                                ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PERCENTAGE_VALUE => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'provider' => 'mp_tier_price_update.tier_price_data_source',

                                                'componentType' => Field::NAME,
                                                'formElement'   => Input::NAME,
                                                'dataType'      => Price::NAME,
                                                'addbefore'     => '%',
                                                'validation'    => [
                                                    'validate-number'     => true,
                                                    'less-than-equals-to' => 100
                                                ],
                                                'visible'       => $firstOption && $firstOption['value']
                                                    === ProductPriceOptionsInterface::VALUE_PERCENT,
                                            ],
                                        ],
                                    ],
                                ],
                                ProductPriceOptions::DISCOUNT_FIXED                               => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'provider' => 'mp_tier_price_update.tier_price_data_source',

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
                                'price_calc'                                                      => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'provider' => 'mp_tier_price_update.tier_price_data_source',

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
                                        'provider'      => 'mp_tier_price_update.tier_price_data_source',

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
     * Retrieve allowed customer groups
     *
     * @return array
     */
    private function getCustomerGroups()
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }

        return $this->customerGroupSource->toOptionArray();
    }

    /**
     * Retrieve default value for customer group
     *
     * @return int
     * @throws LocalizedException
     */
    private function getDefaultCustomerGroup()
    {
        return $this->groupManagement->getAllCustomersGroup()->getId();
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
                        'sortOrder'           => 45,
                        'provider'            => 'mp_tier_price_update.tier_price_data_source',
                        'template'            => 'Mageplaza_BetterTierPrice/ui/dynamic-rows/templates/default',
                        'isSpecificCustomers' => true,
                        'isModal'             => false
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
                                        'sortOrder'     => 10,
                                        'disabled'      =>
                                            $this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite(),
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
                                        'sortOrder'         => 20,
                                        'readonly'          => true,
                                        'additionalClasses' => 'mp-specific-customer-customer',
                                        'component'         =>
                                            'Mageplaza_BetterTierPrice/js/form/components/specific-customer',
                                        'template'          =>
                                            'Mageplaza_BetterTierPrice/ui/form/components/specific-customer',
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
                                        'template'      => 'Mageplaza_BetterTierPrice/ui/form/element/input-require',
                                        'sortOrder'     => 30,
                                        'validation'    => [
                                            'required-entry'             => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-digits'            => true,
                                        ],
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
                                'value_type'                                                      => [
                                    'arguments' => [
                                        'data' => [
                                            'options' => $priceTypeOptions,
                                            'config'  => [
                                                'provider'      => 'mp_tier_price_update.tier_price_data_source',
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
                                ProductAttributeInterface::CODE_TIER_PRICE_FIELD_PRICE            => [
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
                                ProductPriceOptions::DISCOUNT_FIXED                               => [
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
                                'price_calc'                                                      => [
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
     * Retrieve store
     *
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    protected function getStore()
    {
        return $this->storeManager->getStore($this->request->getParam('store') ?: 0);
    }
}
