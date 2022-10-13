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



namespace Mirasvit\StabilityTiming\Plugin\PageCache\Model\App\Response\HttpPlugin;

use Magento\Framework\App\RequestInterface;
use Mirasvit\Stability\Api\Repository\StorageRepositoryInterface;
use Mirasvit\StabilityTiming\Model\Config;

class ServerTimePlugin
{
    /**
     * @var StorageRepositoryInterface
     */
    private $storageRepository;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * ServerTimePlugin constructor.
     * @param StorageRepositoryInterface $storageRepository
     * @param RequestInterface $request
     * @param Config $config
     */
    public function __construct(
        StorageRepositoryInterface $storageRepository,
        RequestInterface $request,
        Config $config
    ) {
        $this->storageRepository = $storageRepository;
        $this->request           = $request;
        $this->config            = $config;
    }

    /**
     * @param mixed $subject
     */
    public function afterBeforeSendResponse($subject)
    {
        register_shutdown_function([
            $this, 'onShutdown',
        ]);
    }

    public function onShutdown()
    {
        $this->storageRepository->put(__CLASS__, 'enqueue', [
            Config::STORAGE_ACTION      => $this->request->getFullActionName(),
            Config::STORAGE_SERVER_TIME => (microtime(true) - $_SERVER['REQUEST_TIME']) * 1000,
        ]);
    }

    /**
     * @param array $data
     */
    public function enqueue(array $data)
    {
        $action = $data[Config::STORAGE_ACTION];

        $code = $this->config->getStorageCode($action, Config::AGGREGATION_SUM);
        $this->storageRepository->setSnapshotValue(
            $code,
            $this->storageRepository->getSnapshotValue($code, 0) + $data[Config::STORAGE_SERVER_TIME]
        );

        $code = $this->config->getStorageCode($action, Config::AGGREGATION_QTY);
        $this->storageRepository->setSnapshotValue(
            $code,
            $this->storageRepository->getSnapshotValue($code, 0) + 1
        );
    }
}
