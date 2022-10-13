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

interface PlaceholderInterface
{
    const DISPLAY_MODE_STATIC  = 'static';
    const DISPLAY_MODE_DYNAMIC = 'dynamic';

    const TABLE_NAME = 'mst_banner_placeholder';

    const ID        = 'placeholder_id';
    const NAME      = 'name';
    const IS_ACTIVE = 'is_active';
    const RENDERER  = 'renderer';

    const LAYOUT_UPDATE_ID  = 'layout_update_id';
    const LAYOUT_POSITION   = 'layout_position';
    const LAYOUT_CONDITIONS = 'layout_conditions';

    const POSITION_LAYOUT    = 'position_layout';
    const POSITION_CONTAINER = 'position_container';
    const POSITION_BEFORE    = 'position_before';
    const POSITION_AFTER     = 'position_after';

    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const CSS                   = 'css';
    const IDENTIFIER            = 'identifier';

    /**
     * @return int
     */
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
    public function getRenderer();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRenderer($value);

    /**
     * @return int
     */
    public function getLayoutUpdateId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setLayoutUpdateId($value);

    /**
     * @return string
     */
    public function getLayoutPosition();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLayoutPosition($value);

    /**
     * @return string
     */
    public function getPositionLayout();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPositionLayout($value);

    /**
     * @return string
     */
    public function getPositionContainer();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPositionContainer($value);

    /**
     * @return string
     */
    public function getPositionBefore();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPositionBefore($value);

    /**
     * @return string
     */
    public function getPositionAfter();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPositionAfter($value);

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
     * @return \Mirasvit\Banner\Model\Placeholder\Rule
     */
    public function getRule();

    /**
     * @return string
     */
    public function getCss();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCss($value);

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIdentifier($value);

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
