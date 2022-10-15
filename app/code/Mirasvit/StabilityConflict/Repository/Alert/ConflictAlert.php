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



namespace Mirasvit\StabilityConflict\Repository\Alert;

use Mirasvit\StabilityAlert\Api\Data\AlertInterface;
use Mirasvit\StabilityConflict\Service\ConflictDetectorService;

class ConflictAlert implements AlertInterface
{
    /**
     * @var ConflictDetectorService
     */
    private $conflictDetectorService;

    /**
     * @var null
     */
    private $conflicts = null;

    /**
     * ConflictAlert constructor.
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
    public function getName()
    {
        return __('Conflicts');
    }

    /**
     * @return int
     */
    public function getImportance()
    {
        return self::IMPORTANCE_MAJOR;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $this->check();

        $list = [];

        if (count($this->conflicts) === 0) {
            $list[] = __('No conflicts found.');
        } else {
            $list[] = __('%1 possible conflict(s) found.', count($this->conflicts));
        }

        return implode(' ', $list);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $this->check();

        return count($this->conflicts) === 0 ? self::STATUS_SUCCESS : self::STATUS_ERROR;
    }

    private function check()
    {
        if ($this->conflicts !== null) {
            return;
        }
        $this->conflicts = $this->conflictDetectorService->getConflicts();
    }
}
