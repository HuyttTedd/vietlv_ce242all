<?php
namespace Amasty\Ogrid\Model\Column;

use Magento\Framework\Data\Collection;

class Tracking extends \Amasty\Ogrid\Model\Column
{
    public function addFieldToSelect($collection)
    {
        $collection->getSelect()->columns([
            $this->getAlias() =>  $this->_fieldKey
        ]);
    }

    public function getAlias()
    {
        return $this->_alias_prefix . 'sales_shipment_track';
    }

    public function addField(Collection $collection, $mainTableAlias = 'main_table')
    {
        return ;
    }
}