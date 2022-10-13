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
 * Class LayoutProcessorWithTypeSelectAndMultiselectVisualTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Checkout
 */
class LayoutProcessorWithTypeSelectAndMultiselectVisualTest extends AbstractLayoutProcessorTest
{
    /**
     * @return array
     */
    public function getOptionsVisualMock()
    {
        return [
            'json' => '{"optionvisual":{"value":{"option_0":["Admin option 1 Label","Default Store View Label",""],"option_1":["Admin Option 2 Label","Default Store View Label 2",""]}},"defaultvisual":["option_0"]}',
            'decode' => [
                'optionvisual' => [
                    'value' => [
                        'option_0' => [
                            0 => 'Admin option 1 Label',
                            1 => 'Default Store View Label',
                            2 => ''
                        ],

                        'option_1' => [
                            0 => 'Admin Option 2 Label',
                            1 => 'Default Store View Label 2',
                            2 => ''
                        ]
                    ],
                ],
                'defaultvisual' => [
                    'option_0'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getAdditionalDataMock()
    {
        return [
            'json' => '{"option_0":{"swatch_value":"\/8\/1\/test.jpg","swatch_type":2},"option_1":{"swatch_value":"#b81fb8","swatch_type":1}}',
            'decode' => [
                'option_0' => [
                    'swatch_value' => '/8/1/test.jpg',
                    'swatch_type' => 2
                ],

                'option_1' => [
                    'swatch_value' => '#b81fb8',
                    'swatch_type' => 1
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function providerTestProcessWithTypeVisual()
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
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/radio-visual',
                    'fieldType' => 'select',
                    'frontendInput' => 'select_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc']
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
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
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/radio-visual',
                    'fieldType' => 'select',
                    'addresses' => $addresses,
                    'frontendInput' => 'select_visual',
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/radio-visual',
                    'fieldType' => 'select',
                    'frontendInput' => 'select_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/radio-visual',
                    'fieldType' => 'select',
                    'addresses' => $addresses,
                    'frontendInput' => 'select_visual',
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/radio-visual',
                    'fieldType' => 'select',
                    'frontendInput' => 'select_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];

            //multiselect_visual
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/checkboxes',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/checkbox-visual',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc']
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/checkboxes',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/checkbox-visual',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];
            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/checkboxes',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/checkbox-visual',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
                        'config' => [
                            'additionalClasses' => $additionalClasses
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
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/checkboxes',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/checkbox-visual',
                    'fieldType' => 'multiselect',
                    'addresses' => $addresses,
                    'frontendInput' => 'multiselect_visual',
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'dataScope' => $scope . '.my_attribute[]',
                        'options' => [
                            [
                                'value' => 'option_0',
                                'label' => new Phrase('Default Store View Label'),
                                'visual' => '<img class="image" src="some_url">'
                            ],
                            [
                                'value' => 'option_1',
                                'label' => new Phrase('Default Store View Label 2'),
                                'visual' => '<div class="color" style="background-color: #b81fb8"></div>'
                            ]
                        ],
                        'default' => 'option_0',
                        'config' => [
                            'additionalClasses' => $additionalClasses,
                        ],
                    ]
                ],
                $value['position'],
                [
                    'isRequired' => false,
                    'isUseTooltip' => false,
                    'frontend_class' => '',
                    'customScope' => $scope,
                    'component' => 'Mageplaza_OrderAttributes/js/form/element/checkboxes',
                    'elementTmpl' => 'Mageplaza_OrderAttributes/form/element/checkbox-visual',
                    'fieldType' => 'multiselect',
                    'frontendInput' => 'multiselect_visual',
                    'addresses' => $addresses,
                    'default' => null
                ],
                $value['isOsc'],
                $this->getOptionsVisualMock(),
                $this->getAdditionalDataMock()
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
     * @dataProvider providerTestProcessWithTypeVisual
     */
    public function testProcessWithTypeSelectAndMultiselectVisual(
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
