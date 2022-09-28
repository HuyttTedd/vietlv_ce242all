<?php
declare(strict_types=1);

namespace Amasty\Ogrid\Model\Column\Order;

use Magento\Framework\Data\Collection;

class GrandTotalSubTax extends \Amasty\Ogrid\Model\Column\Order
{
    public function addField(Collection $collection, $mainTableAlias = 'main_table')
    {
        $alias = $this->getAlias();

        $from = $collection->getSelect()->getPart(\Zend_Db_Select::FROM);
        if (!array_key_exists($alias, $from)) {
            $collection->getSelect()->joinLeft(
                [
                    $alias => $this->_getMainTable()
                ],
                $this->_getFieldCondition($mainTableAlias),
                []
            );
        }
        $collection->getSelect()->columns(['amasty_ogrid_grand_total_sub_tax' => $this->getFieldExpression()]);

        foreach ($this->_columns as $column) {
            $collection->getSelect()->columns([
                $this->_alias_prefix . $column => $alias . '.' . $column
            ]);
        }
    }

    public function changeFilter(\Magento\Framework\Api\Filter $filter)
    {
        $filter->setField($this->getFieldExpression());
    }

    private function getFieldExpression(): \Zend_Db_Expr
    {
        $alias = $this->getAlias();

        return new \Zend_Db_Expr("GREATEST(($alias.base_grand_total - $alias.base_tax_amount), 0)");
    }
}
