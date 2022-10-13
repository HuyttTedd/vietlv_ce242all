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



namespace Mirasvit\Banner\Block\Widget;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;

class Placeholder extends Template implements BlockInterface, IdentityInterface
{

    /**
     * @return mixed
     */
    public function getPlaceholderId()
    {
        return $this->getData(PlaceholderInterface::ID);
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [];
    }

    public function toHtml()
    {
        return $this->_layout->createBlock(\Mirasvit\Banner\Block\Placeholder::class)
            ->setData(PlaceholderInterface::ID, $this->getPlaceholderId())
            ->toHtml();
    }
}
