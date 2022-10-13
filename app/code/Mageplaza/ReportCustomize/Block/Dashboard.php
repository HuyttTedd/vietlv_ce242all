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

namespace Mageplaza\ReportCustomize\Block;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\ReportCustomize\Helper\Data;
use Mageplaza\ReportCustomize\Model\CardsManageFactory;

/**
 * Class Dashboard
 * @package Mageplaza\Reports\Block
 */
class Dashboard extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_ReportCustomize::dashboard.phtml';

    /**
     * @var CardsManageFactory
     */
    protected $_cardsManageFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param CardsManageFactory $cardsManageFactory
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CardsManageFactory $cardsManageFactory,
        Data $helperData,
        array $data = []
    ) {
        $this->_cardsManageFactory = $cardsManageFactory;
        $this->_helperData         = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * @return Template|void
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getCards()
    {
        try {
            $result = $this->_cardsManageFactory->create();
        } catch (Exception $e) {
            $result = [];
            $this->_logger->critical($e);
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getMap()
    {
        return $this->_cardsManageFactory->getMap();
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return 'adminhtml';
    }

    /**
     * @return array
     * @return array
     */
    public function getGridStackConfig()
    {
        $config = [
            'url'         => $this->getUrl('mpcustomize/cards/saveposition', ['form_key' => $this->getFormKey()]),
            'loadCardUrl' => $this->getUrl('mpcustomize/cards/loadcard', ['form_key' => $this->getFormKey()])
        ];

        return $config;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDate()
    {
        return Data::jsonEncode($this->_helperData->getDateRange());
    }
}
