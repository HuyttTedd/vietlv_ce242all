<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Model;

use Mageplaza\BetterTierPrice\Api\ConfigRepositoryInterface;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\General;

/**
 * Class ConfigRepository
 * @package Mageplaza\BetterTierPrice\Model
 */
class ConfigRepository implements ConfigRepositoryInterface
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Config
     */
    protected $config;

    /**
     * ConfigRepository constructor.
     *
     * @param Data $helperData
     * @param Config $config
     */
    public function __construct(
        Data $helperData,
        Config $config
    ) {
        $this->helperData = $helperData;
        $this->config     = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs($storeId = null)
    {
        $configModule = $this->helperData->getConfigValue(Data::CONFIG_MODULE_PATH, $storeId);

        $generalObject = new General($configModule['general']);
        $this->config->setGeneral($generalObject);

        return $this->config;
    }
}
