<?php

namespace Amasty\Checkout\Block\Adminhtml\Field\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Class Form
 */
class Form extends Generic
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', ['_current' => true]),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setHtmlIdPrefix('field_');
        $form->setUseContainer(true);

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
