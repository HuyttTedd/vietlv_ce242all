<?php

namespace Amasty\Stripe\Controller\Customer;

use Amasty\Stripe\Model\StripeAccountManagement;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;

class AddCard extends \Magento\Framework\App\Action\Action
{
    /**
     * @var StripeAccountManagement
     */
    private $stripeAccountManagement;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        StripeAccountManagement $stripeAccountManagement,
        JsonFactory $jsonFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->stripeAccountManagement = $stripeAccountManagement;
        $this->jsonFactory = $jsonFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $source = $this->getRequest()->getParam('source');
        $explodedSource = explode(":", $source);
        $storeId = (int)$this->storeManager->getStore()->getId();
        $this->stripeAccountManagement->processSaveCard($explodedSource, $storeId);

        $cardsData = $this->stripeAccountManagement->getAllCards($storeId);

        return $this->jsonFactory->create()->setData($cardsData);
    }
}
