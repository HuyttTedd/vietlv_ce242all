<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\SampleData\Installer;

use Amasty\GiftCard\Setup\Operation\InstallImageData;
use Magento\Framework\Setup\SampleData\InstallerInterface;

class ImageTemplate implements InstallerInterface
{
    /**
     * @var InstallImageData
     */
    private $installImageData;

    public function __construct(
        InstallImageData $installImageData
    ) {
        $this->installImageData = $installImageData;
    }

    public function install()
    {
        $this->installImageData->addImageTemplates();
    }
}
