<?php

declare(strict_types=1);

namespace Amasty\PushNotifications\Model;

use Amasty\PushNotifications\Lib\MobileDetect;

class DeviceDetect
{
    const DESKTOP = 'desktop';
    const TABLET = 'tablet';
    const MOBILE = 'mobile';

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    public function __construct(
        MobileDetect $mobileDetect
    ) {
        $this->mobileDetect = $mobileDetect;
    }

    public function detectDevice(): string
    {
        if ($this->mobileDetect->isTablet()) {
            return self::TABLET;
        }
        if ($this->mobileDetect->isMobile()) {
            return self::MOBILE;
        }

        return self::DESKTOP;
    }
}
