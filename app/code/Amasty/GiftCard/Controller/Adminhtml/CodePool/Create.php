<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Controller\Adminhtml\CodePool;

use Amasty\GiftCard\Controller\Adminhtml\AbstractCodePool;
use Magento\Framework\Controller\ResultFactory;

class Create extends AbstractCodePool
{
    /**
     * @return void
     */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $result->forward('edit');
    }
}
