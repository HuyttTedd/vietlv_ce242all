<?php
declare(strict_types=1);

namespace Amasty\Checkout\Setup\Operation;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AutocompleteConfigMigration
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(WriterInterface $configWriter)
    {
        $this->configWriter = $configWriter;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    public function execute(ModuleDataSetupInterface $setup): void
    {
        $pathsToReplace = [
            'amasty_checkout/geolocation/google_address_suggestion'
                => 'amasty_address_autocomplete/general/google_address_suggestion',
            'amasty_checkout/geolocation/google_api_key' => 'amasty_address_autocomplete/general/google_api_key'
        ];

        $select = $setup->getConnection()->select()
            ->from($setup->getTable('core_config_data'), ['scope', 'scope_id', 'path', 'value'])
            ->where('path in (?)', array_keys($pathsToReplace));

        $rows = $setup->getConnection()->fetchAll($select);

        foreach ($rows as $row) {
            $this->configWriter->save($pathsToReplace[$row['path']], $row['value'], $row['scope'], $row['scope_id']);
            $this->configWriter->delete($row['path'], $row['scope'], $row['scope_id']);
        }
    }
}
