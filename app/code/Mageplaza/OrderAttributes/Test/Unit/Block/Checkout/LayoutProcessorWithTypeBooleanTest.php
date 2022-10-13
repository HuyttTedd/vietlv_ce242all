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
 * Class LayoutProcessorWithTypeBooleanTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Checkout
 */
class LayoutProcessorWithTypeBooleanTest extends AbstractLayoutProcessorTest
{
    /**
     * @return array
     */
    public function providerTestProcessWithTypeBoolean()
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
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerTestProcessWithTypeBoolean
     */
    public function testProcessWithTypeBoolean(
        $result,
        $position,
        $ui,
        $isOscPage
    ) {
        $result['field']['options'] = [
            ['value' => '0', 'label' => new Phrase('No')],
            ['value' => '1', 'label' => new Phrase('Yes')]
        ];

        $ui = array_merge($ui, [
            'fieldType' => 'select',
            'component' => 'Mageplaza_OrderAttributes/js/form/element/select',
            'elementTmpl' => 'ui/form/element/select',
            'frontendInput' => 'boolean',
        ]);

        $this->process($result, $position, $ui, $isOscPage);
    }
}
