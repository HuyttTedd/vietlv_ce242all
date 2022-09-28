<?php

namespace Amasty\PushNotifications\Model\ResourceModel;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Amasty\PushNotifications\Setup\Operation\CreateSubscriberTable;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\DB\Helper;
use Magento\Framework\DataObject;

class Subscriber extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var Helper
     */
    private $dbHelper;

    /**
     * @var DataObject
     */
    private $associatedQuestionEntityMap;

    /**
     * Question constructor.
     *
     * @param Context $context
     * @param Helper $dbHelper
     * @param DataObject $associatedQuestionEntityMap
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Helper $dbHelper,
        DataObject $associatedQuestionEntityMap,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->associatedQuestionEntityMap = $associatedQuestionEntityMap;
        $this->dbHelper = $dbHelper;
    }

    public function _construct()
    {
        $this->_init(CreateSubscriberTable::TABLE_NAME, SubscriberInterface::SUBSCRIBER_ID);
    }

    /**
     * @param string $entityType
     * @return array
     */
    public function getReferenceConfig($entityType = '')
    {
        return $this->associatedQuestionEntityMap->getData($entityType);
    }
}
