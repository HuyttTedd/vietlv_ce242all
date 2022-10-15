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



namespace Mirasvit\StabilityError\Ui\Error\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;
use Mirasvit\StabilitySnapshot\Api\Service\SnapshotServiceInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var SnapshotServiceInterface
     */
    private $snapshotService;

    /**
     * DataProvider constructor.
     * @param SnapshotServiceInterface $snapshotService
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        SnapshotServiceInterface $snapshotService,
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
        $this->snapshotService = $snapshotService;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @return SearchResultInterface
     */
    public function getSearchResult()
    {
        $this->addFilter(
            $this->filterBuilder->create()
                ->setField(ErrorInterface::SNAPSHOT_ID)
                ->setValue($this->snapshotService->getRunningSnapshot()->getId())
        );

        return parent::getSearchResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $result = [
            'items'        => [],
            'totalRecords' => $searchResult->getTotalCount(),
        ];

        /** @var ErrorInterface $item */
        foreach ($searchResult->getItems() as $item) {
            $data = [
                ErrorInterface::ID          => $item->getId(),
                ErrorInterface::SNAPSHOT_ID => $item->getSnapshotId(),
                ErrorInterface::IDENTIFIER  => $item->getIdentifier(),
                ErrorInterface::TYPE        => $item->getType(),
                ErrorInterface::URI         => $item->getUri(),
                ErrorInterface::MESSAGE     => $item->getMessage(),
                ErrorInterface::TRACE       => $item->getTrace(),
                ErrorInterface::CREATED_AT  => $item->getCreatedAt(),
                ErrorInterface::COUNT       => $item->getCount(),
            ];

            $result['items'][] = $data;
        }

        return $result;
    }
}
