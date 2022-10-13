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



namespace Mirasvit\Banner\Api\Data;

interface BannerInterface
{
    const TABLE_NAME = 'mst_banner_banner';

    const POSITION_PAGE_TOP    = 'page_top';
    const POSITION_PAGE_BOTTOM = 'page_bottom';

    const ID                    = 'banner_id';
    const NAME                  = 'name';
    const IS_ACTIVE             = 'is_active';
    const ACTIVE_FROM           = 'active_from';
    const ACTIVE_TO             = 'active_to';
    const PLACEHOLDER_IDS       = 'placeholder_ids';
    const SORT_ORDER            = 'sort_order';
    const CONTENT               = 'content';
    const URL                   = 'url';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const CUSTOMER_GROUP_IDS    = 'customer_group_ids';
    const STORE_IDS             = 'store_ids';

    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsActive($value);

    /**
     * @return string
     */
    public function getActiveFrom();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setActiveFrom($value);

    /**
     * @return string
     */
    public function getActiveTo();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setActiveTo($value);

    /**
     * @return array
     */
    public function getPlaceholderIds();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setPlaceholderIds(array $value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setContent($value);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrl($value);

    /**
     * @return string
     */
    public function getConditions();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setConditions($value);

    /**
     * @return array
     */
    public function getCustomerGroupIds();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setCustomerGroupIds(array $value);

    /**
     * @return array
     */
    public function getStoreIds();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setStoreIds(array $value);

    /**
     * @return \Mirasvit\Banner\Model\Banner\Rule
     */
    public function getRule();

    /**
     * @param string $key
     *
     * @return string|array
     */
    public function getData($key = null);

    /**
     * @param string $key
     *
     * @return string
     */
    public function getDataUsingMethod($key);

    /**
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function setDataUsingMethod($key, $value);
}
