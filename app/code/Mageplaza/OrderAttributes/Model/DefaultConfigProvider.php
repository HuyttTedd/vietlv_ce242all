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

namespace Mageplaza\OrderAttributes\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\OrderAttributes\Helper\Data;

/**
 * Class DefaultConfigProvider
 * @package Mageplaza\OrderAttributes\Model
 */
class DefaultConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var ResourceModel\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * DefaultConfigProvider constructor.
     *
     * @param ResourceModel\Attribute\CollectionFactory $collectionFactory
     * @param Data $dataHelper
     */
    public function __construct(
        ResourceModel\Attribute\CollectionFactory $collectionFactory,
        Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        if (!$this->dataHelper->isEnabled()) {
            return [];
        }

        return ['mpOaConfig' => $this->getAttributeData()];
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getAttributeData()
    {
        $data = [
            'isOscPage' => $this->dataHelper->isOscPage(),
            'attributeDepend' => [],
            'shippingDepend' => [],
            'contentType' => [],
            'tinymceConfig' => $this->dataHelper->getTinymceConfig()
        ];

        $attributes = $this->dataHelper->getFilteredAttributes();
        foreach ($attributes as $attribute) {
            $frontendInput = $attribute->getFrontendInput();

            if ($attribute->getFieldDepend() || in_array($frontendInput, ['select', 'select_visual', 'boolean'])) {
                $data['attributeDepend'][] = $attribute->getData();
            }

            if ($attribute->getShippingDepend()) {
                $data['shippingDepend'][] = $attribute->getData();
            }

            if ($frontendInput === 'textarea_visual') {
                $data['contentType'][] = $attribute->getData();
            }
        }

        return $data;
    }
}
