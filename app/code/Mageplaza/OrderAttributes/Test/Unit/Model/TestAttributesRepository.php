<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Test\Unit\Model;

use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Helper\Media;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\AttributesRepository;
use Mageplaza\OrderAttributes\Model\FileResult;
use Mageplaza\OrderAttributes\Model\Quote as QuoteAttribute;
use Mageplaza\OrderAttributes\Model\QuoteFactory;
use Mageplaza\OrderAttributes\Model\QuoteFactory as QuoteAttributeFactory;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Zend_Validate_File_Upload;

/**
 * Class TestAttributesRepository
 * @package Mageplaza\OrderAttributes\Test\Unit\Model
 */
class TestAttributesRepository extends TestCase
{
    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    protected $searchCriteriaBuilderMock;

    /**
     * @var CollectionProcessorInterface|MockObject
     */
    protected $collectionProcessorMock;

    /**
     * @var CollectionFactory|MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var SearchResultsInterfaceFactory|MockObject
     */
    protected $searchResultsFactoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManagerMock;

    /**
     * @var Data|MockObject
     */
    protected $helperDataMock;

    /**
     * @var UploaderFactory|MockObject
     */
    protected $uploaderFactoryMock;

    /**
     * @var Filesystem|MockObject
     */
    protected $fileSystemMock;

    /**
     * @var Zend_Validate_File_Upload|MockObject
     */
    protected $fileUploadMock;

    /**
     * @var LoggerInterface|MockObject
     */
    protected $loggerMock;

    /**
     * @var CartRepositoryInterface|MockObject
     */
    protected $cartRepositoryMock;

    /**
     * @var QuoteIdMaskFactory|MockObject
     */
    private $quoteIdMaskFactoryMock;

    /**
     * @var QuoteFactory|MockObject
     */
    protected $quoteAttributeFactoryMock;

    /**
     * @var Media|MockObject
     */
    protected $swatchHelperMock;

    /**
     * @var Uploader|MockObject
     */
    private $uploaderMock;

    /**
     * @var Collection|MockObject
     */
    private $collectionMock;

    /**
     * @var AttributesRepository
     */
    private $attributeRepository;

    protected function setUp()
    {
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();

        $this->collectionProcessorMock = $this->getMockBuilder(CollectionProcessorInterface::class)
            ->getMock();
        $this->searchResultsFactoryMock = $this->getMockBuilder(SearchResultsInterfaceFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMock();
        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()->getMock();
        $this->cartRepositoryMock = $this->getMockBuilder(CartRepositoryInterface::class)
            ->getMock();
        $this->fileUploadMock = $this->getMockBuilder(Zend_Validate_File_Upload::class)
            ->disableOriginalConstructor()->getMock();
        $this->uploaderFactoryMock = $this->getMockBuilder(UploaderFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->quoteIdMaskFactoryMock = $this->getMockBuilder(QuoteIdMaskFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock = $this->getMockBuilder(QuoteAttributeFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->swatchHelperMock = $this->getMockBuilder(Media::class)
            ->disableOriginalConstructor()->getMock();
        $this->fileSystemMock = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()->getMock();
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $this->uploaderMock = $this->getMockBuilder(Uploader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeRepository = new AttributesRepository(
            $this->collectionFactoryMock,
            $this->collectionProcessorMock,
            $this->searchResultsFactoryMock,
            $this->searchCriteriaBuilderMock,
            $this->storeManagerMock,
            $this->helperDataMock,
            $this->cartRepositoryMock,
            $this->fileUploadMock,
            $this->uploaderFactoryMock,
            $this->quoteIdMaskFactoryMock,
            $this->quoteAttributeFactoryMock,
            $this->swatchHelperMock,
            $this->fileSystemMock,
            $this->loggerMock
        );
    }

    public function testGetListWithDisableModule()
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(false);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('The module is disabled');

        $this->attributeRepository->getList();
    }

    /**
     * @return array
     */
    public function providerTestGetListWithEmptyItems()
    {
        return [
            [null],
            [true]
        ];
    }

    /**
     * @param null|boolean $searchCriteria
     *
     * @throws LocalizedException
     * @dataProvider providerTestGetListWithEmptyItems
     */
    public function testGetListWithEmptyItems($searchCriteria)
    {
        $size = 0;
        $items = [];
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        if ($searchCriteria) {
            /**
             * @var SearchCriteriaInterface|MockObject $searchCriteria
             */
            $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)->getMock();
            $searchCriteria = $searchCriteriaMock;
        } else {
            $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
                ->disableOriginalConstructor()->getMock();
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('create')
                ->willReturn($searchCriteriaMock);
        }

        $attributeCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeCollection);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')->with($searchCriteriaMock, $attributeCollection);
        $attributeCollection->expects($this->exactly(2))
            ->method('getItems')->willReturn($items);
        $attributeCollection->expects($this->once())
            ->method('getSize')->willReturn($size);
        $searchResultMock = $this->getMockBuilder(SearchResultsInterface::class)
            ->getMock();
        $this->searchResultsFactoryMock->expects($this->once())->method('create')->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())->method('setSearchCriteria')->willReturn($searchCriteria);
        $searchResultMock->expects($this->once())->method('setItems')->willReturn($items);
        $searchResultMock->expects($this->once())->method('setTotalCount')->willReturn($size);

        $this->assertEquals($searchResultMock, $this->attributeRepository->getList($searchCriteria));
    }

    /**
     * @return array
     */
    public function providerTestGetList()
    {
        $additionalDataMock = [
            'json' => '',
            'result' => []
        ];
        $optionsMock = $additionalDataMock;

        return [
            [
                null,
                $additionalDataMock,
                $optionsMock,
                false
            ],
            [
                null,
                [
                    'json' => '{\"option_0\":{\"swatch_value\":\"#f70f1e\",\"swatch_type\":1}}',
                    'json_decode' => [
                        'option_0' => [
                            'swatch_type' => 1,
                            'swatch_value' => 'f70f1e'
                        ]
                    ],
                    'result' => '{\"option_0\":{\"swatch_value\":\"#f70f1e\",\"swatch_type\":1}}',
                ],
                $optionsMock,
                false
            ],
            [
                null,
                [
                    'json' => '{\"option_0\":{\"swatch_value\":\"some url\",\"swatch_type\":2}}',
                    'json_decode' => [
                        'option_0' => [
                            'swatch_type' => 2,
                            'swatch_value' => 'some url'
                        ]
                    ],
                    'result' => '{\"option_0\":{\"swatch_value\":\"some url\",\"swatch_type\":2}}',
                ],
                $optionsMock,
                true
            ],
            [
                null,
                [
                    'json' => '{\"option_0\":{\"swatch_value\":\"some url\",\"swatch_type\":2}}',
                    'json_decode' => [
                        'option_0' => [
                            'swatch_type' => 2,
                            'swatch_value' => 'some url'
                        ]
                    ],
                    'result' => '{\"option_0\":{\"swatch_value\":\"some url\",\"swatch_type\":2}}',
                ],
                [
                    'json' => '{}',
                    'json_decode' => [],
                    'json_decode_format' => [],
                    'result' => '{}',
                ],
                true
            ],
            [
                null,
                [
                    'json' => '{\"option_0\":{\"swatch_value\":\"some url\",\"swatch_type\":2}}',
                    'json_decode' => [
                        'option_0' => [
                            'swatch_type' => 2,
                            'swatch_value' => 'some url'
                        ]
                    ],
                    'result' => '{\"option_0\":{\"swatch_value\":\"some url\",\"swatch_type\":2}}',
                ],
                [
                    'json' => '{"delete":[],"option":{"value":{"option_0":["My option"]}},"default":"option_0"}',
                    'json_decode' => [

                        'delete' => [],
                        'option' => [
                            'value' => ['option_0' => ['My option']]
                        ],
                        'default' => 'option_0'
                    ],
                    'json_decode_format' => [

                        'options' => ['option_0' => ['My option']],
                        'default' => 'option_0'
                    ],
                    'result' => '{"options":{"option_0":["My option"]},"default":"option_0"}',
                ],
                true
            ],
        ];
    }

    /**
     * @param null|boolean $searchCriteria
     * @param array $additionalDataMock
     * @param array $optionsMock
     * @param boolean $isSwatchTypeVisualImage
     *
     * @dataProvider providerTestGetList
     *
     * @throws LocalizedException
     */
    public function testGetList($searchCriteria, $additionalDataMock, $optionsMock, $isSwatchTypeVisualImage)
    {
        $this->helperDataMock->expects($this->once())->method('isEnabled')->willReturn(true);
        if ($searchCriteria) {
            /**
             * @var SearchCriteriaInterface|MockObject $searchCriteria
             */
            $searchCriteriaMock = $this->getMockBuilder(SearchCriteriaInterface::class)->getMock();
            $searchCriteria = $searchCriteriaMock;
        } else {
            $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
                ->disableOriginalConstructor()->getMock();
            $this->searchCriteriaBuilderMock->expects($this->once())
                ->method('create')
                ->willReturn($searchCriteriaMock);
        }

        $attributeCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeCollection);
        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $attributeCollection);
        $orderAttribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $items = [$orderAttribute];
        $size = 1;

        $orderAttribute->expects($this->atLeastOnce())
            ->method('getAdditionalData')
            ->willReturn($additionalDataMock['json']);
        $orderAttribute->expects($this->atLeastOnce())
            ->method('getOptions')
            ->willReturn($optionsMock['json']);
        $count = 1;
        if ($additionalDataMock['json']) {
            $this->helperDataMock->expects($this->at($count))
                ->method('jsonDecodeData')
                ->with($additionalDataMock['json'])
                ->willReturn($additionalDataMock['json_decode']);
            $count++;
            if ($isSwatchTypeVisualImage) {
                $this->swatchHelperMock->expects($this->once())
                    ->method('getSwatchAttributeImage')
                    ->with(
                        'swatch_thumb',
                        $additionalDataMock['json_decode']['option_0']['swatch_value']
                    )
                    ->willReturn($additionalDataMock['json_decode']['option_0']['swatch_value']);
            }
            $this->helperDataMock->expects($this->at($count))
                ->method('jsonEncodeData')
                ->with($additionalDataMock['json_decode'])
                ->willReturn($additionalDataMock['result']);
            $orderAttribute->expects($this->once())
                ->method('setAdditionalData')
                ->with($additionalDataMock['result'])
                ->willReturnSelf();
            $count++;
        }

        if ($optionsMock['json']) {
            $this->helperDataMock->expects($this->at($count))
                ->method('jsonDecodeData')
                ->with($optionsMock['json'])
                ->willReturn($optionsMock['json_decode']);
            $count++;
            $this->helperDataMock->expects($this->at($count))
                ->method('jsonEncodeData')
                ->with($optionsMock['json_decode_format'])
                ->willReturn($optionsMock['result']);
            $orderAttribute->expects($this->once())
                ->method('setOptions')
                ->with($optionsMock['result'])
                ->willReturnSelf();
        }

        $attributeCollection->expects($this->exactly(2))
            ->method('getItems')->willReturn($items);
        $attributeCollection->expects($this->once())
            ->method('getSize')->willReturn($size);
        $searchResultMock = $this->getMockBuilder(SearchResultsInterface::class)
            ->getMock();
        $this->searchResultsFactoryMock->expects($this->once())->method('create')->willReturn($searchResultMock);
        $searchResultMock->expects($this->once())->method('setSearchCriteria')->willReturn($searchCriteria);
        $searchResultMock->expects($this->once())->method('setItems')->willReturn($items);
        $searchResultMock->expects($this->once())->method('setTotalCount')->willReturn($size);

        $this->assertEquals($searchResultMock, $this->attributeRepository->getList($searchCriteria));
    }

    /**
     * Only test guest upload with exception to check quote id mask.
     * On case guest upload is the same upload
     */
    public function testGuestUploadWithException()
    {
        $cartId = 'YphgiOOIutUAvvpev8agDIXACxThDg9F';
        $quoteIdMaskMock = $this->getMockBuilder(QuoteIdMask::class)->setMethods(['load', 'getQuoteId'])
            ->disableOriginalConstructor()->getMock();
        $this->quoteIdMaskFactoryMock->expects($this->once())->method('create')->willReturn($quoteIdMaskMock);
        $quoteIdMaskMock->expects($this->once())->method('load')->with($cartId, 'masked_id')->willReturnSelf();
        $quoteId = 1;
        $quoteIdMaskMock->expects($this->once())->method('getQuoteId')->willReturn($quoteId);

        $this->cartRepositoryMock->expects($this->once())->method('getActive')->with($quoteId);
        $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn(null);

        $result = new FileResult(['error' => __('File is empty.')]);

        $this->assertEquals($result, $this->attributeRepository->guestUpload($cartId));
    }

    public function testUploadWithException()
    {
        $cartId = 1;
        $this->cartRepositoryMock->expects($this->once())->method('getActive')->with($cartId);
        $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn(null);

        $result = new FileResult(['error' => __('File is empty.')]);

        $this->assertEquals($result, $this->attributeRepository->upload($cartId));
    }

    /**
     * @return array
     */
    public function providerTestUploadWithNoSuchEntityException()
    {
        $orderAttribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        return [
            [
                $orderAttribute,
                null
            ],
            [
                [],
                null
            ]
        ];
    }

    /**
     * @param MockObject $attribute
     * @param int|null $id
     *
     * @dataProvider providerTestUploadWithNoSuchEntityException
     */
    public function testUploadWithNoSuchEntityException($attribute, $id)
    {
        $cartId = 1;
        $this->cartRepositoryMock->expects($this->once())->method('getActive')->with($cartId);
        $attributeCode = 'my_attribute';
        $files = [
            $attributeCode => [
                'name' => 'test.zip',
                'type' => 'application/zip',
                'tmp_name' => '/tmp/phpyqG1oI',
                'error' => 0,
                'size' => 21812
            ]
        ];
        $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn($files);

        $this->uploaderFactoryMock->expects($this->once())->method('create')
            ->with(['fileId' => $files[$attributeCode]])
            ->willReturn($this->uploaderMock);
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($this->collectionMock);
        $this->collectionMock->expects($this->once())->method('addFieldToFilter')
            ->with('attribute_code', $attributeCode)->willReturnSelf();
        $this->collectionMock->expects($this->once())->method('fetchItem')
            ->willReturn($attribute);
        if ($attribute) {
            $attribute->expects($this->once())->method('getId')->willReturn($id);
        }

        $result = new FileResult(['error' => __('No such entity id!')]);

        $this->assertEquals($result, $this->attributeRepository->upload($cartId));
    }

    /**
     * @return array
     */
    public function providerTestUploadWithMaxFileSizeException()
    {
        $files = [
            'my_attribute' => [
                'name' => 'test.zip',
                'type' => 'application/zip',
                'tmp_name' => '/tmp/phpyqG1oI',
                'error' => 0,
                'size' => 21812
            ]
        ];

        $image = [
            'my_attribute' => [
                'name' => 'test.png',
                'type' => 'image/png',
                'tmp_name' => '/tmp/phpyqG1oI',
                'error' => 0,
                'size' => 21812
            ]
        ];

        return [
            [$files, 'file', true],
            [$image, 'image', false]
        ];
    }

    /**
     * @param array $files
     * @param string $frontendInput
     * @param string $allowExtensions
     *
     * @dataProvider providerTestUploadWithMaxFileSizeException
     */
    public function testUploadWithMaxFileSizeException($files, $frontendInput, $allowExtensions)
    {
        $cartId = 1;
        $this->cartRepositoryMock->expects($this->once())->method('getActive')->with($cartId);
        $attributeCode = 'my_attribute';
        $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn($files);

        $this->uploaderFactoryMock->expects($this->once())->method('create')
            ->with(['fileId' => $files[$attributeCode]])
            ->willReturn($this->uploaderMock);
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($this->collectionMock);
        $this->collectionMock->expects($this->once())->method('addFieldToFilter')
            ->with('attribute_code', $attributeCode)->willReturnSelf();
        $orderAttribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionMock->expects($this->once())->method('fetchItem')
            ->willReturn($orderAttribute);
        if ($orderAttribute) {
            $orderAttribute->expects($this->once())->method('getId')->willReturn(1);
        }

        $orderAttribute->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        if ($frontendInput === 'image') {
            $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')
                ->with(['jpg', 'jpeg', 'gif', 'png'])->willReturnSelf();
        } elseif ($allowExtensions) {
            $allowExtensions = 'zip';
            $orderAttribute->expects($this->exactly(2))->method('getAllowExtensions')
                ->willReturn($allowExtensions);
            $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')->with([$allowExtensions])
                ->willReturnSelf();
        }
        $orderAttribute->expects($this->exactly(3))->method('getMaxFileSize')->willReturn(2000);

        $result = new FileResult(['error' => __($files[$attributeCode]['name'] . ' must be less than or equal to 2000 bytes.')]);

        $this->assertEquals($result, $this->attributeRepository->upload($cartId));
    }

    /**
     * @return array
     */
    public function providerTestUpload()
    {
        $files = [
            'my_attribute' => [
                'name' => 'test',
                'type' => 'application/zip',
                'tmp_name' => '/tmp/phpyqG1oI',
                'error' => 0,
                'size' => 111
            ]
        ];

        $image = [
            'my_attribute' => [
                'name' => 'test',
                'type' => 'image/png',
                'tmp_name' => '/tmp/phpyqG1oI',
                'error' => 0,
                'size' => 333
            ]
        ];

        $jsonResult = '{"name":"test","file":"test","url":"test"}';

        return [
            [
                [
                    'name' => 'test',
                    'file' => 'test',
                    'url' => 'some tmp media url'
                ],
                $jsonResult,
                $files,
                'file',
                true
            ],
            [
                [
                    'name' => 'test',
                    'file' => 'test',
                    'url' => 'some tmp media url'
                ],
                $jsonResult,
                $image,
                'image',
                false
            ]
        ];
    }

    /**
     * @param array $result
     * @param string $jsonResult
     * @param array $files
     * @param string $frontendInput
     * @param string $allowExtensions
     *
     * @dataProvider providerTestUpload
     */
    public function testUpload($result, $jsonResult, $files, $frontendInput, $allowExtensions)
    {
        $cartId = 1;
        $quoteMock = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $this->cartRepositoryMock->expects($this->once())->method('getActive')->with($cartId)->willReturn($quoteMock);
        $attributeCode = 'my_attribute';
        $this->fileUploadMock->expects($this->once())->method('getFiles')->willReturn($files);

        $this->uploaderFactoryMock->expects($this->once())->method('create')
            ->with(['fileId' => $files[$attributeCode]])
            ->willReturn($this->uploaderMock);
        $this->collectionFactoryMock->expects($this->once())->method('create')->willReturn($this->collectionMock);
        $this->collectionMock->expects($this->once())->method('addFieldToFilter')
            ->with('attribute_code', $attributeCode)->willReturnSelf();
        $orderAttribute = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collectionMock->expects($this->once())->method('fetchItem')
            ->willReturn($orderAttribute);
        if ($orderAttribute) {
            $orderAttribute->expects($this->once())->method('getId')->willReturn(1);
        }

        $orderAttribute->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        if ($frontendInput === 'image') {
            $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')
                ->with(['jpg', 'jpeg', 'gif', 'png'])->willReturnSelf();
        } elseif ($allowExtensions) {
            $allowExtensions = 'zip';
            $orderAttribute->expects($this->exactly(2))->method('getAllowExtensions')
                ->willReturn($allowExtensions);
            $this->uploaderMock->expects($this->once())->method('setAllowedExtensions')->with([$allowExtensions])
                ->willReturnSelf();
        }
        $orderAttribute->expects($this->exactly(2))->method('getMaxFileSize')->willReturn(2000);

        $this->uploaderMock->expects($this->once())->method('setAllowRenameFiles')->with(true)->willReturnSelf();
        $this->uploaderMock->expects($this->once())->method('setFilesDispersion')->with(true)->willReturnSelf();
        $directoryRead = $this->getMockBuilder(ReadInterface::class)->getMock();
        $this->fileSystemMock->expects($this->once())->method('getDirectoryRead')->with(DirectoryList::MEDIA)
            ->willReturn($directoryRead);
        $path = 'magento_root/pub/media/' . Data::TEMPLATE_MEDIA_PATH . '/tmp';
        $this->helperDataMock->expects($this->once())
            ->method('getBaseTmpMediaPath')
            ->willReturn(Data::TEMPLATE_MEDIA_PATH . '/tmp');
        $directoryRead->expects($this->once())->method('getAbsolutePath')
            ->willReturn($path);
        $resultSave = [
            'name' => 'test',
            'tmp_name' => '/tmp/phpgSzJsB',
            'path' => $path,
            'file' => 'test'
        ];
        $this->uploaderMock->expects($this->once())
            ->method('save')
            ->with('magento_root/pub/media/' . Data::TEMPLATE_MEDIA_PATH . '/tmp')
            ->willReturn($resultSave);

        $this->helperDataMock->expects($this->once())->method('getTmpMediaUrl')
            ->with($resultSave['file'])->willReturn('some tmp media url');

        $quoteAttributeMock = $this->getMockBuilder(QuoteAttribute::class)->disableOriginalConstructor()->getMock();
        $this->quoteAttributeFactoryMock->expects($this->once())->method('create')->willReturn($quoteAttributeMock);
        $quoteMock->expects($this->exactly(2))->method('getId')->willReturn($cartId);
        $quoteAttributeMock->expects($this->once())->method('load')->with($cartId)->willReturnSelf();

        $this->helperDataMock->expects($this->once())->method('jsonEncodeData')->with($result)->willReturn($jsonResult);
        $quoteAttributeMock->expects($this->once())->method('saveAttributeData')
            ->with($cartId, [$attributeCode => $jsonResult])->willReturnSelf();

        $result = new FileResult($result);

        $this->assertEquals($result, $this->attributeRepository->upload($cartId));
    }
}
