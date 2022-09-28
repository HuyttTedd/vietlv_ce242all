<?php

namespace Amasty\PushNotifications\Api;

/**
 * @api
 */
interface CampaignRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Amasty\PushNotifications\Api\Data\CampaignInterface $campaign);

    /**
     * Get by id
     *
     * @param int $campaignId
     * @return \Amasty\PushNotifications\Api\Data\CampaignInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($campaignId);

    /**
     *
     * @param int $campaignId
     * @return $this
     */
    public function increaseClickCounter($campaignId);

    /**
     * Delete
     *
     * @param \Amasty\PushNotifications\Api\Data\CampaignInterface $campaign
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\PushNotifications\Api\Data\CampaignInterface $campaign);

    /**
     * Delete by id
     *
     * @param int $campaignId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($campaignId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
