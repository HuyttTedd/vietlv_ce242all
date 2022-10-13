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



namespace Mirasvit\Banner\Ui\Banner\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Repository\BannerRepository;

class DataProvider extends AbstractDataProvider
{
    private $bannerRepository;

    /**
     * @param BannerRepository $bannerRepository
     * @param string           $name
     * @param string           $primaryFieldName
     * @param string           $requestFieldName
     * @param array            $meta
     * @param array            $data
     */
    public function __construct(
        BannerRepository $bannerRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->collection       = $this->bannerRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        /** @var BannerInterface $model */
        foreach ($this->collection as $model) {
            $data = [];

            foreach (array_keys($model->getData()) as $key) {
                $data[$key] = $model->getDataUsingMethod($key);
            }

            $result[$model->getId()] = $data;
        }

        return $result;
    }
}
