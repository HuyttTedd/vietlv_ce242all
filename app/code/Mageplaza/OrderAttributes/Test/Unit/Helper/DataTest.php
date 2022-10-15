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

namespace Mageplaza\OrderAttributes\Test\Unit\Helper;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Form\Filter\Escapehtml;
use Magento\Framework\Data\Form\Filter\Striptags;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\OfflineShipping\Model\Carrier\Freeshipping;
use Magento\Quote\Model\Quote;
use Magento\Shipping\Model\Config as CarrierConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\Order;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\Collection;
use Mageplaza\OrderAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Temando\Shipping\Model\Shipping\Carrier;

/**
 * Class DataTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Helper
 */
class DataTest extends TestCase
{
    /**
     * @var Session
     */
    private $customerSessionMock;

    /**
     * @var CarrierConfig|MockObject
     */
    private $carrierConfigMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Repository|MockObject
     */
    private $repositoryMock;

    /**
     * @var Json|MockObject
     */
    private $jsonMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManagerMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var EncoderInterface|MockObject
     */
    private $urlEncoder;

    /**
     * @var Manager|MockObject
     */
    private $moduleManager;

    /**
     * @return Http|MockObject
     */
    private $request;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var array
     */
    protected $optionsInvalid = [];

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()->getMock();
        $this->urlBuilderMock = $this->getMockBuilder(UrlInterface::class)->getMock();
        $this->urlEncoder = $this->getMockBuilder(EncoderInterface::class)->getMock();
        $this->moduleManager = $this->getMockBuilder(Manager::class)
            ->disableOriginalConstructor()->getMock();
        $this->request = $this->getMockBuilder(Http::class)->disableOriginalConstructor()
            ->getMock();

        $contextMock->expects($this->once())->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $contextMock->expects($this->once())->method('getUrlEncoder')->willReturn($this->urlEncoder);
        $contextMock->expects($this->once())->method('getModuleManager')->willReturn($this->moduleManager);
        $contextMock->expects($this->once())->method('getRequest')->willReturn($this->request);
        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)->getMock();
        $this->customerSessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()->getMock();
        $this->carrierConfigMock = $this->getMockBuilder(CarrierConfig::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()->getMock();
        $this->repositoryMock = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()->getMock();
        $this->jsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()->getMock();

        $this->helperData = new Data(
            $contextMock,
            $this->objectManagerMock,
            $this->storeManagerMock,
            $this->customerSessionMock,
            $this->carrierConfigMock,
            $this->collectionFactoryMock,
            $this->repositoryMock,
            $this->jsonMock
        );
    }

    public function testJsonEncodeData()
    {
        $value = [
            'my_attribute' => 'test'
        ];
        $result = '{"my_attribute":"test"}';

        $this->jsonMock->expects($this->once())->method('serialize')->with($value)->willReturn($result);

        $this->helperData->jsonEncodeData($value);
    }

    public function testJsonDecodeData()
    {
        $result = [
            'my_attribute' => 'test'
        ];
        $value = '{"my_attribute":"test"}';

        $this->jsonMock->expects($this->once())->method('unserialize')->with($value)->willReturn($result);

        $this->helperData->jsonDecodeData($value);
    }

    /**
     * @return array
     */
    public function providerGetDefaultValueByInput()
    {
        return [
            ['default_value_text', 'text'],
            ['default_value_textarea', 'textarea'],
            ['default_value_date', 'date'],
            ['default_value_yesno', 'boolean'],
            [false, 'select'],
            [false, 'multiselect'],
            [false, 'select_visual'],
            [false, 'multiselect_visual'],
            [false, 'image'],
            [false, 'file'],
            ['default_value_content', 'textarea_visual']
        ];
    }

    /**
     * @param string $result
     * @param string $inputType
     *
     * @dataProvider providerGetDefaultValueByInput
     */
    public function testGetDefaultValueByInput($result, $inputType)
    {
        $this->assertEquals($result, $this->helperData->getDefaultValueByInput($inputType));
    }

    /**
     * @return array
     */
    public function providerGetBackendTypeByInputType()
    {
        return [
            ['varchar', 'text'],
            ['text', 'textarea'],
            ['datetime', 'date'],
            ['int', 'boolean'],
            ['varchar', 'select'],
            ['varchar', 'multiselect'],
            ['varchar', 'select_visual'],
            ['varchar', 'multiselect_visual'],
            ['text', 'image'],
            ['text', 'file'],
            ['text', 'textarea_visual']
        ];
    }

    /**
     * @param string $result
     * @param string $inputType
     *
     * @dataProvider providerGetBackendTypeByInputType
     */
    public function testGetBackendTypeByInputType($result, $inputType)
    {
        $this->assertEquals($result, $this->helperData->getBackendTypeByInputType($inputType));
    }

    /**
     * @return array
     */
    public function providerTestGetFieldTypeByInputType()
    {
        return [
            ['text', 'text'],
            ['textarea', 'textarea'],
            ['date', 'date'],
            ['select', 'boolean'],
            ['select', 'select'],
            ['multiselect', 'multiselect'],
            ['select', 'select_visual'],
            ['multiselect', 'multiselect_visual'],
            ['file', 'image'],
            ['file', 'file'],
            ['content', 'textarea_visual']
        ];
    }

    /**
     * @param string $result
     * @param string $inputType
     *
     * @dataProvider providerTestGetFieldTypeByInputType
     */
    public function testGetFieldTypeByInputType($result, $inputType)
    {
        $this->assertEquals($result, $this->helperData->getFieldTypeByInputType($inputType));
    }

    /**
     * @return array
     */
    public function providerGetComponentByInputType()
    {
        return [
            ['Magento_Ui/js/form/element/abstract', 'text'],
            ['Magento_Ui/js/form/element/textarea', 'textarea'],
            ['Mageplaza_OrderAttributes/js/form/element/date', 'date'],
            ['Mageplaza_OrderAttributes/js/form/element/select', 'boolean'],
            ['Mageplaza_OrderAttributes/js/form/element/select', 'select'],
            ['Magento_Ui/js/form/element/multiselect', 'multiselect'],
            ['Mageplaza_OrderAttributes/js/form/element/select', 'select_visual'],
            ['Mageplaza_OrderAttributes/js/form/element/checkboxes', 'multiselect_visual'],
            ['Mageplaza_OrderAttributes/js/form/element/file-uploader', 'image'],
            ['Mageplaza_OrderAttributes/js/form/element/file-uploader', 'file'],
            ['Mageplaza_OrderAttributes/js/form/element/textarea', 'textarea_visual']
        ];
    }

    /**
     * @param string $result
     * @param string $inputType
     *
     * @dataProvider providerGetComponentByInputType
     */
    public function testGetComponentByInputType($result, $inputType)
    {
        $this->assertEquals($result, $this->helperData->getComponentByInputType($inputType));
    }

    /**
     * @return array
     */
    public function providerGetElementTmplByInputType()
    {
        return [
            ['ui/form/element/input', 'text'],
            ['ui/form/element/textarea', 'textarea'],
            ['ui/form/element/date', 'date'],
            ['ui/form/element/select', 'boolean'],
            ['ui/form/element/select', 'select'],
            ['ui/form/element/multiselect', 'multiselect'],
            ['Mageplaza_OrderAttributes/form/element/radio-visual', 'select_visual'],
            ['Mageplaza_OrderAttributes/form/element/checkbox-visual', 'multiselect_visual'],
            ['ui/form/element/uploader/uploader', 'image'],
            ['ui/form/element/uploader/uploader', 'file'],
            ['ui/form/element/textarea', 'textarea_visual']
        ];
    }

    /**
     * @param string $result
     * @param string $inputType
     *
     * @dataProvider providerGetElementTmplByInputType
     */
    public function testGetElementTmplByInputType($result, $inputType)
    {
        $this->assertEquals($result, $this->helperData->getElementTmplByInputType($inputType));
    }

    /**
     * @return array
     */
    public function providerTestGetOrderAttributesCollection()
    {
        return [
            [1, false, []],
            [1, false, ['field' => 'my_attribute', 'condition' => ['eq' => 1]]],
            [1, true, ['field' => 'my_attribute', 'condition' => ['eq' => 1]]],
            [0, true, ['field' => 'my_attribute', 'condition' => ['eq' => 1]], 0]
        ];
    }

    /**
     * @param int $result
     * @param boolean $isCheckVisible
     * @param array $filters
     * @param int $position
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerTestGetOrderAttributesCollection
     */
    public function testGetOrderAttributesCollection(
        $result,
        $isCheckVisible,
        $filters,
        $position = 1
    ) {
        $orderAttributeCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderAttributeCollectionMock);

        if ($filters) {
            $orderAttributeCollectionMock->expects($this->once())
                ->method('addFieldToFilter')
                ->with($filters['field'], $filters['condition'])
                ->willReturnSelf();

            $filters = [$filters['field'] => $filters['condition']];
        }

        $orderAttribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $orderAttributeCollectionMock->expects($this->once())->method('getItems')->willReturn([$orderAttribute]);
        if ($isCheckVisible) {
            $orderAttribute->expects($this->once())->method('getStoreId')->willReturn('1,2');
            $orderAttribute->expects($this->once())->method('getCustomerGroup')->willReturn('1,2,3');
            $orderAttribute->expects($this->once())->method('getPosition')->willReturn($position);
        }

        $orderAttributes = $this->helperData->getOrderAttributesCollection(
            1,
            1,
            $isCheckVisible,
            $filters
        );

        $this->assertCount($result, $orderAttributes);
    }

    /**
     * @return array
     */
    public function providerTestIsVisible()
    {
        return [
            [true, 1, 1, 1],
            [true, 1, 1, 1],
            [false, 1, 1, 0]
        ];
    }

    /**
     * Base logic only test default value in method.
     * Case getScopeId see testGetScopeId
     * Case getGroupId see testGetGroupId
     *
     * @param boolean $result
     * @param int $storeId
     * @param int $groupId
     * @param string $position
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerTestIsVisible
     */
    public function testIsVisible($result, $storeId, $groupId, $position)
    {
        /**
         * @var Attribute|MockObject $orderAttribute
         */
        $orderAttribute = $this->getMockBuilder(Attribute::class)->disableOriginalConstructor()->getMock();
        $storeIds = '0,1,2';
        $orderAttribute->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeIds);
        $groupIds = '1,2,3';
        $orderAttribute->expects($this->atLeastOnce())->method('getCustomerGroup')->willReturn($groupIds);
        $orderAttribute->expects($this->atLeastOnce())->method('getPosition')->willReturn($position);

        $this->assertEquals($result, $this->helperData->isVisible($orderAttribute, $storeId, $groupId));
    }

    /**
     * @return array
     */
    public function providerGetScopeId()
    {
        return [
            [2, 2, 0],
            [1, 0, 0],
            [2, 1, 1]
        ];
    }

    /**
     * @param int $result
     * @param int $scopeStore
     * @param int $scopeWebsite
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerGetScopeId
     */
    public function testGetScopeId($result, $scopeStore, $scopeWebsite)
    {
        $this->request->expects($this->at(0))
            ->method('getParam')
            ->with(ScopeInterface::SCOPE_STORE)
            ->willReturn($scopeStore);
        if (!$scopeStore) {
            $storeMock = $this->getMockBuilder(Store::class)
                ->disableOriginalConstructor()
                ->getMock();
            $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);
            $storeId = 1;
            $storeMock->expects($this->once())->method('getId')->willReturn($storeId);
        }

        $this->request->expects($this->at(1))
            ->method('getParam')
            ->with(ScopeInterface::SCOPE_WEBSITE)
            ->willReturn($scopeWebsite);
        if ($scopeWebsite) {
            $websiteMock = $this->getMockBuilder(Website::class)
                ->disableOriginalConstructor()
                ->getMock();
            $defaultStore = $this->getMockBuilder(Store::class)
                ->disableOriginalConstructor()
                ->getMock();
            $website = 1;

            $this->storeManagerMock->expects($this->once())
                ->method('getWebsite')
                ->with($website)
                ->willReturn($websiteMock);
            $websiteMock->expects($this->once())->method('getDefaultStore')->willReturn($defaultStore);
            $defaultStore->expects($this->once())->method('getId')->willReturn(2);
        }

        $this->assertEquals($result, $this->helperData->getScopeId());
    }

    /**
     * @return array
     */
    public function providerTestGetGroupId()
    {
        return [
            [0, 0],
            [1, 1]
        ];
    }

    /**
     * @param int $result
     * @param int $isLoggedIn
     *
     * @dataProvider providerTestGetGroupId
     */
    public function testGetGroupId($result, $isLoggedIn)
    {
        $this->customerSessionMock->expects($this->once())->method('isLoggedIn')->willReturn($isLoggedIn);
        if ($isLoggedIn) {
            $groupId = 1;
            $customerMock = $this->getMockBuilder(Customer::class)->disableOriginalConstructor()->getMock();
            $this->customerSessionMock->expects($this->once())->method('getCustomer')->willReturn($customerMock);
            $customerMock->expects($this->once())->method('getGroupId')->willReturn($groupId);
        }

        $this->assertEquals($result, $this->helperData->getGroupId());
    }

    /**
     * @return array
     */
    public function providerTestValidateBoolean()
    {
        return [
            ['0'],
            ['1'],
            [0],
            [1],
            [true],
            [false]
        ];
    }

    /**
     * @param $value
     *
     * @throws InputException
     * @dataProvider providerTestValidateBoolean
     */
    public function testValidateBoolean($value)
    {
        $this->assertTrue($this->helperData->validateBoolean('my_attribute', $value));
    }

    /**
     * @throws InputException
     */
    public function testValidateBooleanWithException()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('my_attribute invalid');

        $this->helperData->validateBoolean('my_attribute', '2');
    }

    public function testIsValidDate()
    {
        $this->assertTrue($this->helperData->isValidDate('01-05-2020'));
    }

    public function testIsValidDateWithException()
    {
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Invalid date');

        $this->helperData->isValidDate('11111');
    }

    /**
     * @return array
     */
    public function providerValidateFile()
    {
        $fileUpload = '{"file":"abc\/test.zip","name":"test.zip","size":1000,"url":"example_url"}';
        $fileUploadDecode = [
            'file' => 'abc/test.zip',
            'name' => 'test.zip',
            'size' => 1000,
            'url' => 'example_url'
        ];

        return [
            [
                $fileUpload,
                $fileUploadDecode,
                '{"file":"abc\/test.zip","name":"abc.zip","size":1000,"url":"example_url"}',
                [
                    'file' => 'abc/test.zip',
                    'name' => 'abc.zip',
                    'size' => 1000,
                    'url' => 'example_url'
                ]
            ],
            [
                $fileUpload,
                $fileUploadDecode,
                '{"file":"abc\/test.zip","name":"abc.zip","size":1000,"url":"example_url"}',
                [
                    'file' => 'abc/test.zip',
                    'size' => 10000,
                    'url' => 'example_url'
                ]
            ]
        ];
    }

    /**
     * @param string $fileUpload
     * @param array $fileUploadDecode
     * @param string $fileDb
     * @param array $fileDbDecode
     *
     * @throws InputException
     * @dataProvider providerValidateFile
     */
    public function testValidateFileWithException($fileUpload, $fileUploadDecode, $fileDb, $fileDbDecode)
    {
        $this->jsonMock->expects($this->at(0))
            ->method('unserialize')->with($fileUpload)->willReturn($fileUploadDecode);
        $this->jsonMock->expects($this->at(1))
            ->method('unserialize')->with($fileDb)->willReturn($fileDbDecode);
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Something went wrong while uploading file (attribute my_attribute)');

        $this->helperData->validateFile($fileUpload, $fileDb, 'my_attribute');
    }

    public function testValidateFile()
    {
        $fileUpload = '{"file":"abc\/test.zip","name":"test.zip","size":1000,"url":"example_url"}';
        $fileUploadDecode = [
            'file' => 'abc/test.zip',
            'name' => 'test.zip',
            'size' => 1000,
            'url' => 'example_url'
        ];
        $fileDb = $fileUpload;
        $fileDbDecode = $fileUploadDecode;
        $this->jsonMock->expects($this->at(0))
            ->method('unserialize')->with($fileUpload)->willReturn($fileUploadDecode);
        $this->jsonMock->expects($this->at(1))
            ->method('unserialize')->with($fileDb)->willReturn($fileDbDecode);

        $this->assertTrue($this->helperData->validateFile($fileUpload, $fileDb, 'my_attribute'));
    }

    /**
     * @return array
     */
    public function providerPrepareAttributesWithBooleanValue()
    {
        return [
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute_option' => new Phrase('Yes'),
                    'my_attribute' => '1'
                ],
                1,
                ['my_attribute' => '1']
            ],
            [
                [
                    'my_attribute_label' => 'Label 2',
                    'my_attribute_option' => new Phrase('No'),
                    'my_attribute' => '0'
                ],
                2,
                ['my_attribute' => '0']
            ],
            [
                [],
                2,
                ['my_attribute_1' => '1']

            ]
        ];
    }

    /**
     * @param string $frontendInput
     * @param string $param
     * @param array $returnValue
     *
     * @return Attribute|MockObject
     */
    public function initPrepareAttributes($frontendInput, $param = '', $returnValue = [])
    {
        $orderAttributeCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderAttributeCollectionMock);

        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderAttributeCollectionMock->expects($this->once())->method('getItems')->willReturn([$orderAttributeMock]);
        $orderAttributeMock->expects($this->once())->method('getAttributeCode')->willReturn('my_attribute');
        $orderAttributeMock->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $labels = '{"1":"Label 1","2":"Label 2"}';
        $labelsDecode = [
            1 => 'Label 1',
            2 => 'Label 2'
        ];

        $this->jsonMock->method('unserialize')
            ->withConsecutive([$labels], [$param])
            ->willReturnOnConsecutiveCalls($labelsDecode, $returnValue);

        $orderAttributeMock->method('getLabels')->willReturn($labels);

        return $orderAttributeMock;
    }

    /**
     * @param array $result
     * @param int $storeId
     * @param array $attributeSubmit
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerPrepareAttributesWithBooleanValue
     */
    public function testPrepareAttributesWithFrontendBoolean($result, $storeId, $attributeSubmit)
    {
        $this->initPrepareAttributes('boolean');

        $this->assertEquals($result, $this->helperData->prepareAttributes($storeId, $attributeSubmit));
    }

    /**
     * @return array
     */
    public function providerPrepareAttributesWithDateValue()
    {
        return [
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute' => 'Apr 30, 2020'
                ],
                1,
                ['my_attribute' => '04/30/2020']
            ],
            [
                [],
                2,
                ['my_attribute_1' => 'Apr 30, 2020']
            ]
        ];
    }

    /**
     * @param array $result
     * @param int $storeId
     * @param array $attributeSubmit
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerPrepareAttributesWithDateValue
     */
    public function testPrepareAttributesWithDateValue($result, $storeId, $attributeSubmit)
    {
        $this->initPrepareAttributes('date');

        $this->assertEquals($result, $this->helperData->prepareAttributes($storeId, $attributeSubmit));
    }

    /**
     * @return array
     */
    public function providerPrepareAttributesWithFileAndImageValue()
    {
        return [
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute' => 'bc/a/bc.png',
                    'my_attribute_name' => 'bc.png',
                    'my_attribute_url' => 'some url',
                ],
                'image',
                1,
                ['my_attribute' => 'bc/a/bc.png']
            ],
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute' => 'bc/a/bc.zip',
                    'my_attribute_name' => 'bc.zip',
                    'my_attribute_url' => 'some url',
                ],
                'file',
                1,
                ['my_attribute' => 'bc/a/bc.zip']
            ]
        ];
    }

    /**
     * @param array $result
     * @param string $frontendInput
     * @param int $storeId
     * @param array $attributeSubmit
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerPrepareAttributesWithFileAndImageValue
     */
    public function testPrepareAttributesWithFileAndImageValue($result, $frontendInput, $storeId, $attributeSubmit)
    {
        $this->initPrepareAttributes($frontendInput);
        $this->urlEncoder->expects($this->once())->method('encode')
            ->with($result['my_attribute'])->willReturn('encode_value');
        $param = 'mporderattributes/viewfile/index/' . $frontendInput . '/encode_value';
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with($param)->willReturn('some url');

        $this->assertEquals($result, $this->helperData->prepareAttributes($storeId, $attributeSubmit));
    }

    /**
     * @return array
     */
    public function providerPrepareAttributesWithSelectValue()
    {
        $options = '{"option":{"value":{"option_0":["A","","",""],"option_1":["B","","",""],"option_2":["C","","",""]}}}';
        $optionDecode = [
            'option' => [
                'value' => [
                    'option_0' => ['A', '', '', ''],
                    'option_1' => ['B', 'Z', '', ''],
                    'option_2' => ['C', '', '', '']
                ]
            ]
        ];

        $optionsVisual = '{"optionvisual":{"value":{"option_0":["Test","Test3","",""],"option_1":["Test2","Test4","",""]}}}';
        $optionVisualDecode = [
            'optionvisual' => [
                'value' => [
                    'option_0' => ['Test', 'Test3', '', ''],
                    'option_1' => ['Test2', 'Test4', '', ''],
                ]
            ]
        ];

        return [
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute' => 'option_0',
                    'my_attribute_option' => 'A',
                ],
                [
                    'options' => $options,
                    'result' => $optionDecode
                ],
                'select',
                1,
                ['my_attribute' => 'option_0']
            ],
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute' => 'option_0',
                    'my_attribute_option' => 'A',
                ],
                [
                    'options' => $options,
                    'result' => $optionDecode
                ],
                'multiselect',
                1,
                ['my_attribute' => 'option_0']
            ],
            [
                [
                    'my_attribute_label' => 'Label 1',
                    'my_attribute' => 'option_1',
                    'my_attribute_option' => 'Test4',
                ],
                [
                    'options' => $optionsVisual,
                    'result' => $optionVisualDecode
                ],
                'select_visual',
                1,
                ['my_attribute' => 'option_1']
            ],
            [
                [
                    'my_attribute_label' => 'Label 2',
                    'my_attribute' => 'option_0',
                    'my_attribute_option' => 'Test',
                ],
                [
                    'options' => $optionsVisual,
                    'result' => $optionVisualDecode
                ],
                'multiselect_visual',
                2,
                ['my_attribute' => 'option_0']
            ]
        ];
    }

    /**
     * @param array $result
     * @param array $optionsData
     * @param string $frontendInput
     * @param int $storeId
     * @param array $attributeSubmit
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerPrepareAttributesWithSelectValue
     */
    public function testPrepareAttributesWithSelectValue(
        $result,
        $optionsData,
        $frontendInput,
        $storeId,
        $attributeSubmit
    ) {
        $attributeMock = $this->initPrepareAttributes($frontendInput, $optionsData['options'], $optionsData['result']);

        $attributeMock->expects($this->once())
            ->method('getOptions')
            ->willReturn($optionsData['options']);

        $this->assertEquals($result, $this->helperData->prepareAttributes($storeId, $attributeSubmit));
    }

    public function testPrepareLabel()
    {
        $labels = '{"1":"Label 1","2":"Label 2"}';
        $labelsDecode = [
            1 => 'Label 1',
            2 => 'Label 2'
        ];

        $this->jsonMock->expects($this->once())->method('unserialize')->with($labels)->willReturn($labelsDecode);
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderAttributeMock->expects($this->once())->method('getLabels')->willReturn($labels);

        $this->assertEquals('Label 1', $this->helperData->prepareLabel($orderAttributeMock, 1));
    }

    public function testPrepareLabelWithFrontendLabel()
    {
        $labels = '{"1":"Label 1","2":"Label 2"}';
        $labelsDecode = [
            1 => 'Label 1',
            2 => 'Label 2'
        ];

        $this->jsonMock->expects($this->once())->method('unserialize')->with($labels)->willReturn($labelsDecode);
        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $orderAttributeMock->expects($this->once())->method('getLabels')->willReturn($labels);
        $orderAttributeMock->expects($this->once())->method('getFrontendLabel')->willReturn('Test');

        $this->assertEquals('Test', $this->helperData->prepareLabel($orderAttributeMock, 0));
    }

    public function testValidateAttributesWithRequireException()
    {
        $quoteMock = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $storeId = 1;
        $groupId = 1;
        $quoteMock->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeId);
        $quoteMock->expects($this->atLeastOnce())->method('getCustomerGroupId')->willReturn($groupId);

        $orderAttributeCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderAttributeCollectionMock);

        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderAttributeCollectionMock->expects($this->once())->method('getItems')->willReturn([$orderAttributeMock]);
        $storeIds = '0,1,2';
        $orderAttributeMock->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeIds);
        $groupIds = '1,2,3';
        $orderAttributeMock->expects($this->atLeastOnce())->method('getCustomerGroup')->willReturn($groupIds);
        $orderAttributeMock->expects($this->atLeastOnce())->method('getPosition')->willReturn(1);
        $orderAttributeMock->expects($this->once())->method('getAttributeCode')->willReturn('my_attribute');
        $orderAttributeMock->expects($this->once())->method('getFrontendInput')->willReturn('boolean');
        $orderAttributeMock->expects($this->once())->method('getIsRequired')->willReturn(true);
        $this->expectException(InputException::class);
        $this->expectExceptionMessage('%1 is required');
        $attributeSubmit = ['my_attribute' => ''];
        $quoteAttribute = $this->getMockBuilder(\Mageplaza\OrderAttributes\Model\Quote::class)
            ->disableOriginalConstructor()->getMock();

        $this->helperData->validateAttributes($quoteMock, $attributeSubmit, $quoteAttribute);
    }

    /**
     * @param string $frontendInput
     *
     * @return array
     */
    public function initValidateAttributes($frontendInput)
    {
        $quoteMock = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $storeId = 1;
        $groupId = 1;
        $quoteMock->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeId);
        $quoteMock->expects($this->atLeastOnce())->method('getCustomerGroupId')->willReturn($groupId);

        $orderAttributeCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderAttributeCollectionMock);

        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderAttributeCollectionMock->expects($this->once())->method('getItems')->willReturn([$orderAttributeMock]);
        $storeIds = '0,1,2';
        $orderAttributeMock->expects($this->atLeastOnce())->method('getStoreId')->willReturn($storeIds);
        $groupIds = '1,2,3';
        $orderAttributeMock->expects($this->atLeastOnce())->method('getCustomerGroup')->willReturn($groupIds);
        $orderAttributeMock->expects($this->atLeastOnce())->method('getPosition')->willReturn(1);
        $orderAttributeMock->expects($this->once())->method('getAttributeCode')->willReturn('my_attribute');
        $orderAttributeMock->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        $orderAttributeMock->method('getIsRequired')->willReturn(true);

        return [$orderAttributeMock, $quoteMock];
    }

    /**
     * @return array
     */
    public function providerValidateAttributesWithDateAndBooleanException()
    {
        return [
            [
                'my_attribute invalid',
                'boolean',
            ],
            [
                'Invalid date',
                'date',
            ],
        ];
    }

    /**
     * @param string $resultMessage
     * @param string $frontendInput
     *
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerValidateAttributesWithDateAndBooleanException
     */
    public function testValidateAttributesWithDateAndBooleanException($resultMessage, $frontendInput)
    {
        $init = $this->initValidateAttributes($frontendInput);
        $orderAttributeMock = $init[0];
        $quoteMock = $init[1];
        $labels = '{"1":"Label 1","2":"Label 2"}';
        $labelsDecode = [
            1 => 'Label 1',
            2 => 'Label 2'
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->with($labels)
            ->willReturn($labelsDecode);

        $orderAttributeMock->method('getLabels')->willReturn($labels);

        $quoteAttribute = $this->getMockBuilder(\Mageplaza\OrderAttributes\Model\Quote::class)
            ->disableOriginalConstructor()->getMock();

        $this->expectException(InputException::class);
        $this->expectExceptionMessage($resultMessage);

        $this->helperData->validateAttributes($quoteMock, ['my_attribute' => 2], $quoteAttribute);
    }

    /**
     * @return array
     */
    public function providerValidateAttributesWithSelectException()
    {
        $options = '{"option":{"value":{"option_0":["A","","",""],"option_1":["B","","",""],"option_2":["C","","",""]}}}';
        $optionDecode = [
            'option' => [
                'value' => [
                    'option_0' => ['A', '', '', ''],
                    'option_1' => ['B', 'Z', '', ''],
                    'option_2' => ['C', '', '', '']
                ]
            ]
        ];

        $optionsVisual = '{"optionvisual":{"value":{"option_0":["Test","Test3","",""],"option_1":["Test2","Test4","",""]}}}';
        $optionVisualDecode = [
            'optionvisual' => [
                'value' => [
                    'option_0' => ['Test', 'Test3', '', ''],
                    'option_1' => ['Test2', 'Test4', '', ''],
                ]
            ]
        ];

        return [
            [
                'select',
                $options,
                $optionDecode
            ],
            [
                'multiselect',
                $options,
                $optionDecode
            ],
            [
                'multiselect',
                $optionsVisual,
                $optionVisualDecode
            ],
            [
                'multiselect',
                $optionsVisual,
                $optionVisualDecode
            ]
        ];
    }

    /**
     * @param $frontendInput
     * @param string $param
     * @param array $returnValue
     *
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerValidateAttributesWithSelectException
     */
    public function testValidateAttributesWithSelectException(
        $frontendInput,
        $param = '',
        $returnValue = []
    ) {
        $init = $this->initValidateAttributes($frontendInput);
        $orderAttributeMock = $init[0];
        $quoteMock = $init[1];

        $labels = '{"1":"Label 1","2":"Label 2"}';
        $labelsDecode = [
            1 => 'Label 1',
            2 => 'Label 2'
        ];

        $this->jsonMock->method('unserialize')
            ->withConsecutive([$labels], [$param])
            ->willReturnOnConsecutiveCalls($labelsDecode, $returnValue);

        $orderAttributeMock->method('getLabels')->willReturn($labels);
        $orderAttributeMock->expects($this->once())->method('getOptions')->willReturn($param);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Invalid options option_5. Details: option_5');

        $quoteAttribute = $this->getMockBuilder(\Mageplaza\OrderAttributes\Model\Quote::class)
            ->disableOriginalConstructor()->getMock();

        $this->helperData->validateAttributes($quoteMock, ['my_attribute' => 'option_5'], $quoteAttribute);
    }

    /**
     * @return array
     */
    public function providerTestValidateAttributesWithFileAndImageException()
    {
        return [
            ['image'],
            ['file']
        ];
    }

    /**
     * @param string $frontendInput
     *
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerTestValidateAttributesWithFileAndImageException
     */
    public function testValidateAttributesWithFileAndImageException(
        $frontendInput
    ) {
        $init = $this->initValidateAttributes($frontendInput);
        $orderAttributeMock = $init[0];
        $quoteMock = $init[1];

        $labels = '{"1":"Label 1","2":"Label 2"}';
        $labelsDecode = [
            1 => 'Label 1',
            2 => 'Label 2'
        ];

        /**
         * Only mock data for file, image is the same
         */
        $fileUpload = '{"file":"abc\/test.zip","name":"test.zip","size":1000,"url":"example_url"}';
        $fileUploadDecode = [
            'file' => 'abc/test.zip',
            'name' => 'test.zip',
            'size' => 1000,
            'url' => 'example_url'
        ];
        $fileDB = '{"file":"abc\/test.zip","name":"test.zip","size":10000,"url":"example_url"}';
        $fileDBDecode = [
            'file' => 'abc/test.zip',
            'name' => 'test.zip',
            'size' => 10000,
            'url' => 'example_url'
        ];

        $this->jsonMock->method('unserialize')
            ->withConsecutive(
                [$labels],
                [$fileUpload],
                [$fileDB]
            )
            ->willReturnOnConsecutiveCalls($labelsDecode, $fileUploadDecode, $fileDBDecode);

        $orderAttributeMock->method('getLabels')->willReturn($labels);

        $this->expectException(InputException::class);
        $this->expectExceptionMessage('Something went wrong while uploading file (attribute my_attribute)');

        $quoteAttribute = $this->getMockBuilder(\Mageplaza\OrderAttributes\Model\Quote::class)
            ->disableOriginalConstructor()->getMock();
        $quoteAttribute->expects($this->once())->method('getData')->with('my_attribute')->willReturn($fileDB);

        $this->helperData->validateAttributes($quoteMock, ['my_attribute' => 'option_5'], $quoteAttribute);
    }

    public function testIsOscPage()
    {
        $this->moduleManager->expects($this->once())
            ->method('isOutputEnabled')
            ->with('Mageplaza_Osc')
            ->willReturn(true);
        $this->request->expects($this->once())->method('getRouteName')->willReturn('onestepcheckout');

        $this->assertTrue($this->helperData->isOscPage());
    }

    public function testGetShippingMethods()
    {
        $freeShipping = $this->getMockBuilder(Freeshipping::class)->disableOriginalConstructor()->getMock();
        $temando = $this->getMockBuilder(Carrier::class)->disableOriginalConstructor()->getMock();

        $freeShipping->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn(['freeshipping' => 'free']);
        $freeShipping->expects($this->once())
            ->method('getConfigData')
            ->with('title')
            ->willReturn('Free Shipping');

        $this->carrierConfigMock->expects($this->once())
            ->method('getAllCarriers')
            ->willReturn(['free' => $freeShipping, 'temando' => $temando]);

        $this->assertEquals(
            [
                [
                    'value' => [
                        [
                            'value' => 'free_freeshipping',
                            'label' => 'free'
                        ]
                    ],
                    'label' => 'Free Shipping'
                ]
            ],
            $this->helperData->getShippingMethods()
        );
    }

    /**
     * @return array
     */
    public function providerApplyFilter()
    {
        return [
            [
                'striptags',
                'input',
            ],
            [
                'escapehtml',
                'outputFilter'
            ]
        ];
    }

    /**
     * @param string $inputFilter
     * @param string $type
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @dataProvider providerApplyFilter
     *
     */
    public function testApplyFilter($inputFilter, $type)
    {
        /**
         * @var AbstractModel| Order $object
         */
        $objectManagerHelper = new ObjectManager($this);
        $value = 'test';
        $object = $objectManagerHelper->getObject(
            Order::class,
            [
                'data' => ['my_attribute' => $value]
            ]
        );
        $orderAttributeCollectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()->getMock();
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($orderAttributeCollectionMock);

        $orderAttributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderAttributeMock->expects($this->exactly(2))
            ->method('getAttributeCode')
            ->willReturn('my_attribute');
        $orderAttributeMock->expects($this->once())
            ->method('getInputFilter')
            ->willReturn($inputFilter);
        $orderAttributeCollectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('input_filter', ['neq' => 'NULL']);
        $orderAttributeCollectionMock->expects($this->once())->method('getItems')->willReturn([$orderAttributeMock]);

        $this->helperData->applyFilter($object, $type);
    }

    public function testGetFilterClass()
    {
        $striptagsObject = new Striptags();
        $escapehtmlObject = new Escapehtml();
        $helperDataObject = new ReflectionClass(Data::class);
        $method = $helperDataObject->getMethod('getFilterClass');
        $method->setAccessible(true);
        $this->assertEquals($striptagsObject, $method->invokeArgs($this->helperData, ['striptags']));
        $this->assertEquals($escapehtmlObject, $method->invokeArgs($this->helperData, ['escapehtml']));
    }

    public function testGetBaseTmpMediaPath()
    {
        $this->assertEquals('mageplaza/order_attributes/tmp', $this->helperData->getBaseTmpMediaPath());
    }

    public function testGetBaseTmpMediaUrl()
    {
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn('https://mydomain/media/');
        $this->assertEquals(
            'https://mydomain/media/mageplaza/order_attributes/tmp',
            $this->helperData->getBaseTmpMediaUrl()
        );
    }

    /**
     * @throws NoSuchEntityException
     */
    public function testGetTmpMediaUrl()
    {
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $storeMock->expects($this->once())->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn('https://mydomain/media/');

        $result = 'https://mydomain/media/mageplaza/order_attributes/tmp/d/abc.zip';
        $this->assertEquals($result, $this->helperData->getTmpMediaUrl('/d\abc.zip'));
    }

    public function testPrepareFile()
    {
        $additional = new ReflectionClass(Data::class);
        $method = $additional->getMethod('_prepareFile');
        $method->setAccessible(true);

        $this->assertEquals('d/abc.zip', $method->invokeArgs($this->helperData, ['/d\abc.zip']));
    }

    /**
     * @return array
     */
    public function providerTestMoveTemporaryFile()
    {
        return [
            [true, true],
            [true, false],
            [true, false]
        ];
    }

    /**
     * @param boolean $isCreate
     * @param boolean $isWritable
     *
     * @throws FileSystemException
     * @throws LocalizedException
     * @dataProvider providerTestMoveTemporaryFile
     */
    public function testMoveTemporaryFile($isCreate, $isWritable)
    {
        $fileSystemMock = $this->getMockBuilder(Filesystem::class)->disableOriginalConstructor()->getMock();

        $this->objectManagerMock->expects($this->once())->method('get')
            ->with(Filesystem::class)
            ->willReturn($fileSystemMock);
        $directoryReadMock = $this->getMockBuilder(ReadInterface::class)->getMock();
        $directoryWriteMock = $this->getMockBuilder(WriteInterface::class)->getMock();
        $fileSystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(DirectoryList::MEDIA)
            ->willReturn($directoryReadMock);
        $pathMock = 'mageplaza/order_attributes/tmp/d/b/db2a43b40f55a3017ea6ddfdd6b57eff_1.png';
        $absolutePath = 'magento_root/pub/media/mageplaza/order_attributes/tmp/d/b/db2a43b40f55a3017ea6ddfdd6b57eff_1.png';

        $fileSystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($directoryWriteMock);
        $directoryReadMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with($pathMock)
            ->willReturn($absolutePath);
        $newPathMock = 'mageplaza/order_attributes/d/b';
        $directoryWriteMock->expects($this->once())
            ->method('create')->with($newPathMock)->willReturn($isCreate);
        if (!$isCreate) {
            $this->expectException(LocalizedException::class);
            $this->expectExceptionMessage('Unable to create directory mageplaza/order_attributes/d/b');
        }

        if (!$isWritable) {
            $this->expectException(LocalizedException::class);
            $this->expectExceptionMessage('Destination folder is not writable or does not exists.');
        } else {
            $directoryWriteMock->expects($this->once())
                ->method('isWritable')->with($newPathMock)->willReturn($isWritable);
        }

        $result = '/d/b/db2a43b40f55a3017ea6ddfdd6b57eff_1.png';
        $fileMock = ['file' => '/d/b/db2a43b40f55a3017ea6ddfdd6b57eff_1.png'];

        $this->assertEquals($result, $this->helperData->moveTemporaryFile($fileMock));
    }

    /**
     * @return array
     */
    public function providerPrepareBoolValue()
    {
        return [
            ['Yes', true],
            ['No', false]
        ];
    }

    /**
     * @param string $result
     * @param boolean $value
     *
     * @dataProvider providerPrepareBoolValue
     */
    public function testPrepareBoolValue($result, $value)
    {
        $this->assertEquals(new Phrase($result), $this->helperData->prepareBoolValue($value));
    }

    /**
     * @return array
     */
    public function providerPrepareDateValue()
    {
        return [
            ['Apr 30, 2020', '04/30/2020'],
            [null, null]
        ];
    }

    /**
     * @return array
     */
    public function providerPrepareOptionValue()
    {
        $options = '{"option":{"value":{"option_0":["A","","",""],"option_1":["B","","",""],"option_2":["C","","",""]}}}';
        $optionDecode = [
            'option' => [
                'value' => [
                    'option_0' => ['A', '', '', ''],
                    'option_1' => ['B', 'Z', '', ''],
                    'option_2' => ['C', '', '', '']
                ]
            ]
        ];

        $optionsVisual = '{"optionvisual":{"value":{"option_0":["Test","Test3","",""],"option_1":["Test2","Test4","",""]}}}';
        $optionVisualDecode = [
            'optionvisual' => [
                'value' => [
                    'option_0' => ['Test', 'Test3', '', ''],
                    'option_1' => ['Test2', 'Test4', '', ''],
                ]
            ]
        ];

        return [
            ['A', $options, $optionDecode, 'option_0', 1],
            ['Z', $options, $optionDecode, 'option_1', 1],
            ['Test4', $optionsVisual, $optionVisualDecode, 'option_1', 1],
            ['Test', $optionsVisual, $optionVisualDecode, 'option_0', 2],

        ];
    }

    /**
     * @param string $result
     * @param array $options
     * @param array $optionsDecode
     * @param string $values
     * @param int $storeId
     *
     * @dataProvider providerPrepareOptionValue
     */
    public function testPrepareOptionValue($result, $options, $optionsDecode, $values, $storeId)
    {
        $this->jsonMock->expects($this->once())->method('unserialize')->with($options)->willReturn($optionsDecode);

        $this->assertEquals($result, $this->helperData->prepareOptionValue($options, $values, $storeId));
    }

    public function testPrepareOptionValueWithInvalidOption()
    {
        $options = '{"option":{"value":{"option_0":["A","","",""],"option_1":["B","","",""],"option_2":["C","","",""]}}}';
        $optionDecode = [
            'option' => [
                'value' => [
                    'option_0' => ['A', '', '', ''],
                    'option_1' => ['B', 'Z', '', ''],
                    'option_2' => ['C', '', '', '']
                ]
            ]
        ];
        $this->jsonMock->expects($this->once())->method('unserialize')->with($options)->willReturn($optionDecode);
        $this->helperData->prepareOptionValue($options, 'option_5,option_1', 1);

        $this->assertEquals(['option_5'], $this->helperData->getOptionsInvalid());
    }

    /**
     * @param string $result
     * @param string $value
     *
     * @dataProvider providerPrepareDateValue
     */
    public function testPrepareDateValue($result, $value)
    {
        $this->assertEquals($result, $this->helperData->prepareDateValue($value));
    }

    public function testPrepareFileName()
    {
        $value = 'bc/a/bc.test';
        $result = 'bc.test';
        $this->assertEquals($result, $this->helperData->prepareFileName($value));
    }

    public function testPrepareFileValue()
    {
        $result = 'some url';
        $this->urlEncoder->expects($this->once())->method('encode')
            ->with('my_value')->willReturn('my_value');
        $param = 'mporderattributes/viewfile/index/text/my_value';
        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with($param)->willReturn($result);

        $this->helperData->prepareFileValue('text', 'my_value');
    }

    /**
     * Only test for case version magento >= 2.3.0 to check compatible
     */
    public function testGetTinymceConfig()
    {
        $productMetaMock = $this->getMockBuilder(ProductMetadataInterface::class)->getMock();
        $this->objectManagerMock->expects($this->once())->method('get')
            ->with(ProductMetadataInterface::class)
            ->willReturn($productMetaMock);
        $productMetaMock->expects($this->once())->method('getVersion')->willReturn('2.3.0');
        $this->repositoryMock->expects($this->once())
            ->method('getUrl')
            ->with('mage/adminhtml/wysiwyg/tiny_mce/themes/ui.css')->willReturn('some url');
        $config = [
            'tinymce4' => [
                'toolbar' => 'formatselect | bold italic underline | alignleft aligncenter alignright | '
                    . 'bullist numlist | link table charmap',
                'plugins' => implode(
                    ' ',
                    [
                        'advlist',
                        'autolink',
                        'lists',
                        'link',
                        'charmap',
                        'media',
                        'noneditable',
                        'table',
                        'contextmenu',
                        'paste',
                        'code',
                        'help',
                        'table'
                    ]
                ),
                'content_css' => 'some url'
            ]
        ];
        $configJson = '{"tinymce4":{"toolbar":"formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table charmap","plugins":"advlist autolink lists link charmap media noneditable table contextmenu paste code help table","content_css":"some url"}}';
        $this->jsonMock->expects($this->once())->method('serialize')->with($config)->willReturn($configJson);
        $this->assertEquals($configJson, $this->helperData->getTinymceConfig());
    }
}
