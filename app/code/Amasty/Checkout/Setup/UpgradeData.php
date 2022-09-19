<?php

namespace Amasty\Checkout\Setup;

use Amasty\Checkout\Setup\Operation\DesignUseDefaultMigration;
use Amasty\Checkout\Setup\Operation\LayoutBuilderDataMigration;
use Amasty\Checkout\Setup\Operation\AutocompleteConfigMigration;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\State;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Amasty\Checkout\Setup\Operation\ConfigDataRegroup;

/**
 * UpgradeData For Database
 *
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Operation\UpgradeDataTo203
     */
    private $upgradeDataTo203;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ConfigDataRegroup
     */
    private $configRegroup;

    /**
     * @var LayoutBuilderDataMigration
     */
    private $layoutBuilderDataMigration;

    /**
     * @var DesignUseDefaultMigration
     */
    private $designUseDefaultMigration;

    /**
     * @var AutocompleteConfigMigration
     */
    private $autocompleteConfigMigration;

    public function __construct(
        Operation\UpgradeDataTo203 $upgradeDataTo203,
        State $appState,
        ConfigDataRegroup $configRegroup,
        LayoutBuilderDataMigration $layoutBuilderDataMigration,
        DesignUseDefaultMigration $designUseDefaultMigration,
        AutocompleteConfigMigration $autocompleteConfigMigration
    ) {
        $this->upgradeDataTo203 = $upgradeDataTo203;
        $this->appState = $appState;
        $this->configRegroup = $configRegroup;
        $this->layoutBuilderDataMigration = $layoutBuilderDataMigration;
        $this->designUseDefaultMigration = $designUseDefaultMigration;
        $this->autocompleteConfigMigration = $autocompleteConfigMigration;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(
            FrontNameResolver::AREA_CODE,
            [$this, 'upgradeDataWithEmulationAreaCode'],
            [$setup, $context]
        );
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgradeDataWithEmulationAreaCode(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $this->upgradeDataTo203->execute();
        }

        if (version_compare($context->getVersion(), '2.10.0', '<')) {
            $this->configRegroup->execute();
        }

        if (version_compare($context->getVersion(), '3.0.0', '<')) {
            $this->designUseDefaultMigration->execute($setup);
            $this->layoutBuilderDataMigration->execute($setup);
        }

        if (version_compare($context->getVersion(), '3.1.3', '<')) {
            $this->autocompleteConfigMigration->execute($setup);
        }

        $setup->endSetup();
    }
}
