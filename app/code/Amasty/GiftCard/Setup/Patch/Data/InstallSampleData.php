<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Patch\Data;

use Amasty\GiftCard\Setup\SampleData\Installer;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InstallSampleData implements DataPatchInterface
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    public function __construct(
        Installer $installer,
        ResourceInterface $moduleResource
    ) {
        $this->installer = $installer;
        $this->moduleResource = $moduleResource;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply(): void
    {
        $setupDataVersion = $this->moduleResource->getDataVersion('Amasty_GiftCard');

        // Check if module was already installed or not.
        // If setup_version present in DB then we don't need to install fixtures, because setup_version is a marker.
        if (!$setupDataVersion) {
            $this->installer->install();
        }
    }
}
