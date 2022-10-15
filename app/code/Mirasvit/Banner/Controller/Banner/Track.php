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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Controller\Banner;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Banner\Repository\AnalyticsRepository;
use Mirasvit\Banner\Service\AnalyticsService;

class Track extends Action
{
    private $analyticsRepository;

    private $analyticsService;

    public function __construct(
        AnalyticsRepository $analyticsRepository,
        AnalyticsService $analyticsService,
        Context $context
    ) {
        $this->analyticsRepository = $analyticsRepository;
        $this->analyticsService    = $analyticsService;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $action   = $this->getRequest()->getParam('action');
        $bannerId = $this->getRequest()->getParam('banner_id');
        $referrer = $this->getRequest()->getParam('referrer');

        if ($action && $bannerId) {
            $model = $this->analyticsRepository->create();
            $model->setAction($action)
                ->setSessionId($this->analyticsService->getSessionId())
                ->setRemoteAddr($this->analyticsService->getRemoteAddr())
                ->setReferrer($referrer)
                ->setBannerId($bannerId)
                ->setValue(1);

            $this->analyticsRepository->save($model);
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(SerializeService::encode([
            'success' => true,
        ]));
    }
}
