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



namespace Mirasvit\StabilityError\Service;

use Magento\Framework\App\RequestInterface;
use Mirasvit\Stability\Repository\StorageRepository;
use Mirasvit\StabilityError\Api\Data\ErrorInterface;
use Mirasvit\StabilityError\Api\Repository\ErrorRepositoryInterface;
use Mirasvit\StabilityError\Model\Config;
use Mirasvit\StabilitySnapshot\Api\Service\SnapshotServiceInterface;

class ErrorHandlerService
{
    /**
     * @var StorageRepository
     */
    private $storageRepository;

    /**
     * @var ErrorRepositoryInterface
     */
    private $errorRepository;

    /**
     * @var SnapshotServiceInterface
     */
    private $snapshotService;

    /** @var \Magento\Framework\App\Request\Http */
    private $request;

    /**
     * ErrorHandlerService constructor.
     * @param StorageRepository $storageRepository
     * @param ErrorRepositoryInterface $errorRepository
     * @param SnapshotServiceInterface $snapshotService
     * @param RequestInterface $request
     */
    public function __construct(
        StorageRepository $storageRepository,
        ErrorRepositoryInterface $errorRepository,
        SnapshotServiceInterface $snapshotService,
        RequestInterface $request
    ) {
        $this->storageRepository = $storageRepository;
        $this->errorRepository   = $errorRepository;
        $this->snapshotService   = $snapshotService;
        $this->request           = $request;
    }

    /**
     * @param array $error
     */
    public function registerServerError(array $error)
    {
    }

    /**
     * @param array $error
     */
    public function registerLogError(array $error)
    {
        $this->storageRepository->put(get_class($this), 'handleError', [
            Config::STORAGE_URI     => $this->request->getUriString(),
            Config::STORAGE_MESSAGE => $error['message'],
            Config::STORAGE_TRACE   => '',
            Config::STORAGE_TYPE    => ErrorInterface::TYPE_PHP_ERROR,
        ]);
    }

    /**
     * @param \Exception $exception
     */
    public function registerException(\Exception $exception)
    {
        $this->storageRepository->put(get_class($this), 'handleError', [
            Config::STORAGE_URI     => $this->request->getUriString(),
            Config::STORAGE_MESSAGE => $exception->getMessage(),
            Config::STORAGE_TRACE   => $exception->getTraceAsString(),
            Config::STORAGE_TYPE    => ErrorInterface::TYPE_PHP_EXCEPTION,
        ]);
    }

    /**
     * @param string $uri
     * @param mixed $message
     */
    public function registerJsError($uri, $message)
    {
        $this->storageRepository->put(get_class($this), 'handleError', [
            Config::STORAGE_URI     => $uri,
            Config::STORAGE_MESSAGE => $message,
            Config::STORAGE_TRACE   => '',
            Config::STORAGE_TYPE    => ErrorInterface::TYPE_JS_ERROR,
        ]);
    }

    /**
     * @param array $data
     */
    public function handleError(array $data)
    {
        $snapshot = $this->snapshotService->getRunningSnapshot();

        try {
            $error = $this->errorRepository->create();

            $error->setSnapshotId($snapshot->getId())
                ->setType($data[Config::STORAGE_TYPE])
                ->setUri($data[Config::STORAGE_URI])
                ->setMessage($data[Config::STORAGE_MESSAGE])
                ->setTrace($data[Config::STORAGE_TRACE]);

            $this->errorRepository->save($error);
        } catch (\Exception $e) {
        }
    }
}
