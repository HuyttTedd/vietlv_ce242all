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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Controller\Adminhtml\SalesOrderCreate;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session\Quote;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\QuoteFactory;

/**
 * Class Index
 * @package Mageplaza\OrderAttributes\Controller\Adminhtml\SalesOrderCreate
 */
class Index extends Action
{
    /**
     * @var string
     */
    protected $scope = 'mpOrderAttributes';

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var Quote
     */
    protected $quote;

    /**
     * @param Context $context
     * @param QuoteFactory $quoteFactory
     * @param Data $data
     * @param Quote $quote
     */
    public function __construct(
        Context $context,
        QuoteFactory $quoteFactory,
        Data $data,
        Quote $quote
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->data = $data;
        $this->quote = $quote;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->_request->getPost($this->scope);

        if ($this->data->isEnabled($this->quote->getStoreId()) && $data) {
            foreach ($data as &$datum) {
                if (is_array($datum)) {
                    $datum = implode(',', $datum);
                }
            }

            $this->quoteFactory->create()->saveAttributeData($this->quote->getQuoteId(), $data);
        }
    }
}
