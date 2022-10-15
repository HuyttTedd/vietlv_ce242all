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



namespace Mirasvit\Banner\Ui\Banner\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Banner\Repository\PlaceholderRepository;

class PlaceholderSource implements OptionSourceInterface
{
    private $placeholderRepository;

    public function __construct(
        PlaceholderRepository $placeholderRepository
    ) {
        $this->placeholderRepository = $placeholderRepository;
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->placeholderRepository->getCollection() as $placeholder) {
            $options[] = [
                'value' => $placeholder->getId(),
                'label' => $placeholder->getName(),
            ];
        }

        return $options;
    }
}
