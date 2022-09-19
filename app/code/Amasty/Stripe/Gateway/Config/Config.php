<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Stripe
 */

namespace Amasty\Stripe\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;

/**
 * Config Provider
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const KEY_ACTIVE = 'active';
    const KEY_APPLE_PAY = 'enable_apple_pay';
    const KEY_PUBLIC_KEY = 'public_key';
    const KEY_PRIVATE_KEY = 'private_key';
    const SAVE_CARDS = 'save_customer_card';
    const KEY_SDK_URL = 'sdk_url';
    const IMAGE = 'Amasty_Stripe::img/stripe.png';
    const CONFIG_PATH_LOGO_ENABLED = 'payment/amasty_stripe/logo';
    const KEY_THREE_D_SECURE_ALWAYS = 'three_d_secure_always';
    const KEY_ORDER_STATUS = 'order_status';
    const KEY_EMAIL_RECEIPTS = 'email_receipts';
    const KEY_PAYMENT_ACTION = 'payment_action';

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Repository $assetRepository,
        Encryptor $encryptor,
        UrlInterface $urlBuilder,
        $pathPattern = self::DEFAULT_PATH_PATTERN,
        $methodCode = null
    ) {
        $this->encryptor = $encryptor;
        $this->assetRepo = $assetRepository;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isActive(int $storeId = null)
    {
        return (bool) $this->getValue(self::KEY_ACTIVE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isApplePayEnabled(int $storeId = null)
    {
        return (bool)$this->getValue(self::KEY_APPLE_PAY, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getPublicKey(int $storeId = null)
    {
        return $this->getValue(self::KEY_PUBLIC_KEY, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getPrivateKey(int $storeId = null)
    {
        return $this->getValue(self::KEY_PRIVATE_KEY, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getSdkUrl(int $storeId = null)
    {
        return $this->getValue(self::KEY_SDK_URL, $storeId);
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        $asset = $this->assetRepo->createAsset(self::IMAGE);

        return $this->isLogoActive() ? $asset->getUrL() : '';
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEnableSaveCards(int $storeId = null)
    {
        return (bool)$this->getValue(self::SAVE_CARDS, $storeId);
    }

    /**
     * @return bool
     */
    private function isLogoActive()
    {
        $isLogoActive = $this->scopeConfig->getValue(
            self::CONFIG_PATH_LOGO_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return (bool) $isLogoActive;
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getThreedSecureAlways(int $storeId = null)
    {
        return $this->getValue(self::KEY_THREE_D_SECURE_ALWAYS, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getOrderStatus(int $storeId = null)
    {
        return $this->getValue(self::KEY_ORDER_STATUS, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isEmailReceiptsEnabled(int $storeId = null)
    {
        return (bool)$this->getValue(self::KEY_EMAIL_RECEIPTS, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return mixed
     */
    public function getAuthorizeMethod(int $storeId = null)
    {
        return $this->getValue(self::KEY_PAYMENT_ACTION, $storeId);
    }

    /**
     * @return string
     */
    public function getSecretUrl()
    {
        return $this->urlBuilder->getUrl("amstripe/paymentintents/data");
    }
}
