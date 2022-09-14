<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\SampleData\Installer;

use Amasty\GiftCard\Setup\Operation\AddGiftCardAttributes;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class GiftCardAttribute implements InstallerInterface
{
    /**
     * @var AddGiftCardAttributes
     */
    private $addGiftCardAttributes;

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    public function __construct(
        AddGiftCardAttributes $addGiftCardAttributes,
        ModuleDataSetupInterface $setup
    ) {
        $this->addGiftCardAttributes = $addGiftCardAttributes;
        $this->setup = $setup;
    }

    public function install()
    {
        $this->addGiftCardAttributes->execute($this->setup);
    }
}
