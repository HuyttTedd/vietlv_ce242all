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



namespace Mirasvit\StabilityAlert\Repository\Alert;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;

class CacheStatusAlert implements AlertInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UrlInterface
     */
    private $urlManager;

    /**
     * @var null
     */
    private $isRunning         = null;

    /**
     * @var null
     */
    private $lastExecutionTime = null;

    /**
     * CacheStatusAlert constructor.
     * @param CacheManager $cacheManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        CacheManager $cacheManager,
        UrlInterface $urlBuilder
    ) {
        $this->cacheManager = $cacheManager;
        $this->urlManager   = $urlBuilder;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {
        return __('Cache Status');
    }

    /**
     * @return int
     */
    public function getImportance()
    {
        return self::IMPORTANCE_MAJOR;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $disabled = $this->getDisabledCaches();

        $list = [];

        if (count($disabled) == 0) {
            $list[] = __('All caches are enabled.');
        } else {
            $list[] = __('Disabled caches: %1', implode(', ', $disabled));

            $url    = $this->urlManager->getUrl('admin/cache');
            $list[] = "<a href='$url'>" . __('Cache Management') . "</a>";
        }

        return implode(' ', $list);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $disabled = $this->getDisabledCaches();

        return in_array('config', $disabled)
        || in_array('layout', $disabled)
        || in_array('full_page', $disabled)
            ? self::STATUS_ERROR
            : self::STATUS_SUCCESS;
    }

    /**
     * @return array
     */
    private function getDisabledCaches()
    {
        $r = [];

        foreach ($this->cacheManager->getStatus() as $cache => $status) {
            if (!$status) {
                $r[] = $cache;
            }
        }

        return $r;
    }
}
