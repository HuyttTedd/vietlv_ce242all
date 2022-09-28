<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Observer;

use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\Handlers\SaveHandler;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSaveAfter implements ObserverInterface
{
    /**
     * @var SaveHandler
     */
    private $saveHandler;

    public function __construct(
        SaveHandler $saveHandler
    ) {
        $this->saveHandler = $saveHandler;
    }

    public function execute(Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $this->saveHandler->saveAttributes($quote);
    }
}
