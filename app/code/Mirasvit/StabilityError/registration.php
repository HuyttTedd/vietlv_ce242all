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


\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Mirasvit_StabilityError',
    __DIR__
);

//register_shutdown_function('onStabilityErrorShutdown');
//
//function onStabilityErrorShutdown()
//{
//    $lastError = error_get_last();
//
//    if (!is_array($lastError)) {
//        return;
//    }
//
//    if (in_array($lastError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
//        /** @var \Mirasvit\StabilityError\Service\ErrorHandlerService $errorHandlerService */
//        $errorHandlerService = \Magento\Framework\App\ObjectManager::getInstance()
//            ->get(\Mirasvit\StabilityError\Service\ErrorHandlerService::class);
//
//        $errorHandlerService->registerServerError($lastError);
//    }
//}