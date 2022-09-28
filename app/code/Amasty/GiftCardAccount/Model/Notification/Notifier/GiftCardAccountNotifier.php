<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\Notification\Notifier;

use Amasty\GiftCard\Api\Data\GiftCardEmailInterface;
use Amasty\GiftCard\Api\Data\GiftCardEmailInterfaceFactory;
use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\GiftCard\Attributes;
use Amasty\GiftCard\Model\Image\EmailImageProvider;
use Amasty\GiftCard\Model\Image\Repository as ImageRepository;
use Amasty\GiftCard\Utils\EmailSender;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Repository as AccountRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class GiftCardAccountNotifier implements GiftCardNotifierInterface
{
    /**
     * @var GiftCardEmailInterfaceFactory
     */
    private $cardEmailFactory;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EmailImageProvider
     */
    private $emailImageProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        GiftCardEmailInterfaceFactory $cardEmailFactory,
        EmailSender $emailSender,
        PriceCurrencyInterface $priceCurrency,
        ConfigProvider $configProvider,
        ImageRepository $imageRepository,
        AccountRepository $accountRepository,
        StoreManagerInterface $storeManager,
        EmailImageProvider $emailImageProvider,
        LoggerInterface $logger
    ) {
        $this->cardEmailFactory = $cardEmailFactory;
        $this->emailSender = $emailSender;
        $this->priceCurrency = $priceCurrency;
        $this->configProvider = $configProvider;
        $this->imageRepository = $imageRepository;
        $this->accountRepository = $accountRepository;
        $this->storeManager = $storeManager;
        $this->emailImageProvider = $emailImageProvider;
        $this->logger = $logger;
    }

    public function notify(
        GiftCardAccountInterface $account,
        string $giftCardRecipientName = null,
        string $giftCardRecipientEmail = null,
        int $storeId = 0
    ): void {
        try {
            if ($account->getOrderItem()) {
                $this->sendByOrderItem($account, $giftCardRecipientName, $giftCardRecipientEmail, $storeId);
            } elseif ($giftCardRecipientEmail) {
                $this->sendByAccountData($account, $giftCardRecipientEmail, $giftCardRecipientName, $storeId);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return;
        }

        $account->setIsSent(true);
        $this->accountRepository->save($account);
    }

    private function sendByOrderItem(
        GiftCardAccountInterface $account,
        string $recipientName = null,
        string $recipientEmail = null,
        int $storeId = 0
    ): void {
        if (!($orderItem = $account->getOrderItem())) {
            return;
        }

        $this->updateProductOptions($orderItem, $recipientName, $recipientEmail);
        $productOptions = $orderItem->getProductOptions();
        $storeId = $storeId ?: (int)$orderItem->getStoreId();
        $cardEmail = $this->prepareGiftCardEmailFromOrderItem($account, $orderItem, $storeId);

        $code = $account->getCodeModel()->getCode();
        $imageHtml = $this->prepareImage((int)$productOptions[GiftCardOptionInterface::IMAGE] ?? 0, $code);

        $cardEmail->setGiftCode($code)->setImage($imageHtml);
        $this->emailSender->sendEmail(
            $this->getRecipients($productOptions, $storeId),
            $this->configProvider->getEmailSender($storeId),
            $storeId,
            $productOptions[Attributes::EMAIL_TEMPLATE] ?? $this->configProvider->getEmailTemplate($storeId),
            ['gcard_email' => $cardEmail],
        );
    }

    private function sendByAccountData(
        GiftCardAccountInterface $account,
        string $recipientEmail,
        string $recipientName = null,
        int $storeId = 0
    ): void {
        $balance = $this->formatBalance($account->getInitialValue(), $storeId);
        $code = $account->getCodeModel()->getCode();
        $imageHtml = $this->prepareImage($account->getImageId(), $code);

        $cardEmail = $this->cardEmailFactory->create()
            ->setRecipientName($recipientName)
            ->setExpiredDate($account->getExpiredDate())
            ->setBalance($balance)
            ->setGiftCode($code)
            ->setImage($imageHtml)
            ->setIsAllowAssignToCustomer($this->configProvider->isAllowAssignToCustomer($storeId));

        $this->emailSender->sendEmail(
            $this->getRecipients([
                GiftCardOptionInterface::RECIPIENT_EMAIL => $recipientEmail,
                GiftCardOptionInterface::RECIPIENT_NAME => $recipientName
            ], $storeId),
            $this->configProvider->getEmailSender($storeId),
            $storeId,
            $this->configProvider->getEmailTemplate($storeId),
            ['gcard_email' => $cardEmail]
        );
    }

    private function updateProductOptions(
        OrderItemInterface $orderItem,
        string $recipientName = null,
        string $recipientEmail = null
    ): void {
        $productOptions = $orderItem->getProductOptions();

        $productOptions[GiftCardOptionInterface::RECIPIENT_NAME] =
            $recipientName ?: $productOptions[GiftCardOptionInterface::RECIPIENT_NAME] ?? '';
        $productOptions[GiftCardOptionInterface::RECIPIENT_EMAIL] =
            $recipientEmail ?: $productOptions[GiftCardOptionInterface::RECIPIENT_EMAIL] ?? '';
        $productOptions[GiftCardOptionInterface::SENDER_NAME] =
            $productOptions[GiftCardOptionInterface::SENDER_NAME] ?? $orderItem->getOrder()->getCustomerName();
        $productOptions[GiftCardOptionInterface::SENDER_EMAIL] =
            $productOptions[GiftCardOptionInterface::SENDER_EMAIL] ?? $orderItem->getOrder()->getCustomerEmail();

        $orderItem->setProductOptions($productOptions);
    }

    private function prepareGiftCardEmailFromOrderItem(
        GiftCardAccountInterface $account,
        OrderItemInterface $orderItem,
        int $storeId = 0
    ): GiftCardEmailInterface {
        $cardEmail = $this->cardEmailFactory->create();
        $balance = $this->formatBalance($account->getInitialValue(), (int)$orderItem->getStoreId());
        $orderItemOptions = $orderItem->getProductOptions();

        $cardEmail->setRecipientName($orderItemOptions[GiftCardOptionInterface::RECIPIENT_NAME])
            ->setSenderName(
                $orderItemOptions[GiftCardOptionInterface::SENDER_NAME]
            )->setSenderEmail(
                $orderItemOptions[GiftCardOptionInterface::SENDER_EMAIL]
            )->setSenderMessage($orderItemOptions[GiftCardOptionInterface::MESSAGE] ?? '')
            ->setExpiredDate($account->getExpiredDate())
            ->setBalance($balance)
            ->setIsAllowAssignToCustomer($this->configProvider->isAllowAssignToCustomer($storeId));

        return $cardEmail;
    }

    private function getRecipients(array $productOptions, int $storeId): array
    {
        $recipients[] = [
            $productOptions[GiftCardOptionInterface::RECIPIENT_EMAIL],
            $productOptions[GiftCardOptionInterface::RECIPIENT_NAME]
        ];
        if ($sendCopyTo = $this->configProvider->getEmailRecipients($storeId)) {
            $recipients = array_merge($recipients, $sendCopyTo);
        }

        return $recipients;
    }

    private function formatBalance(float $amount, int $storeId): string
    {
        try {
            $store = $this->storeManager->getStore($storeId);
        } catch (NoSuchEntityException $e) {
            $store = $this->storeManager->getDefaultStoreView();
        }

        return $this->priceCurrency->convertAndFormat(
            $amount,
            false,
            2,
            $store,
            $store->getCurrentCurrencyCode()
        );
    }

    private function prepareImage(int $imageId, string $code): string
    {
        $imageHtml = '';

        if ($imageId !== 0) {
            try {
                $image = $this->imageRepository->getById($imageId);
                $imageHtml = $this->emailImageProvider->get($image, $code);
            } catch (LocalizedException $e) {
                null;
            }
        }

        return $imageHtml;
    }
}
