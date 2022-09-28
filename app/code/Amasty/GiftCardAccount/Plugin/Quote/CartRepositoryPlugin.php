<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Plugin\Quote;

use Amasty\GiftCardAccount\Model\GiftCardExtension\Quote\Handlers\ReadHandler;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class CartRepositoryPlugin
{
    /**
     * @var ReadHandler
     */
    private $readHandler;

    public function __construct(
        ReadHandler $readHandler
    ) {
        $this->readHandler = $readHandler;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param CartInterface $quote
     *
     * @return CartInterface
     */
    public function afterGet(CartRepositoryInterface $subject, CartInterface $quote): CartInterface
    {
        $this->readHandler->loadAttributes($quote);

        return $quote;
    }

    /**
     * @param CartRepositoryInterface $subject
     * @param SearchResultsInterface $searchResult
     *
     * @return SearchResultsInterface
     */
    public function afterGetList(
        CartRepositoryInterface $subject,
        SearchResultsInterface $searchResult
    ): SearchResultsInterface {
        $quotes = [];

        foreach ($searchResult->getItems() as $quote) {
            $this->readHandler->loadAttributes($quote);
            $quotes[] = $quote;
        }
        $searchResult->setItems($quotes);

        return $searchResult;
    }
}
