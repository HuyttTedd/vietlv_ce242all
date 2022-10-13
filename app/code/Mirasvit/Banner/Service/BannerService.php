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



namespace Mirasvit\Banner\Service;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Repository\BannerRepository;

class BannerService
{
    private $bannerRepository;

    private $customerSession;

    private $storeManager;

    private $urlManager;

    private $contentService;

    public function __construct(
        BannerRepository $bannerRepository,
        CustomerSession $customerSession,
        StoreManagerInterface $storeManager,
        UrlInterface $urlManager,
        ContentService $contentService
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->customerSession  = $customerSession;
        $this->storeManager     = $storeManager;
        $this->urlManager       = $urlManager;
        $this->contentService   = $contentService;
    }

    /**
     * @param PlaceholderInterface $placeholder
     * @param int                  $limit
     *
     * @return BannerInterface[]
     */
    public function getApplicableBanners(PlaceholderInterface $placeholder, $limit)
    {
        $collection = $this->getBannersByPlaceholder($placeholder);

        $banners = [];
        /** @var BannerInterface $banner */
        foreach ($collection as $banner) {
            if ($this->isApplicable($banner)) {
                $banners[] = $banner;

                if (count($banners) >= $limit) {
                    break;
                }
            }
        }

        return $banners;
    }

    /**
     * If banners applicable to placeholder contains extra conditions - ajax load is required
     *
     * @param PlaceholderInterface $placeholder
     *
     * @return bool
     */
    public function isAjaxRequired(PlaceholderInterface $placeholder)
    {
        $list = $this->getBannersByPlaceholder($placeholder);

        if (count($list) === 0) {
            return true;
        }

        foreach ($list as $banner) {
            if (strpos($banner->getConditions(), 'SalesRule') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param PlaceholderInterface $placeholder
     *
     * @return BannerInterface[]
     */
    private function getBannersByPlaceholder(PlaceholderInterface $placeholder)
    {
        $customerGroupId = $this->customerSession->isLoggedIn() ? $this->customerSession->getCustomer()->getGroupId() : 0;
        $storeId         = $this->storeManager->getStore()->getId();

        $collection = $this->bannerRepository->getCollection()
            ->addFieldToFilter(BannerInterface::IS_ACTIVE, 1)
            ->addDateFilter()
            ->addCustomerGroupFilter($customerGroupId)
            ->addStoreFilter($storeId)
            ->addPlaceholderFilter($placeholder->getId())
            ->addOrder(BannerInterface::SORT_ORDER, 'desc')
            ->addOrder('rand()');

        return $collection->getItems();
    }

    private function isApplicable(BannerInterface $banner)
    {
        $dataObject = $banner->getRule()->getDataObject([]);
        $dataObject->init();

        return $banner->getRule()->validate($dataObject);
    }

    public function toHtml(BannerInterface $banner)
    {
        $content = $this->contentService->processHtmlContent($banner->getContent());

        return $this->applyLink($banner, $this->applyWrapper($banner, $content));
    }

    /**
     * @param BannerInterface $banner
     * @param string          $content
     *
     * @return string
     */
    private function applyLink(BannerInterface $banner, $content)
    {
        if (!$banner->getUrl()) {
            return $content;
        }

        $url = $this->urlManager->getUrl('mst_banner/banner/redirect', [BannerInterface::ID => $banner->getId()]);

        return '<div onclick="window.location.href=\'' . $url . '\'" style="cursor:pointer;">' . $content . '</div>';
    }

    /**
     * @param BannerInterface $banner
     * @param string          $content
     *
     * @return string
     */
    private function applyWrapper(BannerInterface $banner, $content)
    {
        return '<div data-banner="' . $banner->getId() . '">' . $content . '</div>';
    }
}
