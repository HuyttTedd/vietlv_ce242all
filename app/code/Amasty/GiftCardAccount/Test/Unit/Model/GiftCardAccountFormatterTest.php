<?php

namespace Amasty\GiftCardAccount\Test\Unit\Model;

use Amasty\GiftCard\Model\Code\Code;
use Amasty\GiftCardAccount\Model\OptionSource\AccountStatus;
use Amasty\GiftCardAccount\Model\GiftCardAccountFormatter;
use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Account;

/**
 * @covers \Amasty\GiftCardAccount\Model\GiftCardAccountFormatter
 */
class GiftCardAccountFormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GiftCardAccountFormatter
     */
    private $accountFormater;

    /**
     * @var DateTime|MockObject
     */
    private $date;

    /**
     * @var PriceCurrency|MockObject
     */
    private $priceCurrency;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->date = $this->createPartialMock(DateTime::class, ['date']);
        $this->priceCurrency = $this->createPartialMock(PriceCurrency::class, ['convertAndFormat']);
        $accountStatus = $this->createPartialMock(AccountStatus::class, []);

        $this->accountFormater = $objectManager->getObject(
            GiftCardAccountFormatter::class,
            [
                'date' => $this->date,
                'priceCurrency' => $this->priceCurrency,
                'accountStatus' => $accountStatus
            ]
        );
    }

    /**
     * @dataProvider getFormattedDataDataProvider
     */
    public function testGetFormattedData($expirationDate, $value, $websiteId, $status, $expected)
    {
        $code = $this->createPartialMock(Code::class, []);
        $code->setCode('test_code');
        $account = $this->createPartialMock(Account::class, ['getCodeModel']);
        $account->expects($this->once())->method('getCodeModel')->willReturn($code);
        $account->setAccountId(10);
        $account->setStatus($status);
        $account->setCurrentValue($value);
        $account->setWebsiteId($websiteId);
        $account->setExpiredDate($expirationDate);

        $this->priceCurrency->expects($this->atLeastOnce())->method('convertAndFormat')
            ->with($value)
            ->willReturn('$' . $value);
        $this->date->expects($expirationDate ? $this->once() : $this->never())->method('date')
            ->willReturn($expirationDate);

        $this->assertEquals($expected, $this->accountFormater->getFormattedData($account));
    }

    public function getFormattedDataDataProvider()
    {
        return [
            [//all correct data
                '04/05/2022',
                25,
                1,
                1,
                [
                    'id' => 10,
                    'code' => 'test_code',
                    'status' => 'Active',
                    'balance' => '$25',
                    'expiredDate' => '04/05/2022'
                ]
            ],
            [//no expiration date
                '',
                25,
                1,
                1,
                [
                    'id' => 10,
                    'code' => 'test_code',
                    'status' => 'Active',
                    'balance' => '$25',
                    'expiredDate' => 'unlimited'
                ]
            ],
            [//undefined status
                '04/05/2022',
                25,
                1,
                4444,
                [
                    'id' => 10,
                    'code' => 'test_code',
                    'status' => 'Undefined',
                    'balance' => '$25',
                    'expiredDate' => '04/05/2022'
                ]
            ]
        ];
    }
}
