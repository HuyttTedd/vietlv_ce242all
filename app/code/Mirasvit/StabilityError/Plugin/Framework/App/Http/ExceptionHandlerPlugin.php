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



namespace Mirasvit\StabilityError\Plugin\Framework\App\Http;

use Mirasvit\StabilityError\Service\ErrorHandlerService;

class ExceptionHandlerPlugin
{
    /**
     * @var ErrorHandlerService
     */
    private $errorHandlerService;

    /**
     * ExceptionHandlerPlugin constructor.
     * @param ErrorHandlerService $errorHandlerService
     */
    public function __construct(
        ErrorHandlerService $errorHandlerService
    ) {
        $this->errorHandlerService = $errorHandlerService;
    }

    /**
     * @param mixed $subject
     * @param mixed $bootstrap
     * @param mixed $exception
     * @return array
     */
    public function beforeCatchException($subject, $bootstrap, $exception)
    {
        if ($exception instanceof \Exception) {
            $this->errorHandlerService->registerException($exception);
        }

        return [$bootstrap, $exception];
    }
}
