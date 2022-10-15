<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-stability
 * @version   1.1.0
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\StabilityError\Ui\Error\Source;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;

class TypeSource implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => ErrorInterface::TYPE_PHP_EXCEPTION,
                'label' => __('PHP Exception'),
            ],
            [
                'value' => ErrorInterface::TYPE_PHP_ERROR,
                'label' => __('Magento Error'),
            ],
            [
                'value' => ErrorInterface::TYPE_JS_ERROR,
                'label' => __('JavaScript Error'),
            ],
        ];
    }
}
