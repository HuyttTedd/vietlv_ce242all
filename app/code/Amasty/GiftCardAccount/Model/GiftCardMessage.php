<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Model;

use Amasty\GiftCardAccount\Api\Data\GiftCardMessageInterface;
use Magento\Framework\DataObject;

class GiftCardMessage extends DataObject implements GiftCardMessageInterface
{
    public function getType(): string
    {
        return (string)$this->getData(GiftCardMessageInterface::TYPE);
    }

    public function setType(string $type): GiftCardMessageInterface
    {
        return $this->setData(GiftCardMessageInterface::TYPE, $type);
    }

    public function getText(): string
    {
        return (string)$this->getData(GiftCardMessageInterface::TEXT);
    }

    public function setText(string $text): GiftCardMessageInterface
    {
        return $this->setData(GiftCardMessageInterface::TEXT, $text);
    }
}
