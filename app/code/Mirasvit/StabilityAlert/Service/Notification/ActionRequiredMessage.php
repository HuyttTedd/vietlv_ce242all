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



namespace Mirasvit\StabilityAlert\Service\Notification;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;
use Mirasvit\StabilityAlert\Api\Repository\AlertRepositoryInterface;

class ActionRequiredMessage implements MessageInterface
{
    /**
     * @var AlertRepositoryInterface
     */
    private $alertRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ActionRequiredMessage constructor.
     * @param AlertRepositoryInterface $alertRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        AlertRepositoryInterface $alertRepository,
        UrlInterface $urlBuilder
    ) {
        $this->alertRepository = $alertRepository;
        $this->urlBuilder      = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayed()
    {
        if ($this->getText()) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return sha1(__CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        $text = [];

        foreach ($this->alertRepository->getAlertPool() as $alert) {
            if ($alert->getStatus() == AlertInterface::STATUS_ERROR) {
                $text[] = $alert->getName() . ': <i>' . $alert->getDescription() . '</i>';
            }
        }

        if ($text) {
            $url    = $this->urlBuilder->getUrl("stability/dashboard/index");
            $text[] = "<a href='$url'>" . __('Open Health Monitoring Suite') . "</a>";

            $text = implode('<br>', $text);

            return $text;
        }

        return '';
    }
}
