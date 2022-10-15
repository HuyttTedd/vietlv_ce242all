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
 * @package     Mageplaza_ReportCustomize
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ReportCustomize\Block\Dashboard;

use Magento\Backend\Block\Template;

/**
 * Class Dashboard
 * @package Mageplaza\ReportCustomize\Block
 */
class Card extends Template
{

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_ReportCustomize::dashboard/card.phtml';

    /**
     * Dashboard constructor.
     *
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getCard()
    {
        return $this->getData('small_card');
    }

    /**
     * @param $card
     *
     * @return Card
     */
    public function setCard($card)
    {
        return $this->setData('small_card', $card);
    }

    /**
     * @return string
     */
    public function getArea()
    {
        return 'adminhtml';
    }
}
