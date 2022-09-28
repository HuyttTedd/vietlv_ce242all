<?php

namespace Amasty\Checkout\Cache\ConditionVariator;

/**
 * Add cache variation for virtual quote.
 */
class IsVirtual implements \Amasty\Checkout\Api\CacheKeyPartProviderInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function __construct(\Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getKeyPart()
    {
        if ($this->checkoutSession->getQuote()->isVirtual()) {
            return 'virtual';
        }

        return 'virtual=fls';
    }
}
