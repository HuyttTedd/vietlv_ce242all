<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\CartAction\Response\Builder\AddToCart;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountResponseInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterfaceFactory;
use Amasty\GiftCardAccount\Model\GiftCardAccount\CartAction\Response\Builder\BuilderInterface;
use Magento\Framework\Message\MessageInterface;

class SuccessMessage implements BuilderInterface
{
    /**
     * @var GiftCardMessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * @param GiftCardMessageInterfaceFactory $messageFactory
     */
    public function __construct(GiftCardMessageInterfaceFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    public function build(
        GiftCardAccountInterface $account,
        GiftCardAccountResponseInterface $response
    ): void {
        $message = $this->messageFactory->create();
        $message->setType(MessageInterface::TYPE_SUCCESS);
        $message->setText(__('Gift Card "%1" was added.', $account->getCodeModel()->getCode())->render());

        $response->addMessage($message);
    }
}
