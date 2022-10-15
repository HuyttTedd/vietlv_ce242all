<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Service;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Cms\Model\Template\FilterProvider as CmsFilterProvider;
use Magento\Email\Model\TemplateFactory as EmailTemplateFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class ContentService
{
    private $filterProvider;

    private $templateFactory;

    private $checkoutSession;

    private $currency;

    private $scopeConfig;

    public function __construct(
        CmsFilterProvider $filterProvider,
        EmailTemplateFactory $templateFactory,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $currency,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->filterProvider  = $filterProvider;
        $this->templateFactory = $templateFactory;
        $this->checkoutSession = $checkoutSession;
        $this->currency        = $currency;
        $this->scopeConfig     = $scopeConfig;
    }

    /**
     * @param string $html
     *
     * @return string
     */
    public function processHtmlContent($html)
    {
        $quote = $this->checkoutSession->getQuote();

        $subtotal             = $quote->getSubtotal();
        $freeShippingSubtotal = $this->getFreeShippingSubtotal();
        $freeShippingLeft     = $freeShippingSubtotal - $subtotal;

        $variables = [
            'cartSubtotal'         => $this->currency->convertAndFormat($subtotal),
            'freeShippingSubtotal' => $this->currency->convertAndFormat($freeShippingSubtotal),
            'freeShippingLeft'     => $this->currency->convertAndFormat($freeShippingLeft),
        ];

        $template = $this->templateFactory->create();
        $template->setTemplateText($html)
            ->setIsPlain(false);
        $template->setTemplateFilter($this->filterProvider->getPageFilter());
        $html = $template->getProcessedTemplate($variables);

        return $html;
    }

    private function getFreeShippingSubtotal()
    {
        $store = $this->checkoutSession->getQuote()->getStore();

        return (float)$this->scopeConfig->getValue('carriers/freeshipping/free_shipping_subtotal',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $store->getWebsiteId()
        );
    }
}
