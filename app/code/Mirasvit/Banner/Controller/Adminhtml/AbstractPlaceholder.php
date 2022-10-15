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



namespace Mirasvit\Banner\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Repository\PlaceholderRepository;

abstract class AbstractPlaceholder extends Action
{
    protected $placeholderRepository;

    private   $registry;

    protected $context;

    public function __construct(
        PlaceholderRepository $placeholderRepository,
        Registry $registry,
        Context $context
    ) {
        $this->placeholderRepository = $placeholderRepository;
        $this->registry              = $registry;
        $this->context               = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Backend::marketing');
        $resultPage->getConfig()->getTitle()->prepend(__('Placeholders'));

        return $resultPage;
    }

    /**
     * @return PlaceholderInterface
     */
    public function initModel()
    {
        $model = $this->placeholderRepository->create();

        if ($this->getRequest()->getParam(PlaceholderInterface::ID)) {
            $model = $this->placeholderRepository->get($this->getRequest()->getParam(PlaceholderInterface::ID));

            if (!$model) {
                $model = $this->placeholderRepository->create();
            }
        }

        $this->registry->register(PlaceholderInterface::class, $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Banner::banner_placeholder');
    }
}
