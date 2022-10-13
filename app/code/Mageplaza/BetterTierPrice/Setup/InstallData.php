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

namespace Mageplaza\BetterTierPrice\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mageplaza\BetterTierPrice\Model\Attribute\Backend\SpecificCustomer;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * Install Data constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        /** @var CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $installer->startSetup();

        $catalogSetup->addAttribute(Product::ENTITY, 'mp_specific_customer', [
            'group'                   => 'Advanced Pricing',
            'label'                   => 'Tier Price for Specific customer',
            'type'                    => 'text',
            'input'                   => 'text',
            'backend'                 => SpecificCustomer::class,
            'global'                  => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order'              => 5,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => true,
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => true,
            'unique'                  => false,
            'used_in_product_listing' => true
        ]);

        $catalogSetup->addAttribute(Product::ENTITY, 'mp_tier_group', [
            'group'                   => 'Advanced Pricing',
            'label'                   => 'Tier Group',
            'type'                    => 'text',
            'input'                   => 'text',
            'global'                  => ScopedAttributeInterface::SCOPE_WEBSITE,
            'sort_order'              => 15,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => true,
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'unique'                  => false,
            'used_in_product_listing' => true
        ]);

        $installer->endSetup();
    }
}
