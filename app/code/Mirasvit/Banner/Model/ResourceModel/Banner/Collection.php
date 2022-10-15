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



namespace Mirasvit\Banner\Model\ResourceModel\Banner;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{

    private $date;

    public function __construct(
        TimezoneInterface $date,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager
    ) {
        $this->date = $date;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            \Mirasvit\Banner\Model\Banner::class,
            \Mirasvit\Banner\Model\ResourceModel\Banner::class
        );
    }

    /**
     * @return $this
     */
    public function addDateFilter()
    {
        $now = $this->date->date()->format('Y-m-d');

        $this->getSelect()->where(
            'active_from is null or active_from <= ?',
            $now
        )->where(
            'active_to is null or active_to >= ?',
            $now
        );

        return $this;
    }

    /**
     * @param int $customerGroupId
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        $this->getSelect()->where('FIND_IN_SET(' . $customerGroupId . ', customer_group_ids)');

        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->getSelect()->where('FIND_IN_SET(' . $storeId . ', store_ids) or store_ids = 0');

        return $this;
    }

    /**
     * @param int $placeholderId
     * @return $this
     */
    public function addPlaceholderFilter($placeholderId)
    {
        $this->getSelect()->where('FIND_IN_SET(' . $placeholderId . ', placeholder_ids)');

        return $this;
    }
}
