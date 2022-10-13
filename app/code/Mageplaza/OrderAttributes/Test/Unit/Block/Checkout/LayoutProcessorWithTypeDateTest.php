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

/**
 * Class LayoutProcessorWithTypeDateTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Checkout
 */
class LayoutProcessorWithTypeDateTest extends AbstractLayoutProcessorTest
{
    /**
     * @return array
     */
    public function providerTestProcessWithTypeDate()
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
                            'additionalClasses' => $additionalClasses . ' required date',
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
                            'additionalClasses' => $additionalClasses . ' required date',
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
                    'addresses' => $addresses,
                    'default' => '05/11/2020'
                ],
                $value['isOsc'],
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses . ' date',
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
                    'default' => '05/11/2020'
                ],
                $value['isOsc'],
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses . ' date',
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
                    'default' => '05/11/2020'
                ],
                $value['isOsc'],
            ];

            $data[] = [
                [
                    'fieldset' => $value['fieldset'],
                    'field' => [
                        'config' => [
                            'additionalClasses' => $additionalClasses . ' date',
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
                    'default' => '05/11/2020'
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
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerTestProcessWithTypeDate
     */
    public function testProcessWithTypeDate(
        $result,
        $position,
        $ui,
        $isOscPage
    ) {
        $result['field']['options'] = [
            'changeMonth' => true,
            'changeYear' => true,
            'showOn' => 'both'
        ];

        $ui = array_merge($ui, [
            'component' => 'Mageplaza_OrderAttributes/js/form/element/date',
            'elementTmpl' => 'ui/form/element/date',
            'fieldType' => 'date',
            'frontendInput' => 'date',
        ]);

        $this->process($result, $position, $ui, $isOscPage);
    }
}
