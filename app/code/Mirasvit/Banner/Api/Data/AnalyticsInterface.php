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

interface AnalyticsInterface
{
    const ACTION_IMPRESSION = 'impression';
    const ACTION_CLICK      = 'click';

    const TABLE_NAME = 'mst_banner_analytics';

    const ID          = 'analytics_id';
    const BANNER_ID   = 'banner_id';
    const ACTION      = 'action';
    const VALUE       = 'value';
    const REFERRER    = 'referrer';
    const SESSION_ID  = 'session_id';
    const REMOTE_ADDR = 'remote_addr';
    const CREATED_AT  = 'created_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getBannerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setBannerId($value);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAction($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getReferrer();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setReferrer($value);

    /**
     * @return string
     */
    public function getSessionId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSessionId($value);

    /**
     * @return string
     */
    public function getRemoteAddr();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRemoteAddr($value);

    /**
     * @return string
     */
    public function getCreatedAt();
}
