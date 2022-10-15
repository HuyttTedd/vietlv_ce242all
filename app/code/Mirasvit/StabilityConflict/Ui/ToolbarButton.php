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

use Mirasvit\Stability\Ui\General\Toolbar\ButtonInterface;
use Mirasvit\StabilityConflict\Service\ConflictDetectorService;

class ToolbarButton implements ButtonInterface
{
    /**
     * @var ConflictDetectorService
     */
    private $conflictDetectorService;

    /**
     * ToolbarButton constructor.
     * @param ConflictDetectorService $conflictDetectorService
     */
    public function __construct(
        ConflictDetectorService $conflictDetectorService
    ) {
        $this->conflictDetectorService = $conflictDetectorService;
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getLabel()
    {
        return __('Conflicts');
    }

    /**
     * @return false|int|void
     */
    public function getExtra()
    {
        return count($this->conflictDetectorService->getConflicts());
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return 30;
    }

    /**
     * @return string
     */
    public function getUiName()
    {
        return 'stability_conflict_listing';
    }
}
