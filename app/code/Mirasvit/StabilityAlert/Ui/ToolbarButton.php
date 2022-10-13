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



namespace Mirasvit\StabilityAlert\Ui;

use Mirasvit\Stability\Ui\General\Toolbar\ButtonInterface;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;
use Mirasvit\StabilityAlert\Api\Repository\AlertRepositoryInterface;

class ToolbarButton implements ButtonInterface
{
    /**
     * @var AlertRepositoryInterface
     */
    private $alertRepository;

    /**
     * ToolbarButton constructor.
     * @param AlertRepositoryInterface $alertRepository
     */
    public function __construct(
        AlertRepositoryInterface $alertRepository
    ) {
        $this->alertRepository = $alertRepository;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return __('Alerts');
    }

    /**
     * @return false|int
     */
    public function getExtra()
    {
        $errors = 0;
        foreach ($this->alertRepository->getAlertPool() as $alert) {
            if ($alert->getStatus() !== AlertInterface::STATUS_SUCCESS) {
                $errors++;
            }
        }

        return $errors;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return 10;
    }

    /**
     * @return string
     */
    public function getUiName()
    {
        return 'stability_alert_listing';
    }
}
