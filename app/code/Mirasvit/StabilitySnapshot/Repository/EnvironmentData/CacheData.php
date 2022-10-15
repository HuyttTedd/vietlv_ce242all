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



namespace Mirasvit\StabilitySnapshot\Repository\EnvironmentData;

use Magento\Framework\App\Cache\Manager as CacheManager;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\EnvironmentDataInterface;

class CacheData implements EnvironmentDataInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * CacheData constructor.
     * @param CacheManager $cacheManager
     */
    public function __construct(
        CacheManager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Cache Status';
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [];

        foreach ($this->cacheManager->getStatus() as $type => $status) {
            $data[$type] = [
                'label' => $type,
                'value' => $status ? 'Enabled' : 'Disabled',
            ];
        }

        return $data;
    }
}
