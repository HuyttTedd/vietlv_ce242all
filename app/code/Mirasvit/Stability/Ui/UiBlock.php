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



namespace Mirasvit\Stability\Ui;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Module\Manager as ModuleManager;

class UiBlock extends Template
{
    /**
     * @var UiComponentFactory
     */
    private $uiComponentFactory;

    /**
     * @var ModuleManager
     */
    private $moduleList;

    /**
     * @var Template\Context
     */
    private $context;

    /**
     * UiBlock constructor.
     * @param UiComponentFactory $uiComponentFactory
     * @param ModuleManager $moduleList
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        UiComponentFactory $uiComponentFactory,
        ModuleManager $moduleList,
        Template\Context $context,
        array $data = []
    ) {
        $this->uiComponentFactory = $uiComponentFactory;
        $this->moduleList         = $moduleList;
        $this->context            = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toHtml()
    {
        $uiName = $this->context->getRequest()->getParam('ui', 'stability_alert_listing');

        if (!$this->moduleList->isEnabled('Mirasvit_StabilityAlert') && $uiName == 'stability_alert_listing') {
            $uiName = 'stability_snapshot_listing';
        }

        if ($uiName) {
            $component = $this->uiComponentFactory->create($uiName);

            $this->prepareComponent($component);

            /** @var \Magento\Ui\Component\Wrapper\UiComponent $block */
            $block = $this->context->getLayout()->createBlock(
                \Magento\Ui\Component\Wrapper\UiComponent::class,
                $uiName,
                [
                    'component' => $component,
                ]
            );

            return $block->toHtml();
        }
    }

    /**
     * @param UiComponentInterface $component
     */
    protected function prepareComponent(UiComponentInterface $component)
    {
        $data = [];
        foreach ($component->getChildComponents() as $name => $child) {
            $data['children'][$name] = $this->prepareComponent($child);
        }

        $component->prepare();
    }
}
