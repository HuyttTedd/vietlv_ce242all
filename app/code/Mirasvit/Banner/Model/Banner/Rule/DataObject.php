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



namespace Mirasvit\Banner\Model\Banner\Rule;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Mirasvit\Banner\Model\Banner\Rule\Condition\PageCondition;
use Mirasvit\Banner\Service\AreaContext\RequestContext;

class DataObject extends AbstractModel
{
    private $requestContext;

    private $checkoutSession;

    private $request;

    private $productRepository;

    private $categoryRepository;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        RequestContext $requestContext,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        CheckoutSession $checkoutSession,
        HttpRequest $request,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->requestContext     = $requestContext;
        $this->productRepository  = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->checkoutSession    = $checkoutSession;
        $this->request            = $request;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function init()
    {
        $this->updateQuoteData();

        $product = $this->requestContext->getProduct();
        if ($product) {
            foreach ($product->getData() as $key => $value) {
                $this->setData($key, $value);
            }
        }

        $category = $this->requestContext->getCategory();
        $this->setData('category', $category);

        $uri = $this->requestContext->getUri();
        $this->setData(PageCondition::DATA_URI, $uri);

        $actionName = $this->requestContext->getActionName();
        $this->setData(PageCondition::DATA_ACTION_NAME, $actionName);

        return $this;
    }

    private function updateQuoteData()
    {
        $quote = $this->requestContext->getQuote();

        if (!$quote) {
            return;
        }

        $quote->collectTotals();

        $allItems = [];
        foreach ($quote->getAllItems() as $item) {
            $allItems[] = $item;
        }

        $this->setData('quote', $quote)
            ->setData('all_items', $allItems);
    }
}
