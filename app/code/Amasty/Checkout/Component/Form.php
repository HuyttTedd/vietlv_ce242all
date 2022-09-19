<?php

namespace Amasty\Checkout\Component;

use Magento\Ui\Component\Form as UiFrom;

/**
 * Class Form
 */
class Form extends UiFrom
{
    /**
     * {@inheritdoc}
     */
    public function getDataSourceData()
    {
        return $this->getContext()->getDataProvider()->getData();
    }
}
