<?php
namespace Amasty\Ogrid\Model\Column\Product;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Qty extends \Amasty\Ogrid\Model\Column\Product
{

    public function modifyItem(&$item, $config = [])
    {
        parent::modifyItem($item, $config);

        $item[$this->_alias_prefix . $this->_fieldKey] *= 1;
    }
}