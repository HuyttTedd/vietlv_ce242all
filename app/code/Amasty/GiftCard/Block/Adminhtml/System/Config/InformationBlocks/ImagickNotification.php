<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_GiftCard
*/

declare(strict_types=1);

namespace Amasty\GiftCard\Block\Adminhtml\System\Config\InformationBlocks;

use Amasty\GiftCard\Model\Image\Utils\Factory\ImagickFactory;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

class ImagickNotification extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_GiftCard::config/information/imagick_notification.phtml';

    public function getNotificationText(): Phrase
    {
        return __('We recommend installing the Imagick extension to display images for different email clients '
            . 'properly. If you are not sure how to install the necessary extension on the server correctly, '
            . 'please contact your administrator.');
    }

    protected function _toHtml()
    {
        if ($this->isImagickInstalled()) {
            return '';
        }

        return parent::_toHtml();
    }

    private function isImagickInstalled(): bool
    {
        return extension_loaded(ImagickFactory::EXTENSION);
    }
}
