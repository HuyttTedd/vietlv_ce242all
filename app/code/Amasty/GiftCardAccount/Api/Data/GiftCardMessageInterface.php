<?php
declare(strict_types=1);

namespace Amasty\GiftCardAccount\Api\Data;

interface GiftCardMessageInterface
{
    public const TYPE = 'type';
    public const TEXT = 'text';

    /**
     * Getter message type
     * @return string
     */
    public function getType(): string;

    /**
     * Setter message type
     * @param string $type
     * @return $this
     */
    public function setType(string $type): GiftCardMessageInterface;

    /**
     * Getter for text of message
     * @return string
     */
    public function getText(): string;

    /**
     * Setter message text
     * @param string $text
     * @return $this
     */
    public function setText(string $text): GiftCardMessageInterface;
}
