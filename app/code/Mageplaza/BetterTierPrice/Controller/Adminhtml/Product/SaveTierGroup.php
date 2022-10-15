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
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Controller\Adminhtml\Product;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Store\Model\Store;
use Mageplaza\BetterTierPrice\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class SaveTierGroup
 * @package Mageplaza\BetterTierPrice\Controller\Adminhtml\Product
 */
class SaveTierGroup extends Action
{
    /**
     * @var Writer
     */
    protected $writer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Context $context
     * @param Writer $writer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Writer $writer,
        LoggerInterface $logger
    ) {
        $this->writer = $writer;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax() && ($tierGroup = $this->getRequest()->getParam('tierGroup'))) {
            try {
                $this->writer->save(
                    'mp_tier_price/general/tier_group',
                    $tierGroup,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    Store::DEFAULT_STORE_ID
                );

                return $this->getResponse()->representJson(Data::jsonEncode([
                    'error_mes' => false
                ]));
            } catch (Exception $e) {
                $this->logger->critical($e);

                return $this->getResponse()->representJson(Data::jsonEncode([
                    'error_mes' => __('Something went wrong while updating tier group')
                ]));
            }
        }

        return $this->getResponse()->representJson(Data::jsonEncode([
            'error_mes' => __('Something went wrong while updating tier group')
        ]));
    }
}
