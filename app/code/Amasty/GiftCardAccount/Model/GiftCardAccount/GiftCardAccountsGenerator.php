<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\GiftCardAccount;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Model\Code\Repository as CodeRepository;
use Amasty\GiftCard\Model\Code\ResourceModel\Code as CodeResource;
use Amasty\GiftCard\Model\OptionSource\Status;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel\Account as AccountResource;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Filter\Date;

class GiftCardAccountsGenerator
{
    /**
     * @var CodeRepository
     */
    private $codeRepository;

    /**
     * @var Date
     */
    private $dateFilter;

    /**
     * @var CodeResource
     */
    private $codeResource;

    /**
     * @var AccountResource
     */
    private $accountResource;

    public function __construct(
        CodeRepository $codeRepository,
        Date $dateFilter,
        CodeResource $codeResource,
        AccountResource $accountResource
    ) {
        $this->codeRepository = $codeRepository;
        $this->dateFilter = $dateFilter;
        $this->codeResource = $codeResource;
        $this->accountResource = $accountResource;
    }

    /**
     * @param DataObject $accountsFormData
     * @param int $qty
     * @return void
     *
     * @throws LocalizedException
     */
    public function generate(DataObject $accountsFormData, int $qty): void
    {
        $codePoolId = (int)$accountsFormData->getCodePool();
        $availableCodes = $this->codeRepository->getAvailableCodesByCodePoolId($codePoolId, $qty);
        if ($qty > count($availableCodes)) {
            throw new LocalizedException(__(
                'Unable to generate new accounts. Error: No available codes found for Code Pool with ID "%1".',
                $codePoolId
            ));
        }

        $accountInsertData = $codeInsertData = [];
        $accountsData = $this->prepareAccountsData($accountsFormData);
        for ($i = 0; $i < $qty; $i++) {
            /** @var CodeInterface $code */
            $code = array_pop($availableCodes);
            $accountsData[GiftCardAccountInterface::CODE_ID] = $code->getCodeId();
            $accountInsertData[] = $accountsData;
            $code->setStatus(Status::USED);
            $codeInsertData[] = $code->getData();
        }
        $this->accountResource->insertMultipleAccounts($accountInsertData, $codeInsertData);
    }

    /**
     * @param DataObject $accountsFormData
     * @return array
     *
     * @throws \Exception
     */
    private function prepareAccountsData(DataObject $accountsFormData): array
    {
        $accountsData = $accountsFormData->getData();

        if ($balance = $accountsData['balance'] ?? 0) {
            $accountsData[GiftCardAccountInterface::INITIAL_VALUE] =
            $accountsData[GiftCardAccountInterface::CURRENT_VALUE] = $balance;
        }

        if ($expiredDate = $accountsData[GiftCardAccountInterface::EXPIRED_DATE] ?? '') {
            $accountsData[GiftCardAccountInterface::EXPIRED_DATE] = $this->dateFilter->filter($expiredDate);
        } else {
            $accountsData[GiftCardAccountInterface::EXPIRED_DATE] = null;
        }

        unset(
            $accountsData['qty'],
            $accountsData['code_pool'],
            $accountsData['form_key'],
            $accountsData['balance']
        );

        return $accountsData;
    }
}
