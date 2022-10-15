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
 * Class LayoutProcessorWithTypeTextareaTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Checkout
 */
class LayoutProcessorWithTypeTextareaTest extends AbstractLayoutProcessorTest
{
    /**
     * @return array
     */
    public function providerTestProcess()
    {
        $type = [
            'frontendInput' => 'textarea',
            'fieldType' => 'textarea',
            'component' => 'Magento_Ui/js/form/element/textarea',
            'elementTmpl' => 'ui/form/element/textarea'
        ];

        return $this->getGeneralData($type);
    }

    /**
     * @param array $result
     * @param string $position
     * @param array $ui
     * @param boolean $isOscPage
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerTestProcess
     */
    public function testProcess(
        $result,
        $position,
        $ui,
        $isOscPage
    ) {
        $this->process($result, $position, $ui, $isOscPage);
    }
}
