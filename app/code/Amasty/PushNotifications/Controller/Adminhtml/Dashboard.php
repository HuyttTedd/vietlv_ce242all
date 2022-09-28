<?php

namespace Amasty\PushNotifications\Controller\Adminhtml;

abstract class Dashboard extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_PushNotifications::notifications_dashboard';
}
