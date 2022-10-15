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



namespace Mirasvit\Banner\Service;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\SessionManager;
use Mirasvit\Banner\Api\Data\AnalyticsInterface;

class AnalyticsService
{
    private $resource;

    private $sessionManager;

    private $remoteAddress;

    public function __construct(
        ResourceConnection $resource,
        SessionManager $sessionManager,
        RemoteAddress $remoteAddress
    ) {
        $this->resource       = $resource;
        $this->sessionManager = $sessionManager;
        $this->remoteAddress  = $remoteAddress;
    }

    public function getSessionId()
    {
        return $this->sessionManager->getSessionId();
    }

    public function getRemoteAddr()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @param int $bannerId
     *
     * @return int
     */
    public function getImpression($bannerId)
    {
        return $this->getValue($bannerId, AnalyticsInterface::ACTION_IMPRESSION);
    }

    /**
     * @param int $bannerId
     *
     * @return int
     */
    public function getClicks($bannerId)
    {
        return $this->getValue($bannerId, AnalyticsInterface::ACTION_CLICK);
    }

    /**
     * @param int    $bannerId
     * @param string $action
     *
     * @return int
     */
    private function getValue($bannerId, $action)
    {
        $select = $this->resource->getConnection()->select();
        $select->from(
            $this->resource->getTableName(AnalyticsInterface::TABLE_NAME),
            [new \Zend_Db_Expr('SUM(' . AnalyticsInterface::VALUE . ')')]
        )
            ->where(AnalyticsInterface::BANNER_ID . ' = ?', $bannerId)
            ->where(AnalyticsInterface::ACTION . ' = ?', $action);

        return $this->resource->getConnection()->fetchOne($select);
    }
}
