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



namespace Mirasvit\StabilityError\Controller\Handle;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\StabilityError\Service\ErrorHandlerService;

class JsError extends Action
{
    /**
     * @var ErrorHandlerService
     */
    private $errorHandlerService;

    /**
     * JsError constructor.
     * @param ErrorHandlerService $errorHandlerService
     * @param Context $context
     */
    public function __construct(
        ErrorHandlerService $errorHandlerService,
        Context $context
    ) {
        $this->errorHandlerService = $errorHandlerService;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $uri   = $this->getRequest()->getParam('uri');
        $error = $this->getRequest()->getParam('error');

        $this->errorHandlerService->registerJsError($uri, $error);

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(\Zend_Json_Encoder::encode([
            'success' => true,
        ]));
    }
}
