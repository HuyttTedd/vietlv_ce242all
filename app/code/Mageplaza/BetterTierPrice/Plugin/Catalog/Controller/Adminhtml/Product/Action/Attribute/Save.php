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
 * @category  Mageplaza
 * @package   Mageplaza_BetterTierPrice
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product\Action\Attribute;

use Exception;
use Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save as SaveAttribute;
use Magento\Catalog\Helper\Product\Edit\Action\Attribute;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;

/**
 * Class Save
 * @package Mageplaza\BetterTierPrice\Plugin\Catalog\Controller\Adminhtml\Product\Action\Attribute
 */
class Save
{
    /**
     * @var Attribute
     */
    private $attributeHelper;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Action
     */
    private $productAction;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Save constructor.
     *
     * @param Attribute $attributeHelper
     * @param ProductFactory $productFactory
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param ResourceConnection $resource
     * @param Action $productAction
     * @param Data $helperData
     */
    public function __construct(
        Attribute $attributeHelper,
        ProductFactory $productFactory,
        RequestInterface $request,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        ResourceConnection $resource,
        Action $productAction,
        Data $helperData
    ) {
        $this->attributeHelper = $attributeHelper;
        $this->productFactory  = $productFactory;
        $this->request         = $request;
        $this->messageManager  = $messageManager;
        $this->storeManager    = $storeManager;
        $this->productAction   = $productAction;
        $this->helperData      = $helperData;
        $this->resource        = $resource;
    }

    /**
     * @param SaveAttribute $save
     *
     * @throws NoSuchEntityException
     */
    public function beforeExecute(SaveAttribute $save)
    {
        if ($this->_validateProducts() && $this->helperData->isEnabled()) {
            $productIds            = $this->attributeHelper->getProductIds();
            $product               = $this->request->getParam('product');
            $tierPriceData         = $this->request->getParam('mpTierPriceData')
                                        ? Data::jsonDecode($this->request->getParam('mpTierPriceData')) : [];
            $specificCustomersData = $this->request->getParam('mpSpecificCustomer') ?: '';
            $mpSpecificCustomer    = json_decode($specificCustomersData, true);

            if (is_array($mpSpecificCustomer) && $mpSpecificCustomer) {
                foreach ($mpSpecificCustomer as &$data) {
                    if ($data['value_type'] === ProductPriceOptions::VALUE_FIXED && !$data['price']) {
                        $data['price'] = 0;
                    }
                }

                $storeId = $this->attributeHelper->getSelectedStoreId();

                if (isset($product['mp_specific_customer']['specific_customer_change_checkbox'])) {
                    $this->productAction->updateAttributes(
                        $productIds,
                        ['mp_specific_customer' => json_encode($mpSpecificCustomer)],
                        $this->getWebsiteId($storeId)
                    );
                }
            }

            if (isset($product['tier_price']['customer_group_change_checkbox'])) {
                foreach ($productIds as $productId) {
                    $this->massUpdateTierPrice((int) $productId, $tierPriceData);
                }
            }
        }
    }

    /**
     * @param int $productId
     * @param array $tierPriceData
     */
    public function massUpdateTierPrice($productId, $tierPriceData)
    {
        $tableName  = $this->resource->getTableName('catalog_product_entity_tier_price');
        $connection = $this->resource->getConnection();

        if (!$tierPriceData) {
            $select = $connection->select()->from($tableName);
            $where  = [
                'entity_id = ?' => $productId
            ];

            foreach ($where as $whereKey => $whereValue) {
                $select->where($whereKey, $whereValue);
            }
            if ($tierPrices = $connection->fetchAll($select)) {
                try {
                    foreach ($tierPrices as $tierPrice) {
                        $connection->delete($tableName, ['value_id = ?' => $tierPrice['value_id']]);
                    }
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
        }

        foreach ($tierPriceData as $tierPriceItem) {
            $select = $connection->select()->from($tableName);
            $where  = [
                'entity_id = ?'         => $productId,
                'website_id = ?'        => $tierPriceItem['website_id'],
                'qty = ?'               => $tierPriceItem['price_qty'],
                'customer_group_id = ?' => $tierPriceItem['cust_group'] === '32000'
                    ? 0 : $tierPriceItem['cust_group']
            ];

            switch ($tierPriceItem['value_type']) {
                case ProductPriceOptions::VALUE_PERCENT:
                    $data['percentage_value']  = $tierPriceItem['percentage_value'];
                    $data['value']             = 0;
                    $data['mp_discount_fixed'] = 0;
                    break;
                case ProductPriceOptions::DISCOUNT_FIXED:
                    $data['mp_discount_fixed'] = $tierPriceItem['mp_discount_fixed'];
                    $data['value']             = 0;
                    $data['percentage_value']  = 0;
                    break;
                default:
                    $data['value']             = $tierPriceItem['price'];
                    $data['mp_discount_fixed'] = null;
                    $data['percentage_value']  = null;
                    break;
            }

            foreach ($where as $whereKey => $whereValue) {
                $select->where($whereKey, $whereValue);
            }

            if ($rowData = $connection->fetchRow($select)) {
                try {
                    $connection->delete($tableName, ['value_id = ?' => $rowData['value_id']]);
                } catch (Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            }
            $data['entity_id']         = (int) $productId;
            $data['website_id']        = $tierPriceItem['website_id'];
            $data['qty']               = $tierPriceItem['price_qty'];
            $data['customer_group_id'] = $tierPriceItem['cust_group'] === '32000'
                ? 0 : $tierPriceItem['cust_group'];

            try {
                $connection->insert($tableName, $data);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
    }

    /**
     * Validate selection of products for mass update
     *
     * @return boolean
     */
    protected function _validateProducts()
    {
        $productIds = $this->attributeHelper->getProductIds();
        if (!is_array($productIds)) {
            return false;
        }

        if (!$this->productFactory->create()->isProductsHasSku($productIds)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $storeId
     *
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteId($storeId)
    {
        if ($this->storeManager->hasSingleStore() || count($this->storeManager->getWebsites()) < 2) {
            return 0;
        }

        return $this->storeManager->getStore($storeId)->getWebsiteId();
    }
}
