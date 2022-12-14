<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Utils;

use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Model\Email\UploadTransportBuilder;
use Amasty\GiftCard\Model\GiftCard\GiftCardEmail;
use Amasty\GiftCard\Model\Pdf\PdfImageConverter;
use Magento\Framework\App\Area;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State as AppState;
use Psr\Log\LoggerInterface;

class EmailSender
{
    public const KEY_EMAIL = 0;
    public const KEY_NAME = 1;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UploadTransportBuilder
     */
    private $transportBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var PdfImageConverter
     */
    private $pdfImageConverter;

    /**
     * @var AppState
     */
    private $appState;

    public function __construct(
        StoreManagerInterface $storeManager,
        UploadTransportBuilder $transportBuilder,
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        ConfigProvider $configProvider,
        PdfImageConverter $pdfImageConverter,
        AppState $appState
    ) {
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->configProvider = $configProvider;
        $this->pdfImageConverter = $pdfImageConverter;
        $this->appState = $appState;
    }

    /**
     * @param array $sendTo
     * @param string $sendFrom
     * @param int $storeId
     * @param string $templateIdentifier
     * @param array $vars
     * @param string $area
     */
    public function sendEmail(
        array $sendTo = [],
        string $sendFrom = 'general',
        int $storeId = 0,
        string $templateIdentifier = '',
        array $vars = [],
        string $area = Area::AREA_FRONTEND
    ) {
        try {
            /** @var \Magento\Store\Model\Store $store */
            $store = $this->storeManager->getStore($storeId);

            foreach ($sendTo as $receiver) {
                $this->transportBuilder->setTemplateIdentifier($templateIdentifier)
                    ->setTemplateOptions(['area' => $area, 'store' => $store->getId()])
                    ->setTemplateVars($vars)
                    ->setFrom($sendFrom, $storeId);
                if (is_array($receiver)) {
                    $this->transportBuilder->addTo(
                        $receiver[self::KEY_EMAIL],
                        $receiver[self::KEY_NAME]
                    );
                } else {
                    $this->transportBuilder->addTo($receiver);
                }
                /** @var GiftCardEmail $gCardEmail */
                $gCardEmail = $vars['gcard_email'];
                if ($this->configProvider->isAttachPdfGiftCard() && $gCardEmail->getImage()) {
                    $pdf = $this->pdfImageConverter->convert(
                        $gCardEmail->getImage(),
                        $gCardEmail->getGiftCode()
                    );
                    $this->transportBuilder->addAttachment($pdf);
                }
                $transport = $this->appState->emulateAreaCode(
                    Area::AREA_FRONTEND,
                    [$this->transportBuilder, 'getTransport']
                );

                $transport->sendMessage();
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(__('Something went wrong with sending emails. Please review logs.'));
        }
    }
}
