<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Setup\Operation;

use Amasty\GiftCard\Model\Image\ResourceModel\Image;
use Amasty\GiftCard\Model\Image\ResourceModel\ImageElements;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpdateDataTo250
{
    /**
     * @param ModuleDataSetupInterface $setup
     *
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $imageTable = $setup->getTable(Image::TABLE_NAME);
        // declarative schema creates 'amasty_giftcard_image_elements' table.
        $imageElementsTable = $setup->getTable(ImageElements::TABLE_NAME);

        $selectUpdate = $connection->select()
            ->from(
                $imageTable,
                [
                    new \Zend_Db_Expr('NULL as element_id'),
                    'image_id' => 'image_id',
                    new \Zend_Db_Expr('\'code\' as name'),
                    new \Zend_Db_Expr('0 as width'),
                    new \Zend_Db_Expr('0 as height'),
                    'pos_x' => 'code_pos_x',
                    'pos_y' => 'code_pos_y',
                    new \Zend_Db_Expr('NULL as custom_css')
                ]
            );
        $connection->query($connection->insertFromSelect($selectUpdate, $imageElementsTable));

        $connection->dropColumn($imageTable, 'code_pos_x');
        $connection->dropColumn($imageTable, 'code_pos_y');
        $connection->dropColumn($imageTable, 'code_text_color');

        $setup->endSetup();
    }
}
