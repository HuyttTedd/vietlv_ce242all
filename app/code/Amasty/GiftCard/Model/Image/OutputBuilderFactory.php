<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Model\Image\OutputBuilders\OutputBuilderInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;

class OutputBuilderFactory
{
    public const UI_BUILDER = 'ui';
    public const HTML_BUILDER = 'html';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $builderClasses;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $builderClasses = []
    ) {
        $this->objectManager = $objectManager;
        $this->builderClasses = $builderClasses;
    }

    public function create(string $type, array $args = []): OutputBuilderInterface
    {
        if (!isset($this->builderClasses[$type])) {
            throw new NotFoundException(
                __('The "%1" builder type isn\'t defined. Verify the builder and try again.', $type)
            );
        }

        $builder = $this->objectManager->create($this->builderClasses[$type], $args);
        if (!$builder instanceof OutputBuilderInterface) {
            throw new \InvalidArgumentException(
                'The builder instance "' . $type . '" must implement '
                . OutputBuilderInterface::class
            );
        }

        return $builder;
    }
}
