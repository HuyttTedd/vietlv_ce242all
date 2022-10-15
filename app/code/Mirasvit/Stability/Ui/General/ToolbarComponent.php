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



namespace Mirasvit\Stability\Ui\General;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;

class ToolbarComponent extends AbstractComponent
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Toolbar\ButtonInterface[]
     */
    private $tabPool;

    /**
     * ToolbarComponent constructor.
     * @param RequestInterface $request
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     * @param array $tabPool
     */
    public function __construct(
        RequestInterface $request,
        ContextInterface $context,
        array $components = [],
        array $data = [],
        array $tabPool = []
    ) {
        $this->request = $request;
        $this->tabPool = $tabPool;

        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getComponentName()
    {
        return 'toolbar';
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');

        $config['tabs'] = [];


        foreach ($this->tabPool as $tab) {
            $extra = $tab->getExtra();

            $config['tabs'][] = [
                'label'     => $tab->getLabel(),
                'extra'     => $extra !== false ? $extra : '',
                'url'       => $this->context->getUrl('stability/dashboard/index', ['ui' => $tab->getUiName()]),
                'active'    => $this->request->getParam('ui', 'stability_alert_listing') === $tab->getUiName(),
                'sortOrder' => $tab->getSortOrder(),
            ];
        }

        usort($config['tabs'], function ($a, $b) {
            return $a['sortOrder'] - $b['sortOrder'];
        });

        $this->setData('config', $config);

        parent::prepare();
    }
}
