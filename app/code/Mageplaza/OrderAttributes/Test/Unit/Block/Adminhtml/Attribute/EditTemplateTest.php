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

namespace Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute;

use Magento\Backend\Block\Template\Context;
use Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\EditTemplate;
use Mageplaza\OrderAttributes\Helper\Data;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class EditTemplateTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Block\Adminhtml\Attribute
 */
class EditTemplateTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var EditTemplate
     */
    private $editTemplateBock;

    protected function setUp()
    {
        /**
         * @var Context $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();

        $this->editTemplateBock = new EditTemplate(
            $contextMock,
            $this->helperDataMock
        );
    }

    public function testGetTinymceConfig()
    {
        $this->helperDataMock->expects($this->once())->method('getTinymceConfig')->willReturn('TinymceConfig Mock');
        $this->editTemplateBock->getTinymceConfig();
    }
}
