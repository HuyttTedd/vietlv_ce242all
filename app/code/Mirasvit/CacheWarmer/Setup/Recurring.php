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



namespace Mirasvit\CacheWarmer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Mirasvit\CacheWarmer\Api\Repository\JobRepositoryInterface;

class Recurring implements InstallSchemaInterface
{
    /**
     * @var JobRepositoryInterface
     */
    private $jobRepository;

    /**
     * Recurring constructor.
     * @param JobRepositoryInterface $jobRepository
     */
    public function __construct(
        JobRepositoryInterface $jobRepository
    ) {
        $this->jobRepository = $jobRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $jobCollection = $this->jobRepository->getCollection();

        foreach ($jobCollection as $job) {
            $this->jobRepository->delete($job);
        }
    }
}
