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



namespace Mirasvit\Banner\Ui\Placeholder\Form\Modifier;

use Magento\Framework\View\Element\UiComponent\Config\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Repository\PlaceholderRepository;

class RendererModifier
{
    private $context;

    private $placeholderRepository;

    private $uiComponentFactory;

    private $componentManager;

    public function __construct(
        ContextInterface $context,
        PlaceholderRepository $placeholderRepository,
        UiComponentFactory $uiComponentFactory,
        ManagerInterface $componentManager
    ) {
        $this->context               = $context;
        $this->placeholderRepository = $placeholderRepository;
        $this->uiComponentFactory    = $uiComponentFactory;
        $this->componentManager      = $componentManager;
    }

    public function modifyMeta(array $meta)
    {
        $id = $this->context->getRequestParam(PlaceholderInterface::ID, null);

        if ($id) {
            $placeholder = $this->placeholderRepository->get($id);

            $componentName = 'mstBanner_placeholder_' . $placeholder->getRenderer();
            $isExist       = false;

            try {
                $this->componentManager->prepareData($componentName);
                $isExist = true;
            } catch (\Exception $e) {
            }

            if ($isExist) {
                $isExist = $this->uiComponentFactory->create($componentName);

                return ['props' => $this->prepareComponent($isExist)];
            }
        }

        return $meta;
    }

    /**
     * @param UiComponentInterface $component
     *
     * @return array
     */
    protected function prepareComponent(UiComponentInterface $component)
    {
        $data = [];
        foreach ($component->getChildComponents() as $child) {
            $data['children'][] = $this->prepareComponent($child);
        }

        $component->prepare();
        $data['arguments']['data'] = $component->getData();
        unset($data['arguments']['data']['options']);

        return $data;
    }
}
