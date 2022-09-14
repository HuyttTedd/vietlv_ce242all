<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\Utils\Factory;

use Magento\Framework\ObjectManagerInterface;

class ImagickFactory
{
    public const EXTENSION = 'imagick';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string
     */
    private $instanceName = '\\Imagick';

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(array $data = []): ?\Imagick
    {
        if (!extension_loaded(self::EXTENSION)) {
            return null;
        }

        return $this->objectManager->create($this->instanceName, $data);
    }
}
