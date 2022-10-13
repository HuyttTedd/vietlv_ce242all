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



namespace Mirasvit\Stability\Repository;

use Magento\Framework\FlagFactory;
use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Stability\Api\Repository\StorageRepositoryInterface;
use Mirasvit\Stability\Model\Config;
use Mirasvit\StabilitySnapshot\Api\Service\SnapshotServiceInterfaceFactory;

class StorageRepository implements StorageRepositoryInterface
{
    const PUT_CLASS  = 'class';
    const PUT_METHOD = 'method';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var FlagFactory
     */
    private $flagFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SnapshotServiceInterfaceFactory
     */
    private $snapshotServiceFactory;

    /**
     * StorageRepository constructor.
     * @param Config $config
     * @param FlagFactory $flagFactory
     * @param SnapshotServiceInterfaceFactory $snapshotServiceFactory
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Config $config,
        FlagFactory $flagFactory,
        SnapshotServiceInterfaceFactory $snapshotServiceFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->config                 = $config;
        $this->flagFactory            = $flagFactory;
        $this->snapshotServiceFactory = $snapshotServiceFactory;
        $this->objectManager          = $objectManager;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array $data
     * @return mixed|void
     */
    public function put($class, $method, array $data)
    {
        $basePath = $this->config->getStoragePath();

        $files    = scandir($basePath);

        // put treshold in case of cron error
        if(count($files) >= 5000) {
            return;
        }

        $fileName = str_replace('.', '', microtime(true)) . '.dat';

        $data[self::PUT_CLASS]  = $class;
        $data[self::PUT_METHOD] = $method;

        file_put_contents("$basePath/$fileName", \Zend_Json::encode($data, true));
    }

    /**
     * @param string $code
     * @param int|string $value
     * @return $this|StorageRepositoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setValue($code, $value)
    {
        $flag = $this->flagFactory->create(['data' => ['flag_code' => 'stability_' . $code]])
            ->loadSelf();

        $flag->setFlagData($value)
            ->save();

        return $this;
    }

    /**
     * @param string $code
     * @param bool $default
     * @return bool|int|mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValue($code, $default = false)
    {
        $flag = $this->flagFactory->create(['data' => ['flag_code' => 'stability_' . $code]])
            ->loadSelf();

        return $flag->getFlagData() ? $flag->getFlagData() : $default;
    }

    /**
     * @param string $code
     * @param int|string $value
     * @return $this|StorageRepositoryInterface
     */
    public function setSnapshotValue($code, $value)
    {
        $snapshot = $this->snapshotServiceFactory->create()->getRunningSnapshot();

        if (!$snapshot) {
            return $this;
        }

        return $this->setValue($snapshot->getId() . '_' . $code, $value);
    }

    /**
     * @param string $code
     * @param bool $default
     * @return bool|int|mixed|string
     */
    public function getSnapshotValue($code, $default = false)
    {
        $snapshot = $this->snapshotServiceFactory->create()->getRunningSnapshot();

        if (!$snapshot) {
            return $default;
        }

        return $this->getValue($snapshot->getId() . '_' . $code, $default);
    }

    /**
     * @return array
     */
    public function process()
    {
        $items    = [];
        $basePath = $this->config->getStoragePath();
        $files    = scandir($basePath);

        foreach ($files as $idx => $file) {
            if ($file[0] === '.') {
                continue;
            }

            if($idx >= 1000) {
                break; // read treshold
            }

            try {
                if (!file_exists("$basePath/$file")) {
                    continue;
                }
                $data = \Zend_Json::decode(file_get_contents("$basePath/$file"));
                if (isset($data[self::PUT_CLASS]) && isset($data[self::PUT_METHOD])) {
                    $instance = $this->getClassInstance($data[self::PUT_CLASS]);
                    call_user_func([$instance, $data[self::PUT_METHOD]], $data);
                }
            } catch (\Exception $e) {
            } finally {
                unlink("$basePath/$file");
            }
        }

        return $items;
    }

    /**
     * @param string $class
     * @return mixed
     */
    private function getClassInstance($class)
    {
        return $this->objectManager->get($class);
    }
}
