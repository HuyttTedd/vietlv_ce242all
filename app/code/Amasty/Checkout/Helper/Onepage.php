<?php

namespace Amasty\Checkout\Helper;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Amasty\Checkout\Model\Config;

class Onepage extends AbstractHelper
{
    const ONE_COLUMN = '1column';
    const TWO_COLUMNS = '2columns';
    const THREE_COLUMNS = '3columns';

    /**
     * @var Config
     */
    private $configProvider;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    public function __construct(
        Context $context,
        Config $configProvider,
        CheckoutSession $checkoutSession
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->configProvider->getTitle();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->configProvider->getDescription();
    }

    /**
     * @return bool
     */
    public function isModernCheckoutDesign()
    {
        return (bool)$this->configProvider->getCheckoutDesign();
    }

    /**
     * @return string
     */
    public function getLayoutTemplate()
    {
        if ($this->isModernCheckoutDesign()) {
            return $this->configProvider->getLayoutModernTemplate();
        }

        return $this->configProvider->getLayoutTemplate();
    }

    /**
     * @return string
     */
    public function getDesignLayout()
    {
        if (!$this->checkoutSession->getQuote()->isVirtual()) {
            return $this->getLayoutTemplate();
        }

        return self::TWO_COLUMNS;
    }
}
