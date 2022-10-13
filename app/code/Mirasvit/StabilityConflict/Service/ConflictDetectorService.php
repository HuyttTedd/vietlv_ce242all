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



namespace Mirasvit\StabilityConflict\Service;

use Magento\Framework\App\Area;
use Magento\Framework\Config\FileResolverInterface;
use Magento\Framework\ObjectManager\Config\Reader\Dom as DomReader;

class ConflictDetectorService
{
    /**
     * @var DomReader
     */
    private $reader;

    /**
     * @var FileResolverInterface
     */
    private $fileResolver;

    /**
     * ConflictDetectorService constructor.
     * @param DomReader $reader
     * @param FileResolverInterface $fileResolver
     */
    public function __construct(
        DomReader $reader,
        FileResolverInterface $fileResolver
    ) {
        $this->reader       = $reader;
        $this->fileResolver = $fileResolver;
    }

    /**
     * @return array
     */
    public function getConflicts()
    {
        $areas = [
            Area::AREA_DOC,
            Area::AREA_CRONTAB,
            Area::AREA_ADMINHTML,
            Area::AREA_GLOBAL,
            Area::AREA_FRONTEND,
            Area::AREA_WEBAPI_REST,
            Area::AREA_WEBAPI_SOAP,
        ];

        $preferences = [];
        foreach ($areas as $area) {
            $fileList = $this->fileResolver->get('di.xml', $area);
            foreach ($fileList as $key => $content) {
                try {
                    $dom = new \DOMDocument();
                    $dom->loadXML($content);

                    /** @var \DOMElement $preference */
                    foreach ($dom->getElementsByTagName('preference') as $preference) {
                        $for  = $preference->getAttribute('for');
                        $type = $preference->getAttribute('type');

                        if (!$for || !$type) {
                            continue;
                        }

                        if (!isset($preferences[$for])) {
                            $preferences[$for] = [];
                        }

                        if (!in_array($type, $preferences[$for])) {
                            $preferences[$for][] = $type;
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }

        $conflicts = [];
        foreach ($preferences as $parent => $rewrites) {
            if (count($rewrites) == 1) {
                continue;
            }

            $isNative = true;
            foreach ($rewrites as $rewrite) {
                $isMagento  = false;
                $isMirasvit = false;
                if (strpos($rewrite, 'Magento\\') === 0
                    || strpos($rewrite, '\\Magento\\') === 0) {
                    $isMagento = true;
                }

                if (strpos($rewrite, 'Mirasvit\\') === 0
                    || strpos($rewrite, '\\Mirasvit\\') === 0) {
                    $isMirasvit = true;
                }

                if (!$isMagento && !$isMirasvit) {
                    $isNative = false;
                }
            }

            if ($isNative) {
                continue;
            }

            $conflicts[$parent] = $rewrites;
        }

        return $conflicts;
    }
}
