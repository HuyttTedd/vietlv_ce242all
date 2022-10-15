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



namespace Mirasvit\Banner\Service\AreaContext;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Mirasvit\Banner\Model\Banner\Rule\Condition\PageCondition;

class RequestContext extends \Magento\Framework\DataObject
{
    private $categoryRepository;

    private $checkoutSession;

    private $productRepository;

    private $registry;

    private $request;

    public function __construct(
        CategoryRepository $categoryRepository,
        CheckoutSession $checkoutSession,
        ProductRepository $productRepository,
        Registry $registry,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($data);

        $this->categoryRepository = $categoryRepository;
        $this->checkoutSession    = $checkoutSession;
        $this->productRepository  = $productRepository;
        $this->request            = $request;
        $this->registry           = $registry;
    }

    /**
     * @return Quote|null
     */
    public function getQuote()
    {
        if (!$this->hasData('quote')) {
            try {
                $this->setData('quote', $this->checkoutSession->getQuote());
            } catch (\Exception $e) {}
        }

        return $this->getData('quote');
    }

    public function setQuote(Quote $quote)
    {
        $this->setData('quote', $quote);

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        if (!$this->hasData('category')) {

            $category = $this->registry->registry('current_category');
            if (!$category && $this->request->getParam('category_id')) {
                try {
                    $category = $this->categoryRepository->get($category);
                } catch (\Exception $e) {}
            }

            $this->setData('category', $category);
        }

        return $this->getData('category');
    }

    public function setCategory(Category $category)
    {
        $this->setData('category', $category);

        return $this;
    }

    /**
     * @return null|Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {

            $product = $this->registry->registry('current_product');
            if (!$product && $this->request->getParam('product_id')) {
                try {
                    $product = $this->productRepository->getById($this->request->getParam('product_id'));
                } catch (\Exception $e) {}
            }

            $this->setData('product', $product);
        }

        return $this->getData('product');
    }

    public function setProduct(Product $product)
    {
        $this->setData('product', $product);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUri()
    {
        if (!$this->hasData(PageCondition::DATA_URI)) {
            $this->setData(PageCondition::DATA_URI, (string)$this->request->getUri());
        }

        return $this->getData(PageCondition::DATA_URI);
    }

    /**
     * @param string $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this->setData(PageCondition::DATA_URI, $uri);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getActionName()
    {
        if (!$this->hasData(PageCondition::DATA_ACTION_NAME)) {
            $this->setData(PageCondition::DATA_ACTION_NAME, (string)$this->request->getFullActionName());
        }

        return $this->getData(PageCondition::DATA_ACTION_NAME);
    }

    /**
     * @param string $uri
     * @return $this
     */
    public function setActionName($uri)
    {
        $this->setData(PageCondition::DATA_ACTION_NAME, $uri);

        return $this;
    }
}
