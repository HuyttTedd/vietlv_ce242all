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
 * @package   mirasvit/module-cache-warmer
 * @version   1.5.8
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */




namespace Mirasvit\CacheWarmer\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;
use Mirasvit\CacheWarmer\Api\Repository\SourceRepositoryInterface;

class Sources implements ArrayInterface
{
    private $sourceRepository;

    public function __construct(SourceRepositoryInterface $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $collection = $this->sourceRepository->getCollection();

        $options = [];

        foreach ($collection as $source) {
            $options[] = [
                'value' => $source->getId(),
                'label' => $source->getSourceName(),
            ];
        }

        return $options;
    }
}
