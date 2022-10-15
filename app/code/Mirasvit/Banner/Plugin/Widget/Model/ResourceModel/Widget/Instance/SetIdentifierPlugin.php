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
 * @package   mirasvit/module-banner
 * @version   1.0.11
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Plugin\Widget\Model\ResourceModel\Widget\Instance;

use Mirasvit\Banner\Block\Widget\Placeholder;

class SetIdentifierPlugin
{
    /**
     * @param mixed $subject
     * @param mixed $object
     * @return array
     */
    public function beforeSave($subject, $object)
    {
        /** @var \Magento\Widget\Model\Widget\Instance $object */
        if ($object->getData('instance_type') === Placeholder::class) {
            $params = $object->getWidgetParameters();
            if (empty($params) || !isset($params['position']) || !$params['position']) {
                $params['position'] = sha1(microtime(true));
            }
            $object->setData('widget_parameters', $params);
        }

        return [$object];
    }
}
