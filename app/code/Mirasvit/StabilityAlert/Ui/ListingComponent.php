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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;
use Mirasvit\StabilityAlert\Api\Repository\AlertRepositoryInterface;

class ListingComponent extends AbstractComponent
{
    /**
     * @var AlertRepositoryInterface
     */
    private $alertRepository;

    /**
     * ListingComponent constructor.
     * @param AlertRepositoryInterface $alertRepository
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        AlertRepositoryInterface $alertRepository,
        ContextInterface $context,
        $components = [],
        array $data = []
    ) {
        $this->alertRepository = $alertRepository;

        parent::__construct($context, $components, $data);
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return 'alert_list';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        foreach ($this->alertRepository->getAlertPool() as $alert) {
            switch ($alert->getStatus()) {
                case AlertInterface::STATUS_SUCCESS:
                    $sortOrder = 100;
                    break;
                case AlertInterface::STATUS_WARING:
                    $sortOrder = 200;
                    break;
                case AlertInterface::STATUS_ERROR:
                    $sortOrder = 300;
                    break;
                default:
                    $sortOrder = 50;
            }

            $config['alerts'][] = [
                AlertInterface::NAME        => $alert->getName(),
                AlertInterface::IMPORTANCE  => $alert->getImportance(),
                AlertInterface::STATUS      => $alert->getStatus(),
                AlertInterface::DESCRIPTION => str_replace(PHP_EOL, '<br>', $alert->getDescription()),
                'sortOrder'                 => $sortOrder,
            ];
        }

        usort($config['alerts'], function ($a, $b) {
            return $b['sortOrder'] - $a['sortOrder'];
        });

        $this->setData('config', $config);

        parent::prepare();
    }
}
