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



namespace Mirasvit\StabilityError\Plugin\Framework\Logger\Monolog;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\StabilityError\Service\ErrorHandlerServiceFactory;

class ErrorHandlerPlugin extends Base
{
    /**
     * @var ErrorHandlerServiceFactory
     */
    private $errorHandlerServiceFactory;
  
    /**
     * @var string
     */
    protected $fileName;

    /**
     * ErrorHandlerPlugin constructor.
     * @param ErrorHandlerServiceFactory $errorHandlerServiceFactory
     * @param DriverInterface $filesystem
     * @param null $filePath
     * @param null $fileName
     * @throws \Exception
     */
    public function __construct(
        ErrorHandlerServiceFactory $errorHandlerServiceFactory,
        DriverInterface $filesystem,
        $filePath = null,
        $fileName = null
    ) {
        $this->errorHandlerServiceFactory = $errorHandlerServiceFactory;
        $this->fileName = $fileName;
        parent::__construct($filesystem, $filePath);
    }

    /**
     * @param array $record
     * @return bool
     */
    public function handle(array $record)
    {
        try {
            if ($record['level_name'] == 'INFO') {
                return false;
            }

            $message = trim($record['message']);

            if ($message === 'cache_invalidate:'
                || $message === 'The specified path is not allowed.'
                || $message === 'Environment emulation nesting is not allowed.') {
                return false;
            }

            $this->errorHandlerServiceFactory->create()
                ->registerLogError($record);
        } catch (\Exception $e) {
        }

        return false; // always allow bubbling
    }
}
