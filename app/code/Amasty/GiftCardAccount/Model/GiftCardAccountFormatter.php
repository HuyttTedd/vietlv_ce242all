<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\OptionSource\AccountStatus;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class GiftCardAccountFormatter
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var AccountStatus
     */
    private $accountStatus;

    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        PriceCurrencyInterface $priceCurrency,
        AccountStatus $accountStatus
    ) {
        $this->date = $date;
        $this->priceCurrency = $priceCurrency;
        $this->accountStatus = $accountStatus;
    }

    /**
     * @param GiftCardAccountInterface|null $account
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormattedData(GiftCardAccountInterface $account): array
    {
        return [
            'id' => $account->getAccountId(),
            'code' => $account->getCodeModel()->getCode(),
            'status' => $this->getCardStatus($account),
            'balance' => $this->getCurrentBalance($account->getCurrentValue()),
            'expiredDate' => $account->getExpiredDate()
                ? $this->getExpiredDate($account->getExpiredDate())
                : 'unlimited'
        ];
    }

    /**
     * @param float $price
     *
     * @return string
     */
    protected function getCurrentBalance(float $price): string
    {
        return $this->priceCurrency->convertAndFormat($price);
    }

    /**
     * @param string $date
     *
     * @return string
     */
    protected function getExpiredDate(string $date): string
    {
        return $this->date->date('Y-m-d', $date);
    }

    /**
     * @param GiftCardAccountInterface $card
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function getCardStatus(GiftCardAccountInterface $card)
    {
        $statuses = $this->accountStatus->toArray();

        return $statuses[$card->getStatus()] ?? 'Undefined';
    }
}
