<?php

namespace Amasty\PushNotifications\Model\Api;

use Amasty\PushNotifications\Exception\NotificationException;

interface SenderInterface
{
    /**
     * @param array $params
     * @param int|null $storeId
     *
     * @return array
     *
     * @throws NotificationException
     */
    public function send(array $params, $storeId = null);
}
