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
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Plugin\Checkout\GuestPaymentInformationManagement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TestGuestPaymentInformationManagement
 * @package Mageplaza\OrderAttributes\Test\Unit\Model\Plugin\Checkout
 */
class TestGuestPaymentInformationManagement extends TestCase
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
     * @var QuoteIdMaskFactory|MockObject
     */
    protected $quoteIdMaskFactoryMock;

    /**
     * @var GuestPaymentInformationManagement
     */
    private $plugin;

    protected function setUp()
    {
        $this->cartRepositoryMock = $this->getMockBuilder(CartRepositoryInterface::class)
            ->disableOriginalConstructor()->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->quoteIdMaskFactoryMock = $this->getMockBuilder(QuoteIdMaskFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->plugin = new GuestPaymentInformationManagement(
            $this->cartRepositoryMock,
            $this->quoteIdMaskFactoryMock,
            $this->helperDataMock
        );
    }

    public function testBeforeSavePaymentInformation()
    {
        $cartId = 'YphgiOOIutUAvvpev8agDIXACxThDg9F';
        $storeId = 1;
        $quoteIdMaskMock = $this->getMockBuilder(QuoteIdMask::class)->setMethods(['load', 'getQuoteId'])
            ->disableOriginalConstructor()->getMock();
        $this->quoteIdMaskFactoryMock->expects($this->once())->method('create')->willReturn($quoteIdMaskMock);
        $quoteIdMaskMock->expects($this->once())->method('load')->with($cartId, 'masked_id')->willReturnSelf();
        $quoteMock = $this->getMockBuilder(Quote::class)
            ->setMethods(['getStoreId', 'setMpOrderAttributes'])
            ->disableOriginalConstructor()->getMock();
        $quoteId = 1;
        $quoteIdMaskMock->expects($this->once())->method('getQuoteId')->willReturn($quoteId);
        $this->cartRepositoryMock->expects($this->once())
            ->method('get')
            ->with($quoteId)
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helperDataMock->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(true);
        /**
         * @var PaymentInterface|MockObject $paymentMock
         */
        $paymentMock = $this->createMock(PaymentInterface::class);

        /**
         * @var AddressInterface|MockObject $billingAddressMock
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
         * @var \Magento\Checkout\Model\GuestPaymentInformationManagement $subject
         */
        $subject = $this->getMockBuilder(\Magento\Checkout\Model\GuestPaymentInformationManagement::class)
            ->disableOriginalConstructor()->getMock();

        $email = 'test@gmail.com';
        $this->assertSame(
            [$cartId, $email, $paymentMock, $billingAddressMock],
            $this->plugin->beforeSavePaymentInformation($subject, $cartId, $email, $paymentMock, $billingAddressMock)
        );
    }
}
