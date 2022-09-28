<?php

namespace Amasty\PushNotifications\Model\Builder;

use Amasty\PushNotifications\Exception\NotificationException;

interface BuilderInterface
{
    /**
     * @param array $params
     * @return array|string
     * @throws NotificationException
     */
    public function build(array $params);
}
