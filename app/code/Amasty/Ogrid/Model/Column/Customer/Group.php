<?php
namespace Amasty\Ogrid\Model\Column\Customer;

use Magento\Framework\Data\Collection;

class Group extends \Amasty\Ogrid\Model\Column
{
    public function addField(Collection $collection, $mainTableAlias = 'main_table')
    {
        $customerColumn = \Magento\Framework\App\ObjectManager::getInstance()->create('Amasty\\Ogrid\\Model\\Column\\Customer');
        $customerColumn->addField($collection);

        return parent::addField($collection, $customerColumn->getAlias());
    }
}