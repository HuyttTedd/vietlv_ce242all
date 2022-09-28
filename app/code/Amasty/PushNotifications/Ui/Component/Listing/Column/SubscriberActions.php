<?php

namespace Amasty\PushNotifications\Ui\Component\Listing\Column;

use Amasty\PushNotifications\Api\Data\SubscriberInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class SubscriberActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_DELETE = 'amasty_notifications/subscriber/delete';

    /**
     * Customer Edit Url path
     */
    const URL_CUSTOMER_EDIT = 'customer/index/edit';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[SubscriberInterface::SUBSCRIBER_ID])) {
                    $item[$this->getData('name')]['remove'] = [
                        'href' => $this->urlBuilder->getUrl(
                            static::URL_PATH_DELETE,
                            [
                                'id' => $item[SubscriberInterface::SUBSCRIBER_ID]
                            ]
                        ),
                        'label' => __('Remove')
                    ];
                    if (!is_a($item[SubscriberInterface::CUSTOMER_ID], 'Magento\Framework\Phrase')) {
                        $item[$this->getData('name')]['open_customer'] = [
                            'href' => $this->urlBuilder->getUrl(
                                self::URL_CUSTOMER_EDIT,
                                [
                                    'id' => $item[SubscriberInterface::CUSTOMER_ID]
                                ]
                            ),
                            'label' => __('Open Customer')
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
