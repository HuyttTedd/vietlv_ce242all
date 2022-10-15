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



namespace Mirasvit\Banner\Ui\Placeholder\Form;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Repository\PlaceholderRepository;
use Mirasvit\Banner\Ui\Placeholder\Source\ContainerSource;
use Mirasvit\Banner\Ui\Placeholder\Source\LayoutSource;

class DataProvider extends AbstractDataProvider
{
    private $layoutSource;

    private $containerSource;

    private $rendererModifier;

    /**
     * @param PlaceholderRepository     $placeholderRepository
     * @param LayoutSource              $layoutSource
     * @param ContainerSource           $containerSource
     * @param Modifier\RendererModifier $rendererModifier
     * @param string                    $name
     * @param string                    $primaryFieldName
     * @param string                    $requestFieldName
     * @param array                     $meta
     * @param array                     $data
     */
    public function __construct(
        PlaceholderRepository $placeholderRepository,
        LayoutSource $layoutSource,
        ContainerSource $containerSource,
        Modifier\RendererModifier $rendererModifier,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection       = $placeholderRepository->getCollection();
        $this->layoutSource     = $layoutSource;
        $this->containerSource  = $containerSource;
        $this->rendererModifier = $rendererModifier;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getMeta()
    {
        return $this->rendererModifier->modifyMeta(parent::getMeta());
    }

    public function getData()
    {
        $result = [];

        /** @var PlaceholderInterface $model */
        foreach ($this->collection as $model) {
            $data = [];

            foreach (array_merge(array_keys($model->getData()), [
                PlaceholderInterface::POSITION_LAYOUT,
                PlaceholderInterface::POSITION_CONTAINER,
                PlaceholderInterface::POSITION_BEFORE,
                PlaceholderInterface::POSITION_AFTER,
            ]) as $key) {
                $data[$key] = $model->getDataUsingMethod($key);
            }

            $data['position_predefined'] = $this->inSource($this->layoutSource, $model->getPositionLayout())
            && $this->inSource($this->containerSource, $model->getPositionContainer()) ? "1" : "0";

            $result[$model->getId()] = $data;
        }

        return $result;
    }

    /**
     * @param OptionSourceInterface $source
     * @param string                $value
     *
     * @return bool
     */
    private function inSource(OptionSourceInterface $source, $value)
    {
        foreach ($source->toOptionArray() as $item) {
            if ($item['value'] == $value) {
                return true;
            }
        }

        return false;
    }
}
