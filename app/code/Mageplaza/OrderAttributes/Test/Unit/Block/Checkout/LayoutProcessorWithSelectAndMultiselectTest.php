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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;

/**
 * Class LayoutProcessorWithSelectAndMultiselectTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Checkout
 */
class LayoutProcessorWithSelectAndMultiselectTest extends AbstractLayoutProcessorTest
{
    /**
     * @return array
     */
    public function getOptionsMock()
    {
        return [
            'json' => '{"option":{"value":{"option_0":["Admin1","Default Store View 1","Us1",""],"option_1":["Admin2","Default Store View 2","Us1",""]}},"default":["option_0"]}',
            'decode' => [
                'option' => [
                    'value' => [
                        'option_0' => [
                            'Admin1',
                            'Default Store View 1',
                            'Us1',
                            ''
                        ],
                        'option_1' => [
                            'Admin2',
                            'Default Store View 2',
                            'Us1',
                            ''
                        ]
                    ],
                ],
                'default' => [
                    'option_0'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerTestProcessWithTypeSelectAndMultiselect()
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'ui/form/element/select',
                    'fieldType' => 'select',
                    'frontendInput' => 'select',
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
                            'additionalClasses' => $additionalClasses . ' required',
                            'tooltip' => [
                                'description' => 'Store 1'
                            ]
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'ui/form/element/select',
                    'fieldType' => 'select',
                    'frontendInput' => 'select',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
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
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'ui/form/element/select',
                    'fieldType' => 'select',
                    'frontendInput' => 'select',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'ui/form/element/select',
                    'fieldType' => 'select',
                    'frontendInput' => 'select',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
                        ],
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => false,
                    'isUseTooltip' => false,
                    'frontend_class' => '',
                    'customScope' => $scope,
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'ui/form/element/select',
                    'fieldType' => 'select',
                    'frontendInput' => 'select',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
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
                    'component' => 'Magento_Ui/js/form/element/multiselect',
                    'elementTmpl' => 'ui/form/element/multiselect',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'config' => [
                            'additionalClasses' => $additionalClasses . ' required',
                            'tooltip' => [
                                'description' => 'Store 1'
                            ]
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
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
                    'component' => 'Magento_Ui/js/form/element/multiselect',
                    'elementTmpl' => 'ui/form/element/multiselect',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                            'tooltip' => [
                                'description' => 'Store 1'
                            ]
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
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
                    'component' => 'Magento_Ui/js/form/element/multiselect',
                    'elementTmpl' => 'ui/form/element/multiselect',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
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
                    'component' => 'Magento_Ui/js/form/element/multiselect',
                    'elementTmpl' => 'ui/form/element/multiselect',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ],
                        'options' => [
                            ['value' => 'option_0', 'label' => new Phrase('Default Store View 1')],
                            ['value' => 'option_1', 'label' => new Phrase('Default Store View 2')]
                        ],
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => false,
                    'isUseTooltip' => false,
                    'frontend_class' => '',
                    'customScope' => $scope,
                    'component' => 'Magento_Ui/js/form/element/multiselect',
                    'elementTmpl' => 'ui/form/element/multiselect',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect',
                    'addresses' => $addresses,
                    'default' => 'option_0'
                ],
                $value['isOsc'],
                $this->getOptionsMock()
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
     * @dataProvider providerTestProcessWithTypeSelectAndMultiselect
     */
    public function testProcessWithTypeSelectAndMultiselect(
        $result,
        $position,
        $ui,
        $isOscPage,
        $options = ['json' => '', 'decode' => []],
        $additionalData = ['json' => '', 'decode' => []]
    ) {
        $this->process($result, $position, $ui, $isOscPage, $options, $additionalData);
    }
}
