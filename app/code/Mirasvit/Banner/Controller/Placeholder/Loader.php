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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Controller\Placeholder;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Repository\PlaceholderRepository;
use Mirasvit\Banner\Service\PlaceholderService;

class Loader extends Action
{
    private $placeholderService;

    private $placeholderRepository;

    public function __construct(
        PlaceholderService $placeholderService,
        PlaceholderRepository $placeholderRepository,
        Context $context
    ) {
        $this->placeholderService    = $placeholderService;
        $this->placeholderRepository = $placeholderRepository;

        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $placeholderIds = $this->getRequest()->getParam(PlaceholderInterface::ID, $params);

        $result = [];

        foreach ($placeholderIds as $placeholderId) {
            $placeholder = $this->placeholderRepository->get($placeholderId);

            if (!$placeholder) {
                continue;
            }

            $html = $this->placeholderService->getRendererHtml($placeholder);

            $result[] = [
                PlaceholderInterface::ID => $placeholderId,
                'html'                   => $html,
            ];
        }

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(SerializeService::encode([
            'placeholders' => $result,
            'success'      => true,
        ]));
    }
}
