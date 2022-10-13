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



namespace Mirasvit\Banner\Ui\Banner\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Service\AnalyticsService;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $analyticsService;

    /**
     * @SuppressWarnings(PHPMD)
     * @param AnalyticsService      $analyticsService
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param ReportingInterface    $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface      $request
     * @param FilterBuilder         $filterBuilder
     * @param array                 $meta
     * @param array                 $data
     */
    public function __construct(
        AnalyticsService $analyticsService,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->analyticsService = $analyticsService;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [
            'items'        => [],
            'totalRecords' => $searchResult->getTotalCount(),
        ];

        /** @var BannerInterface $model */
        foreach ($searchResult->getItems() as $model) {
            $data = [];
            foreach (array_keys($model->getData()) as $key) {
                $data[$key] = $model->getDataUsingMethod($key);
            }

            $data['analytics'] = $this->analyticsHtml($model->getId());

            $arrItems['items'][] = $data;
        }

        return $arrItems;
    }

    /**
     * @param int $blockId
     *
     * @return string
     */
    private function analyticsHtml($blockId)
    {
        $impression = round($this->analyticsService->getImpression($blockId));
        $clicks     = round($this->analyticsService->getClicks($blockId));

        $ctr = $impression > 0 ? round($clicks / $impression * 100, 1) : 0;


        return sprintf(
            '
            <div class="mst-banner__analytics-html">
                <div><p>Impression <span>%s</span></p></div>
                <div><p>Clicks <span>%s<i>%s%%</i></span></p></div>

            </div>',
            $impression,
            $clicks,
            $ctr
        );
    }
}
