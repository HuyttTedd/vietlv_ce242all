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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\StabilityAlert\Api\Data\AlertInterface;

class CacheTypeAlert implements AlertInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * CacheTypeAlert constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getName()
    {

        return __('Cache Type');
    }

    /**
     * @return int
     */
    public function getImportance()
    {
        return self::IMPORTANCE_NORMAL;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $list = [];

        if ($this->isVarnish()) {
            $list[] = __('Varnish cache is active.');
        } else {
            $list[] = __('Switch Magento cache to Varnish for archiving in better response time.');
        }

        return implode(' ', $list);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->isVarnish() ? self::STATUS_SUCCESS
            : self::STATUS_WARING;
    }

    /**
     * @return bool
     */
    private function isVarnish()
    {
        $type = $this->scopeConfig->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TYPE);

        return $type != 1 ? true : false;
    }
}
