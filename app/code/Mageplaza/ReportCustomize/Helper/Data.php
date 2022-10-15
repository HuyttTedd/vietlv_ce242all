<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Reports
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ReportCustomize\Helper;

use DateInterval;
use DatePeriod;
use DateTimeZone;
use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;

/**
 * Class Data
 * @package Mageplaza\Reports\Helper
 */
class Data extends AbstractData
{

    /**
     * @var CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var array
     */
    protected $lifetimeSales = [];

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $orderCollectionFactory
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CollectionFactory $orderCollectionFactory,
        DateTime $dateTime,
        TimezoneInterface $timezone
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_dateTime               = $dateTime;
        $this->_timezone               = $timezone;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param      $startDate
     * @param null $endDate
     * @param null $isConvertToLocalTime
     *
     * @param null $format
     *
     * @return array
     * @throws Exception
     */
    public function getDateTimeRangeFormat($startDate, $endDate = null, $isConvertToLocalTime = null, $format = null)
    {
        $endDate   = (new \DateTime($endDate ?: $startDate, new DateTimeZone($this->getTimezone())))->setTime(
            23,
            59,
            59
        );
        $startDate = (new \DateTime($startDate, new DateTimeZone($this->getTimezone())))->setTime(00, 00, 00);

        if ($isConvertToLocalTime) {
            $startDate->setTimezone(new DateTimeZone('UTC'));
            $endDate->setTimezone(new DateTimeZone('UTC'));
        }

        return [$startDate->format($format ?: 'Y-m-d H:i:s'), $endDate->format($format ?: 'Y-m-d H:i:s')];
    }

    /**
     * @return array|mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * @param null $format
     *
     * @return array
     */
    public function getDateRange($format = null)
    {
        try {
            if ($dateRange = $this->_request->getParam('dateRange')) {
                $startDate        = $format ? $this->formatDate($format, $dateRange[0]) : $dateRange[0];
                $endDate          = $format ? $this->formatDate($format, $dateRange[1]) : $dateRange[1];
                $compareStartDate = null;
                $compareEndDate   = null;
            } else {
                [$startDate, $endDate] = $this->getDateTimeRangeFormat('-1 month', 'now', null, $format);
                $days = date('z', strtotime($endDate) - strtotime($startDate));
                [$compareStartDate, $compareEndDate] = $this->getDateTimeRangeFormat(
                    '-1 month -' . ($days + 1) . ' day',
                    '-1 month -1 day',
                    null,
                    $format
                );
            }
        } catch (Exception $e) {
            $this->_logger->critical($e);

            return [null, null, null, null];
        }

        return [$startDate, $endDate, $compareStartDate, $compareEndDate];
    }

    /**
     * @param $format
     * @param $date
     *
     * @return string
     * @throws Exception
     */
    public function formatDate($format, $date)
    {
        return (new \DateTime($date))->format($format);
    }
}
