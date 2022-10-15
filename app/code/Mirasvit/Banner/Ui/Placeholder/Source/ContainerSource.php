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



namespace Mirasvit\Banner\Ui\Placeholder\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Layout\ProcessorFactory as LayoutProcessorFactory;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;

class ContainerSource implements OptionSourceInterface
{
    private $layoutProcessorFactory;

    private $themeCollectionFactory;

    public function __construct(
        LayoutProcessorFactory $layoutProcessorFactory,
        ThemeCollectionFactory $themeCollectionFactory
    ) {
        $this->layoutProcessorFactory = $layoutProcessorFactory;
        $this->themeCollectionFactory = $themeCollectionFactory;
    }

    public function toOptionArray()
    {
        $pageLayoutProcessor = $this->layoutProcessorFactory->create([
            'theme' => $this->getThemeInstance(1),
        ]);

        foreach (['1column-center', '2columns-left', '2columns-right', '3columns'] as $handle) {
            $pageLayoutProcessor->addHandle($handle);
        }

        $pageLayoutProcessor->load();


        $options = [];
        foreach ($pageLayoutProcessor->getContainers() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
            ];
        }

        return $options;
    }

    /**
     * @param int $themeId
     *
     * @return \Magento\Framework\DataObject
     */
    private function getThemeInstance($themeId)
    {
        /** @var \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection */
        $themeCollection = $this->themeCollectionFactory->create();

        return $themeCollection->getItemById($themeId);
    }
}
