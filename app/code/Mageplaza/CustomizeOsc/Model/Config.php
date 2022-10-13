<?php

namespace Mageplaza\CustomizeOsc\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\Base\Model\Serializer;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 * Class Config for manage global settings
 */
class Config extends ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * Path Prefix For Config
     */
    const PATH_PREFIX = 'customize_osc/';

    const GENERAL_BLOCK = 'general/';
    const FIELD_ENABLED = 'enabled';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ReinitableConfigInterface
     */
    private $reinitableConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Serializer $serializer,
        EavConfig $eavConfig,
        WriterInterface $configWriter,
        ReinitableConfigInterface $reinitableConfig
    ) {
        parent::__construct($scopeConfig);
        $this->serializer = $serializer;
        $this->eavConfig = $eavConfig;
        $this->configWriter = $configWriter;
        $this->reinitableConfig = $reinitableConfig;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isSetFlag(self::GENERAL_BLOCK . self::FIELD_ENABLED);
    }
}