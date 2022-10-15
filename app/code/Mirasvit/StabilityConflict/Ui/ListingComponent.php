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



namespace Mirasvit\StabilityConflict\Ui;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Mirasvit\StabilityConflict\Service\ConflictDetectorService;

class ListingComponent extends AbstractComponent
{
    /**
     * @var ConflictDetectorService
     */
    private $conflictDetectorService;

    /**
     * ListingComponent constructor.
     * @param ConflictDetectorService $conflictDetectorService
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ConflictDetectorService $conflictDetectorService,
        ContextInterface $context,
        $components = [],
        array $data = []
    ) {
        $this->conflictDetectorService = $conflictDetectorService;

        parent::__construct($context, $components, $data);
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return 'listing';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        $config['conflicts'] = [];

        $conflicts = $this->conflictDetectorService->getConflicts();

        foreach ($conflicts as $parent => $preferences) {
            $config['conflicts'][] = [
                'parent'      => $parent,
                'preferences' => $preferences,
            ];
        }

        $this->setData('config', $config);

        parent::prepare();
    }
}
