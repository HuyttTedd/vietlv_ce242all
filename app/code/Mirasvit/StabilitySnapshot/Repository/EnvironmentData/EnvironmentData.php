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

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\StabilitySnapshot\Api\Data\Snapshot\EnvironmentDataInterface;

class EnvironmentData implements EnvironmentDataInterface
{
    const PHP_VERSION     = 'PHP Version';
    const MYSQL_VERSION   = 'MySQL Version';
    const MAGENTO_EDITION = 'Magento Edition';
    const MAGENTO_VERSION = 'Magento Version';
    const DOCUMENT_ROOT   = 'Root Directory';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * EnvironmentData constructor.
     * @param ResourceConnection $resource
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ResourceConnection $resource,
        ProductMetadataInterface $productMetadata
    ) {
        $this->resource        = $resource;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Environment';
    }

    /**
     * @return array
     */
    public function capture()
    {
        $data = [
            self::MAGENTO_EDITION => [
                'label' => self::MAGENTO_EDITION,
                'value' => $this->productMetadata->getEdition(),
            ],
            self::MAGENTO_VERSION => [
                'label' => self::MAGENTO_VERSION,
                'value' => $this->productMetadata->getVersion(),
            ],
            self::PHP_VERSION     => [
                'label' => self::PHP_VERSION,
                'value' => phpversion(),
            ],
            self::MYSQL_VERSION   => [
                'label' => self::MYSQL_VERSION,
                'value' => $this->getMysqlVersion(),
            ],
            self::DOCUMENT_ROOT   => [
                'label' => self::DOCUMENT_ROOT,
                'value' => BP,
            ],
        ];

        return $data;
    }

    /**
     * @return string
     */
    private function getMysqlVersion()
    {
        $row = $this->resource->getConnection()->fetchRow('SHOW VARIABLES LIKE "version"');

        return isset($row['Value']) ? $row['Value'] : 'N/A';
    }
}
