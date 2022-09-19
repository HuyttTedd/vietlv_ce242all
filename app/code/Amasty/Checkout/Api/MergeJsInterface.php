<?php

namespace Amasty\Checkout\Api;

interface MergeJsInterface
{
    /**
     * @param string[] $fileNames
     * @return boolean
     */
    public function createBundle(array $fileNames);
}
