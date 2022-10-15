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

namespace Mageplaza\OrderAttributes\Test\Unit\Block\Checkout;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Media;
use Mageplaza\OrderAttributes\Block\Checkout\LayoutProcessor;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Config\Source\Position;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class AbstractLayoutProcessorTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Checkout
 */
abstract class AbstractLayoutProcessorTest extends TestCase
{
    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperDataMock;

    /**
     * @var Media|PHPUnit_Framework_MockObject_MockObject
     */
    protected $swatchHelperMock;

    /**
     * @var Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var StoreManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var LayoutProcessor
     */
    protected $layoutProcessorBock;

    /**
     * @var array
     */
    protected $fieldData = [];

    protected function setUp()
    {
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->swatchHelperMock = $this->getMockBuilder(Media::class)
            ->disableOriginalConstructor()->getMock();
        $this->customerSessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->layoutProcessorBock = new LayoutProcessor(
            $this->helperDataMock,
            $this->swatchHelperMock,
            $this->customerSessionMock,
            $this->storeManagerMock
        );
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function testProcessWithDisableModule()
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(false);
        $jsLayout = [];
        $this->assertEquals($jsLayout, $this->layoutProcessorBock->process($jsLayout));
    }

    /**
     * @return array
     */
    public function getOscSummaryFieldset()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'sidebar' => [
                            'children' => [
                                'place-order-information-left' => [
                                    'children' => [
                                        'addition-information' => [
                                            'children' => [
                                                'mpOrderAttributes' => [
                                                    'children' => [
                                                        'my_attribute' => '1'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getCheckoutSummaryFieldset()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'sidebar' => [
                            'children' => [
                                'summary' => [
                                    'children' => [
                                        'itemsAfter' => [
                                            'children' => [
                                                'mpOrderAttributes' => [
                                                    'children' => [
                                                        'my_attribute' => '1'
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPaymentBottomFieldset()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'afterMethods' => [
                                                    'children' => [
                                                        'mpOrderAttributes' => [
                                                            'children' => [
                                                                'my_attribute' => 1
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPaymentTopFieldset()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'billing-step' => [
                                    'children' => [
                                        'payment' => [
                                            'children' => [
                                                'beforeMethods' => [
                                                    'children' => [
                                                        'mpOrderAttributes' => [
                                                            'children' => [
                                                                'my_attribute' => 1
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getShippingBottomFieldSet()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'mpOrderAttributes' => [
                                                    'children' => [
                                                        'my_attribute' => 1
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getShippingTopFieldSet()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'before-shipping-method-form' => [
                                                    'children' => [
                                                        'mpOrderAttributes' => [
                                                            'children' => [
                                                                'my_attribute' => 1
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getNewAddressFieldSet()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'shipping-address-fieldset' => [
                                                    'children' => [
                                                        'mpOrderAttributes' => [
                                                            'children' => [
                                                                'my_attribute' => 1
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getCustomerHasAddressFieldSet()
    {
        return [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => [
                                                'before-form' => [
                                                    'children' => [
                                                        'mpOrderAttributes' => [
                                                            'children' => [
                                                                'my_attribute' => 1
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return [
            'mpShippingAddressAttributes' => [
                'position' => Position::ADDRESS,
                'fieldset' => $this->getNewAddressFieldSet(),
                'isOsc' => false
            ],
            'mpShippingAddressNewAttributes' => [
                'position' => Position::ADDRESS,
                'fieldset' => $this->getCustomerHasAddressFieldSet(),
                'addresses' => [['first_name' => 'test']],
                'isOsc' => true
            ],
            'mpShippingMethodTopAttributes' => [
                'position' => Position::SHIPPING_TOP,
                'fieldset' => $this->getShippingTopFieldSet(),
                'isOsc' => false
            ],
            'mpShippingMethodBottomAttributes' => [
                'position' => Position::SHIPPING_BOTTOM,
                'fieldset' => $this->getShippingBottomFieldSet(),
                'isOsc' => false
            ],
            'mpPaymentMethodTopAttributes' => [
                'position' => Position::PAYMENT_TOP,
                'fieldset' => $this->getPaymentTopFieldset(),
                'isOsc' => false
            ],
            'mpPaymentMethodBottomAttributes' => [
                'position' => Position::PAYMENT_BOTTOM,
                'fieldset' => $this->getPaymentBottomFieldset(),
                'isOsc' => false
            ],
            'mpOrderSummaryOscAttributes' => [
                'position' => Position::ORDER_SUMMARY,
                'fieldset' => $this->getOscSummaryFieldset(),
                'isOsc' => true
            ],
            'mpOrderSummaryAttributes' => [
                'position' => Position::ORDER_SUMMARY,
                'fieldset' => $this->getCheckoutSummaryFieldset(),
                'isOsc' => false
            ],
        ];
    }

    /**
     * @param array $type
     *
     * @return array
     */
    public function getGeneralData($type)
    {
        $data = [];

        foreach ($this->getPositions() as $scope => $value) {
            $addresses = !empty($value['addresses']) ? $value['addresses'] : [];
            $additionalClasses = ($scope === 'mpShippingAddressNewAttributes') ? 'col-mp' : '';

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses . ' required',
                            'tooltip' => [
                                'description' => 'Store 1'
                            ]
                        ],
                        'validation' => ['required-entry' => true, 'my-css' => true]
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => true,
                    'isUseTooltip' => true,
                    'frontend_class' => 'my-css',
                    'customScope' => $scope,
                    'fieldType' => $type['fieldType'],
                    'component' => $type['component'],
                    'elementTmpl' => $type['elementTmpl'],
                    'frontendInput' => $type['frontendInput'],
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                            'tooltip' => [
                                'description' => 'Store 1'
                            ]
                        ],
                        'validation' => ['my-css' => true]
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => false,
                    'isUseTooltip' => true,
                    'frontend_class' => 'my-css',
                    'customScope' => $scope,
                    'addresses' => $addresses,
                    'fieldType' => $type['fieldType'],
                    'component' => $type['component'],
                    'elementTmpl' => $type['elementTmpl'],
                    'frontendInput' => $type['frontendInput'],
                    'default' => null
                ],
                $value['isOsc'],
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ],
                        'validation' => ['my-css' => true]
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => false,
                    'isUseTooltip' => false,
                    'frontend_class' => 'my-css',
                    'customScope' => $scope,
                    'addresses' => $addresses,
                    'fieldType' => $type['fieldType'],
                    'component' => $type['component'],
                    'elementTmpl' => $type['elementTmpl'],
                    'frontendInput' => $type['frontendInput'],
                    'default' => null
                ],
                $value['isOsc'],
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ]
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => false,
                    'isUseTooltip' => false,
                    'frontend_class' => '',
                    'customScope' => $scope,
                    'addresses' => $addresses,
                    'fieldType' => $type['fieldType'],
                    'component' => $type['component'],
                    'elementTmpl' => $type['elementTmpl'],
                    'frontendInput' => $type['frontendInput'],
                    'default' => null
                ],
                $value['isOsc'],
            ];
        }

        return $data;
    }

    /**
     * @param array $result
     * @param string $position
     * @param array $ui
     * @param boolean $isOscPage
     * @param array $options
     * @param array $additionalData
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function process(
        $result,
        $position,
        $ui,
        $isOscPage,
        $options = ['json' => '', 'decode' => []],
        $additionalData = ['json' => '', 'decode' => []]
    ) {
        $attributeCode = 'my_attribute';
        $frontendInput = $ui['frontendInput'];
        $component = $ui['component'];
        $elementTmpl = $ui['elementTmpl'];
        $fieldType = $ui['fieldType'];
        $storeId = 1;
        $tooltips = '{"0":"Default","1":"Store 1","2":"Store 2"}';
        $tooltipsDecode = [
            0 => 'Default',
            1 => 'Store 1',
            2 => 'Store 2'
        ];
        $isRequire = $ui['isRequired'];
        $frontendClass = $ui['frontend_class'];
        $defaultValue = $ui['default'];
        $sortOrder = 1;
        $isUseTooltip = $ui['isUseTooltip'];

        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        $jsLayout = [];

        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helperDataMock->expects($this->once())
            ->method('getFilteredAttributes')
            ->willReturn([$orderAttributeMock]);

        $customerMock = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()->getMock();
        $this->customerSessionMock->expects($this->once())->method('getCustomer')->willReturn($customerMock);
        $addresses = !empty($ui['addresses']) ? $ui['addresses'] : [];
        $customerMock->expects($this->once())->method('getAddresses')->willReturn($addresses);

        $orderAttributeMock->expects($this->atLeastOnce())->method('getAttributeCode')->willReturn($attributeCode);
        $orderAttributeMock->expects($this->once())->method('getPosition')->willReturn($position);

        $this->helperDataMock->method('isOscPage')->willReturn($isOscPage);
        $orderAttributeMock->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $this->helperDataMock->expects($this->once())
            ->method('getComponentByInputType')
            ->with($frontendInput)
            ->willReturn($component);
        $this->helperDataMock->expects($this->once())
            ->method('getElementTmplByInputType')
            ->with($frontendInput)
            ->willReturn($elementTmpl);
        $this->helperDataMock->expects($this->once())
            ->method('getFieldTypeByInputType')
            ->with($frontendInput)
            ->willReturn($fieldType);

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()->getMock();

        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())
            ->method('prepareLabel')
            ->with($orderAttributeMock, $storeId)
            ->willReturn('Test');
        $orderAttributeMock->expects($this->once())->method('getTooltips')->willReturn($tooltips);
        $this->helperDataMock
            ->method('jsonDecodeData')
            ->withConsecutive([$tooltips], [$options['json']], [$additionalData['json']], [$additionalData['json']])
            ->willReturnOnConsecutiveCalls(
                $tooltipsDecode,
                $options['decode'],
                $additionalData['decode'],
                $additionalData['decode']
            );

        $orderAttributeMock->expects($this->once())->method('getIsRequired')->willReturn($isRequire);
        $orderAttributeMock->expects($this->atLeastOnce())->method('getFrontendClass')->willReturn($frontendClass);
        $orderAttributeMock->expects($this->once())->method('getDefaultValue')->willReturn($defaultValue);
        $orderAttributeMock->expects($this->once())->method('getSortOrder')->willReturn($sortOrder);
        $orderAttributeMock->expects($this->once())->method('getUseTooltip')->willReturn($isUseTooltip);
        if (in_array($frontendInput, ['select', 'multiselect', 'select_visual', 'multiselect_visual'], true)) {
            $orderAttributeMock->expects($this->once())
                ->method('getOptions')
                ->willReturn($options['json']);
            if (!empty($additionalData['json'])) {
                $orderAttributeMock->expects($this->atLeastOnce())
                    ->method('getAdditionalData')
                    ->willReturn($additionalData['json']);

                $this->swatchHelperMock->expects($this->once())->method('getSwatchAttributeImage')
                    ->with('swatch_thumb', $additionalData['decode']['option_0']['swatch_value'])
                    ->willReturn('some_url');
            }
        }

        $field = [
            'component' => $component,
            'fieldType' => $fieldType,
            'dataScope' => $ui['customScope'] . '.my_attribute',
            'validation' => [],
            'label' => 'Test',
            'options' => [],
            'caption' => new Phrase('Please select an option'),
            'provider' => 'mpOrderAttributesCheckoutProvider',
            'visible' => true,
            'sortOrder' => 1,
            'default' => $defaultValue,
            'config' => [
                'rows' => 5,
                'customScope' => $ui['customScope'],
                'elementTmpl' => $elementTmpl,
                'template' => 'ui/form/field',
                'additionalClasses' => '',
            ]
        ];

        $field = array_replace_recursive($field, $result['field']);
        $result = $this->mergerData($result['fieldset'], $field);

        $this->assertEquals($result, $this->layoutProcessorBock->process($jsLayout));
    }

    /**
     * @param array $result
     * @param array $data
     *
     * @return mixed
     */
    public function mergerData($result, $data)
    {
        $this->fieldData = $data;
        array_walk_recursive(
            $result,
            function (&$value, $key) {
                if ($key === 'my_attribute') {
                    $value = $this->fieldData;
                }
            }
        );

        return $result;
    }
}
