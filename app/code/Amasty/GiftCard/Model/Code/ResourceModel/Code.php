<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Model\Code\ResourceModel;

use Amasty\GiftCard\Api\Data\CodeInterface;
use Amasty\GiftCard\Model\OptionSource\Status;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Code extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_code';
    public const MAX_INSERT_CHUNK = 10000;

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CodeInterface::CODE_ID);
    }

    /**
     * @param array $codesData
     * @return void
     * @throws \Exception
     */
    public function insertMultipleCodes(array $codesData): void
    {
        $insertChunks = array_chunk($codesData, self::MAX_INSERT_CHUNK);
        try {
            $this->beginTransaction();
            foreach ($insertChunks as $chunk) {
                $this->getConnection()->insertMultiple(
                    $this->getMainTable(),
                    $chunk
                );
            }
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack(); //rollback everything even if single chunk is failed
            throw $e;
        }
    }

    /**
     * @param int $codePoolId
     * @return void
     * @throws \Exception
     */
    public function deleteAllAvailableCodesByCodePoolId(int $codePoolId): void
    {
        try {
            $this->beginTransaction();
            $connection = $this->getConnection();
            $whereConditions = [
                $connection->quoteInto(CodeInterface::CODE_POOL_ID . '= ?', $codePoolId),
                $connection->quoteInto(CodeInterface::STATUS . ' = ?', Status::AVAILABLE)
            ];
            $connection->delete(
                $this->getMainTable(),
                $whereConditions
            );
            $this->commit();
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }
}
