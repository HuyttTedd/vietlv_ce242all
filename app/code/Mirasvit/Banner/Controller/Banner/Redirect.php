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
use Mirasvit\Banner\Api\Data\AnalyticsInterface;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Repository\AnalyticsRepository;
use Mirasvit\Banner\Repository\BannerRepository;
use Mirasvit\Banner\Service\AnalyticsService;

class Redirect extends Action
{
    private $bannerRepository;

    private $analyticsService;

    private $analyticsRepository;

    public function __construct(
        BannerRepository $bannerRepository,
        AnalyticsService $analyticsService,
        AnalyticsRepository $analyticsRepository,
        Context $context
    ) {
        $this->bannerRepository    = $bannerRepository;
        $this->analyticsService    = $analyticsService;
        $this->analyticsRepository = $analyticsRepository;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $bannerId = (int)$this->getRequest()->getParam(BannerInterface::ID);

        $banner = $this->bannerRepository->get($bannerId);

        if (!$banner) {
            $this->_redirect('/');
        }

        $this->analyticsRepository->save(
            $this->analyticsRepository->create()
                ->setBannerId($banner->getId())
                ->setSessionId($this->analyticsService->getSessionId())
                ->setRemoteAddr($this->analyticsService->getRemoteAddr())
                ->setAction(AnalyticsInterface::ACTION_CLICK)
                ->setValue(1)
                ->setReferrer($this->_redirect->getRefererUrl())
        );

        $this->_redirect($banner->getUrl());
    }
}
