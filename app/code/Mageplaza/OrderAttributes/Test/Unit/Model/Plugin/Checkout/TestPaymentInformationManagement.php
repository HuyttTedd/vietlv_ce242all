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

namespace Mageplaza\OrderAttributes\Test\Unit\Model\Plugin\Checkout;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentExtensionInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Plugin\Checkout\PaymentInformationManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestPaymentInformationManagement
 * @package Mageplaza\OrderAttributes\Test\Unit\Model\Plugin\Checkout
 */
class TestPaymentInformationManagement extends TestCase
{
    /**
     * @var CartRepositoryInterface|MockObject
     */
    private $cartRepositoryMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var PaymentInformationManagement
     */
    private $plugin;

    protected function setUp()
    {
        $this->cartRepositoryMock = $this->getMockBuilder(CartRepositoryInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->plugin = new PaymentInformationManagement(
            $this->cartRepositoryMock,
            $this->helperDataMock
        );
    }

    public function testBeforeSavePaymentMethod()
    {
        $cartId = 1;
        $storeId = 1;
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'setMpOrderAttributes'])
            ->disableOriginalConstructor()->getMock();
        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($cartId)
            ->willReturn($quoteMock);
        $quoteMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(true);
        /**
         * @var PaymentInterface|MockObject $paymentMock
         */
        $paymentMock = $this->createMock(PaymentInterface::class);

        /**
         * @var AddressInterface $billingAddressMock
         */
        $billingAddressMock = $this->createMock(AddressInterface::class);

        $paymentExtensionMock = $this->getMockBuilder(PaymentExtensionInterface::class)
            ->setMethods(['getMpOrderAttributes'])->getMockForAbstractClass();

        $paymentMock->expects($this->once())->method('getExtensionAttributes')->willReturn($paymentExtensionMock);

        $orderAttribute = ['my_attribute' => 'test'];
        $paymentExtensionMock->expects($this->exactly(2))->method('getMpOrderAttributes')
            ->willReturn($orderAttribute);

        $quoteMock->expects($this->once())->method('setMpOrderAttributes')->with($orderAttribute);

        /**
         * @var \Magento\Checkout\Model\PaymentInformationManagement $subject
         */
        $subject = $this->getMockBuilder(\Magento\Checkout\Model\PaymentInformationManagement::class)
            ->disableOriginalConstructor()->getMock();

        $this->assertSame(
            [$cartId, $paymentMock, $billingAddressMock],
            $this->plugin->beforeSavePaymentInformation($subject, $cartId, $paymentMock, $billingAddressMock)
        );
    }
}
