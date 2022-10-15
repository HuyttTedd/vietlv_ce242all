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
 * @package   mirasvit/module-cache-warmer
 * @version   1.5.8
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CacheWarmer\Ui\Page\Listing\Component;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\CacheWarmer\Model\Config;
use Mirasvit\CacheWarmer\Service\Config\ExtendedConfig;
use Mirasvit\CacheWarmer\Service\Rate\CacheCoverageRateService;
use Mirasvit\CacheWarmer\Service\Rate\CacheFillRateService;

class Info extends AbstractComponent
{
    /**
     * @var CacheFillRateService
     */
    private $fillRateService;

    /**
     * @var CacheCoverageRateService
     */
    private $coverageRateService;

    /**
     * @var ExtendedConfig
     */
    private $extendedConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    public function __construct(
        CacheFillRateService $fillRateService,
        CacheCoverageRateService $coverageRateService,
        Config $config,
        ExtendedConfig $extendedConfig,
        ContextInterface $context,
        DeploymentConfig $deploymentConfig,
        array $components = [],
        array $data = []
    ) {
        $this->fillRateService     = $fillRateService;
        $this->coverageRateService = $coverageRateService;
        $this->config              = $config;
        $this->extendedConfig      = $extendedConfig;
        $this->deploymentConfig    = $deploymentConfig;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'fill_rate';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        switch ($this->config->getCacheType()) {
            case 1:
                $cacheType = 'Built-in';

                if($external = $this->deploymentConfig->get('cache/frontend/page_cache/backend')) {
                    $type = strpos($external, 'Redis') !== false ? 'Redis' : $external;

                    $cacheType .= " ($type)";
                }
                break;
            case 'LITEMAGE':
                $cacheType = 'LiteMage';
                break;
            default:
                $cacheType = 'Varnish';
        }

        $config['cacheType']         = $cacheType;
        $config['cacheTtl']          = $this->prettifyTTL($this->config->getCacheTtl());
        $config['fillHistory']       = $this->getFillHistory();//$this->fillRateService->getHistory();
        $config['fillRates']         = [
            'inCache' => $this->fillRateService->getRate(),
            'total'   => 100,
        ];
        $config['statisticsEnabled'] = (int)$this->extendedConfig->isStatisticsEnabled();
        $config['coverageRate']      = $this->coverageRateService->getRate();

        $this->setData('config', $config);

        parent::prepare();
    }

    /**
     * @return array
     */
    private function getFillHistory()
    {
        $history = [];

        $ts          = ceil($this->config->getDateTime()->getTimestamp() / 60) * 60 - 24 * 60 * 60;
        $rateHistory = $this->fillRateService->getHistory();

        $prevValue = 0;

        for ($i = 0; $i < 24 * 60; $i++) {
            $ts += 60;

            $key = date("H:i", $ts);

            $history[$key] = isset($rateHistory[$ts]) ? $rateHistory[$ts] : $prevValue;

            $prevValue = $history[$key];
        }

        return $history;
    }

    /**
     * @param int $ttl
     * @return \Magento\Framework\Phrase
     */
    private function prettifyTTL($ttl)
    {
        $hour = 60 * 60;
        $day  = 24 * $hour;

        if ($ttl > 2 * $day) { // 2 days
            return __("%1 days", intval($ttl / $day));
        }

        if ($ttl > 3 * $hour) { // 3 hours
            return __("%1 hours", intval($ttl / $hour));
        }

        return __("%1 mins", intval($ttl / 60));
    }
}
