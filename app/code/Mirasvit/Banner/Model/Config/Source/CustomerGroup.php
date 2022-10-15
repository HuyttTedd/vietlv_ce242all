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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Model\Config\Source;

use Magento\Customer\Model\Config\Source\Group;
use Magento\Framework\Data\OptionSourceInterface;

class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var Group
     */
    private $group;


    /**
     * CustomerGroup constructor.
     * @param Group $group
     */
    public function __construct(
        Group $group
    ) {
        $this->group = $group;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [
            [
                'label' => 'NOT LOGGED IN',
                'value' => 0,
            ],
        ];

        foreach ($this->group->toOptionArray() as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
