<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model\GiftCardAccount;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountResponseInterface;
use Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterface;
use Magento\Framework\DataObject;

class Response extends DataObject implements GiftCardAccountResponseInterface
{
    public function getAccount(): GiftCardAccountInterface
    {
        return $this->getData(self::ACCOUNT);
    }

    public function setAccount(GiftCardAccountInterface $account): GiftCardAccountResponseInterface
    {
        return $this->setData(self::ACCOUNT, $account);
    }

    public function getMessages(): ?array
    {
        return $this->_getData(self::MESSAGES);
    }

    public function setMessages(array $messages): GiftCardAccountResponseInterface
    {
        return $this->setData(self::MESSAGES, $messages);
    }

    public function addMessage(GiftCardMessageInterface $message): void
    {
        $messages = $this->getMessages() ?? [];
        $messages = array_merge($messages, [$message]);
        $this->setMessages($messages);
    }
}
