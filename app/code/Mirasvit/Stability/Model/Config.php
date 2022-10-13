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



namespace Mirasvit\Stability\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Config
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Config constructor.
     * @param Filesystem $fs
     */
    public function __construct(
        Filesystem $fs
    ) {
        $this->fs = $fs;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->fs->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath();
    }

    /**
     * @return string
     */
    public function getStoragePath()
    {
        $varDir = $this->fs->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();
        $path = $varDir . 'stability';

        if (is_writable($varDir) && !file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }
}
