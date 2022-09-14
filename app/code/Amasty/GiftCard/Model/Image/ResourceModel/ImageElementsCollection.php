<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Image\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ImageElementsCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\GiftCard\Model\Image\ImageElements::class,
            \Amasty\GiftCard\Model\Image\ResourceModel\ImageElements::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
