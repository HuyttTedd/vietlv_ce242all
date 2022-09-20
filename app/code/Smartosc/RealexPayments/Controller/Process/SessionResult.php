<?php

namespace Smartosc\RealexPayments\Controller\Process;

use Magento\Checkout\Model\Session;
use Magento\Framework\Controller\ResultFactory;
use RealexPayments\HPP\Api\RealexPaymentManagementInterface;
use RealexPayments\HPP\Logger\Logger;

/**
 * Class SessionResult
 * @package Smartosc\RealexPayments\Controller\Process
 */
class SessionResult extends \RealexPayments\HPP\Controller\Process\SessionResult
{
    /**
     * @var \RealexPayments\HPP\Helper\Data
     */
    private $_helper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $_order;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var Logger
     */
    private $_logger;

    /**
     * @var Session
     */
    private $_session;

    /**
     * SessionResult constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \RealexPayments\HPP\Helper\Data $helper
     * @param Logger $logger
     * @param Session $session
     * @param ResultFactory $resultFactory
     * @param RealexPaymentManagementInterface $paymentManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \RealexPayments\HPP\Helper\Data $helper,
        \RealexPayments\HPP\Logger\Logger $logger,
        \Magento\Checkout\Model\Session $session,
        ResultFactory $resultFactory,
        RealexPaymentManagementInterface $paymentManagement
    ) {
        $this->_helper = $helper;
        $this->_url = $context->getUrl();
        $this->_logger = $logger;
        $this->_session = $session;
        parent::__construct($context, $helper, $logger, $session, $resultFactory, $paymentManagement);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/logFile.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('a1');
        $response = $this->getRequest()->getParams();
        if (!$this->_validateResponse($response)) {
            $this->messageManager->addErrorMessage(
                __('Your payment was unsuccessful. Please try again or use a different card / payment method.')
            );
            $this->_redirect('eshop/services');

            return;
        }
        $result = boolval($response['result']);
        if ($result) {
            $this->_session->getQuote()
                ->setIsActive(false)
                ->save();
            $this->_redirect('checkout/onepage/success');
        } else {
            $this->_cancel();
            $this->_session->setData(\RealexPayments\HPP\Block\Process\Result\Observe::OBSERVE_KEY, '1');
            $this->messageManager->addErrorMessage(
                __('Your payment was unsuccessful. Please try again or use a different card / payment method.')
            );
            $this->_redirect('eshop/services');
        }
    }

    /**
     * @param $response
     * @return bool|mixed
     */
    private function _validateResponse($response)
    {
        if (!isset($response) || !isset($response['timestamp']) || !isset($response['order_id']) ||
            !isset($response['result']) || !isset($response['hash'])) {
            return false;
        }

        $timestamp = $response['timestamp'];
        $merchantid = $this->_helper->getConfigData('merchant_id');
        $orderid = $response['order_id'];
        $result = $response['result'];
        $hash = $response['hash'];
        $sha1hash = $this->_helper->signFields("$timestamp.$merchantid.$orderid.$result");

        //Check to see if hashes match or not
        if ($sha1hash !== $hash) {
            return false;
        }

        $order = $this->_getOrder($orderid);

        return $order->getId();
    }

    /**
     * Cancel the order and restore the quote.
     */
    private function _cancel()
    {
        // restore the quote
        $this->_session->restoreQuote();

        $this->_helper->cancelOrder($this->_order);
    }

    /**
     * Get order based on increment_id.
     *
     * @param $incrementId
     *
     * @return \Magento\Sales\Model\Order
     */
    private function _getOrder($incrementId)
    {
        if (!$this->_order) {
            $this->_order = $this->_helper->getOrderByIncrement($incrementId);
        }

        return $this->_order;
    }
}
