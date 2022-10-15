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

namespace Mageplaza\ReportCustomize\Block\Dashboard;

use Magento\Backend\Block\Template;
use Magento\Directory\Model\Currency;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\Reports\Helper\Data;

/**
 * Class AbstractClass
 * @package Mageplaza\ReportCustomize\Block\Dashboard
 * @method setArea(string $string)
 */
abstract class AbstractClass extends Template
{
    const NAME              = '';
    const MAGE_REPORT_CLASS = '';

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var Currency
     */
    protected $baseCurrency;

    /**
     * @var string Price Format
     */
    protected $basePriceFormat;

    /**
     * @var Currency|null
     */

    /**
     * @var
     */
    protected $_currency;

    /**
     * AbstractClass constructor.
     *
     * @param Template\Context $context
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->setArea('adminhtml');
        $this->_helperData = $helperData;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function getContentHtml()
    {
        if (static::MAGE_REPORT_CLASS) {
            return $this->getLayout()->createBlock(static::MAGE_REPORT_CLASS)->setArea('adminhtml')
                ->toHtml();
        }

        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @return string
     */
    public function getDetailUrl()
    {
        return '';
    }

    /**
     * @param $date
     * @param null $endDate
     *
     * @return int
     * @SuppressWarnings(Unused)
     * @throws Exception
     */
    protected function getDataByDate($date, $endDate = null)
    {
        return random_int(1, 10);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getDateRange()
    {
        return $this->_helperData->getDateRange();
    }

    /**
     * @return string
     */
    protected function getYLabel()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getYUnit()
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function isEnabledChart()
    {
        return $this->_helperData->isEnabledChart();
    }

    /**
     * @return bool
     */
    public function isCompare()
    {
        return $this->_helperData->isCompare();
    }

    /**
     * @return bool
     */
    protected function isFill()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return false;
    }
}
