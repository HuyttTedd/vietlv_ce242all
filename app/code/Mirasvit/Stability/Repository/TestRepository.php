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



namespace Mirasvit\Stability\Repository;

use Mirasvit\Stability\Api\Repository\TestRepositoryInterface;

class TestRepository implements TestRepositoryInterface
{
    /**
     * @var array
     */
    private $generalTests;

    /**
     * @var array
     */
    private $pageTests;

    /**
     * TestRepository constructor.
     * @param array $generalTests
     * @param array $pageTests
     */
    public function __construct(
        array $generalTests = [],
        array $pageTests = []
    ) {
        $this->generalTests = $generalTests;
        $this->pageTests = $pageTests;
    }

    /**
     * @return array|\Mirasvit\Stability\Api\Data\GeneralTestInterface[]
     */
    public function getGeneralTests()
    {
        return $this->generalTests;
    }

    /**
     * @return array|\Mirasvit\Stability\Api\Data\PageTestInterface[]
     */
    public function getPageTests()
    {
        return $this->pageTests;
    }
}
