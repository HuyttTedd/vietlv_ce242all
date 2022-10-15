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
 * @package   mirasvit/module-stability
 * @version   1.1.0
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\StabilitySnapshot\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\StabilitySnapshot\Api\Data\SnapshotInterface;

class Snapshot extends AbstractModel implements SnapshotInterface
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Snapshot::class);
    }

    /**
     * @return mixed|string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param string $value
     * @return SnapshotInterface|Snapshot
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @return array|mixed
     */
    public function getEnvironmentData()
    {
        try {
            return \Zend_Json::decode($this->getData(SnapshotInterface::ENVIRONMENT_DATA));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param array $value
     * @return SnapshotInterface|Snapshot
     */
    public function setEnvironmentData($value)
    {
        $value = \Zend_Json::encode($value);
        $this->setEnvironmentHash(sha1($value));

        return $this->setData(SnapshotInterface::ENVIRONMENT_DATA, $value);
    }

    /**
     * @return mixed|string
     */
    public function getEnvironmentHash()
    {
        return $this->getData(self::ENVIRONMENT_HASH);
    }

    /**
     * @param string $value
     * @return SnapshotInterface|Snapshot
     */
    public function setEnvironmentHash($value)
    {
        return $this->setData(self::ENVIRONMENT_HASH, $value);
    }

    /**
     * @return array|mixed
     */
    public function getHealthData()
    {
        try {
            return \Zend_Json::decode($this->getData(SnapshotInterface::HEALTH_DATA));
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param array $value
     * @return SnapshotInterface|Snapshot
     */
    public function setHealthData($value)
    {
        return $this->setData(SnapshotInterface::HEALTH_DATA, \Zend_Json::encode($value));
    }

    /**
     * @return mixed|string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * @param string $value
     * @return SnapshotInterface|Snapshot
     */
    public function setNote($value)
    {
        return $this->setData(self::NOTE, $value);
    }

    /**
     * @return mixed|string
     */
    public function getClosedAt()
    {
        return $this->getData(self::CLOSED_AT);
    }

    /**
     * @param string $value
     * @return SnapshotInterface|Snapshot
     */
    public function setClosedAt($value)
    {
        return $this->setData(self::CLOSED_AT, $value);
    }

    /**
     * @return mixed|string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
}
