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

namespace Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\FormData;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\OrderAttributes\Controller\Adminhtml\Attribute\Save;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;
use Mageplaza\OrderAttributes\Model\AttributeFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Zend_Validate_Exception;

/**
 * Class SaveTest
 * @package Mageplaza\OrderAttributes\Test\Unit\Controller\Adminhtml\Attribute
 */
class SaveTest extends TestCase
{
    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var PageFactory|MockObject
     */
    private $pageFactoryMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var AttributeFactory|MockObject
     */
    private $orderAttributeMock;

    /**
     * @var Data|MockObject
     */
    private $helperDataMock;

    /**
     * @var FormData|MockObject
     */
    private $formDataMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var MockObject
     */
    private $viewMock;

    /**
     * @var MockObject
     */
    private $responseMock;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var EventManagerInterface|MockObject
     */
    private $eventManagerMock;

    /**
     * @var Session|MockObject
     */
    private $backendSessionMock;

    /**
     * @var Save
     */
    private $saveController;

    protected function setUp()
    {
        /**
         * @var Context|MockObject $contextMock
         */
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->setMethods(['getParams', 'getParam', 'getPostValue'])->getMockForAbstractClass();
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);

        $this->viewMock = $this->createMock(ViewInterface::class);
        $this->responseMock = $this->getMockBuilder(ResponseInterface::class)
            ->setMethods(['setBody'])
            ->getMockForAbstractClass();
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventManagerMock = $this->getMockForAbstractClass(EventManagerInterface::class);

        $this->backendSessionMock = $this->getMockBuilder(Session::class)
            ->setMethods(['setAttributeData'])
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock->method('getSession')->willReturn($this->backendSessionMock);
        $contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $contextMock->method('getResponse')->willReturn($this->responseMock);
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getView')->willReturn($this->viewMock);
        $contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $contextMock->method('getEventManager')->willReturn($this->eventManagerMock);

        $this->pageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock = $this->getMockBuilder(AttributeFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->helperDataMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->formDataMock = $this->getMockBuilder(FormData::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->saveController = new Save(
            $contextMock,
            $this->pageFactoryMock,
            $this->registryMock,
            $this->orderAttributeMock,
            $this->helperDataMock,
            $this->formDataMock
        );
    }

    /**
     * @throws Zend_Validate_Exception
     */
    public function testExecuteWithEmptyPostValue()
    {
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn([]);
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirectMock);

        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('mporderattributes/*/', [])
            ->willReturnSelf();

        $this->saveController->execute();
    }

    public function testExecuteWithException()
    {
        $attributeId = 1;
        $postValue = ['attribute_id' => 1];
        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($postValue);

        $this->requestMock->expects($this->once())->method('getParam')->with('attribute_id')->willReturn($attributeId);

        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = 'test';
        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);
        $attributeMock->expects($this->once())->method('addData')->willReturnSelf();
        $attributeMock->expects($this->once())->method('save')->willThrowException(new Exception($exception));
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($exception)
            ->willReturnSelf();
        $this->backendSessionMock->expects($this->once())
            ->method('setAttributeData')
            ->with([
                'attribute_id' => 1,
                'frontend_input' => null,
                'shipping_depend' => null
            ]);

        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirectMock);

        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('mporderattributes/*/edit', ['id' => $attributeId, '_current' => true])
            ->willReturnSelf();

        $this->saveController->execute();
    }

    /**
     * @return array
     */
    public function getPostValue()
    {
        return [
            'serialized_options' => '[]',
            'frontend_input' => '',
            'attribute_id' => '0',
            'frontend_label' => 'My frontend label',
            'max_file_size' => '0',
            'allow_extensions' => '',
            'default_value_text' => '0',
            'default_value_textarea' => '0',
            'default_value_date' => '',
            'default_value_yesno' => '0',
            'default_value_content' => '<p>0</p>',
            'is_required' => '0',
            'frontend_class' => '',
            'input_filter' => '',
            'is_used_in_grid' => '0',
            'position' => '6',
            'use_tooltip' => '1',
            'store_id' => [0 => '0',],
            'customer_group' => [
                0 => '0',
                1 => '1',
                2 => '2',
                3 => '3',
            ],
            'show_in_frontend_order' => '1',
            'sort_order' => '0',
            'labels' => [
                1 => 'My label',
                2 => '',
            ],
            'tooltips' => [
                1 => 'Test1',
                2 => 'Test2',
            ],
            'dropdown_attribute_validation' => '',
            'dropdown_attribute_validation_unique' => '',
            'visual_swatch_validation' => '',
            'visual_swatch_validation_unique' => '',
            'field_depend' => '',
        ];
    }

    /**
     * @return array|mixed
     */
    public function getResult()
    {
        return [
            'serialized_options' => '[]',
            'frontend_label' => 'My frontend label',
            'max_file_size' => '0',
            'allow_extensions' => '',
            'default_value_text' => '0',
            'default_value_textarea' => '0',
            'default_value_date' => '',
            'default_value_yesno' => '0',
            'default_value_content' => '<p>0</p>',
            'is_required' => '0',
            'frontend_class' => '',
            'input_filter' => '',
            'is_used_in_grid' => '0',
            'position' => '6',
            'use_tooltip' => '1',
            'store_id' => '0',
            'customer_group' => '0,1,2,3',
            'show_in_frontend_order' => '1',
            'sort_order' => '0',
            'labels' => '{"1":"My label","2":""}',
            'tooltips' => '{"1":"Test1","2":"Test2"}',
            'dropdown_attribute_validation' => '',
            'dropdown_attribute_validation_unique' => '',
            'visual_swatch_validation' => '',
            'visual_swatch_validation_unique' => '',
            'field_depend' => '',
            'frontend_input' => '',
            'shipping_depend' => null,
            'default_value' => '0'
        ];
    }

    /**
     * @return array
     */
    public function providerTestExecute()
    {
        $postValue = $this->getPostValue();
        $data = $this->getResult();

        $newAttributePostValue = $postValue;
        $newAttributePostValue['frontend_input'] = 'date';
        $resultNewAttribute = $data;
        $resultNewAttribute['frontend_input'] = 'date';
        $resultNewAttribute['attribute_id'] = '0';
        $resultNewAttribute['backend_type'] = 'datetime';
        $resultNewAttribute['default_value'] = '2020-05-22';

        $editAttributePostValue = $newAttributePostValue;
        $editAttributePostValue['attribute_id'] = 1;
        $resultEditAttribute = $data;
        $resultEditAttribute['frontend_input'] = 'date';
        $resultEditAttribute['attribute_id'] = '1';
        $resultEditAttribute['default_value'] = '2020-05-22';

        $editAttributePostValueWithShippingDepend = $editAttributePostValue;
        $editAttributePostValueWithShippingDepend['shipping_depend'] = [
            0 => 'flatrate_flatrate',
            1 => 'tablerate_bestway'
        ];
        $resultEditAttributeWithShippingDepend = $resultEditAttribute;
        $resultEditAttributeWithShippingDepend['shipping_depend'] = 'flatrate_flatrate,tablerate_bestway';

        $editAttributeOptionsPostValue = $postValue;
        $editAttributeOptionsPostValue['attribute_id'] = '1';
        $editAttributeOptionsPostValue['frontend_input'] = 'select';
        $editAttributeOptionsPostValue['serialized_options'] = '["option%5Border%5D%5Boption_0%5D=1&option%5Bvalue%5D%5Boption_0%5D%5B0%5D=A&option%5Bvalue%5D%5Boption_0%5D%5B1%5D=&option%5Bvalue%5D%5Boption_0%5D%5B2%5D=&option%5Bdelete%5D%5Boption_0%5D=","option%5Border%5D%5Boption_1%5D=2&default%5B%5D=option_1&option%5Bvalue%5D%5Boption_1%5D%5B0%5D=B&option%5Bvalue%5D%5Boption_1%5D%5B1%5D=&option%5Bvalue%5D%5Boption_1%5D%5B2%5D=&option%5Bdelete%5D%5Boption_1%5D=","option%5Border%5D%5Boption_2%5D=3&option%5Bvalue%5D%5Boption_2%5D%5B0%5D=C&option%5Bvalue%5D%5Boption_2%5D%5B1%5D=&option%5Bvalue%5D%5Boption_2%5D%5B2%5D=&option%5Bdelete%5D%5Boption_2%5D="]';

        $resultEditAttributeOptions = $data;
        $resultEditAttributeOptions['attribute_id'] = '1';
        $resultEditAttributeOptions['frontend_input'] = 'select';
        $resultEditAttributeOptions['default_value'] = false;
        $resultEditAttributeOptions['serialized_options'] = $editAttributeOptionsPostValue['serialized_options'];
        $resultEditAttributeOptions['options'] = '{"option":{"order":{"option_0":"1","option_1":"2","option_2":"3"},"value":{"option_0":["A","",""],"option_1":["B","",""],"option_2":["C","",""]},"delete":{"option_0":"","option_1":"","option_2":""}},"default":["option_1"]}';

        $optionsVisual = [
            'order' => [
                'option_0' => '1',
                'option_1' => '2',
                'option_2' => '3',
                'option_3' => '4',
            ],
            'value' => [
                'option_0' => [
                    0 => 'Admin1',
                    1 => '',
                    2 => '',
                ],
                'option_1' => [
                    0 => 'Admin 2',
                    1 => '',
                    2 => '',
                ],
                'option_2' => [
                    0 => 'Admin3',
                    1 => '',
                    2 => '',
                ],
                'option_3' => [
                    0 => 'Admin4',
                    1 => '',
                    2 => '',
                ],
            ],
            'delete' => [
                'option_0' => '',
                'option_1' => '',
                'option_2' => '',
                'option_3' => '1',
            ],
        ];
        $optionsVisualResult = [
            'order' => [
                'option_0' => '1',
                'option_1' => '2',
                'option_2' => '3',
                'option_3' => '4',
            ],
            'value' => [
                'option_0' => [
                    0 => 'Admin1',
                    1 => '',
                    2 => '',
                ],
                'option_1' => [
                    0 => 'Admin 2',
                    1 => '',
                    2 => '',
                ],
                'option_2' => [
                    0 => 'Admin3',
                    1 => '',
                    2 => '',
                ]
            ],
            'delete' => [
                'option_0' => '',
                'option_1' => '',
                'option_2' => '',
                'option_3' => '1',
            ],
        ];
        $swatchVisual = [
            'value' => [
                'option_0' => '',
                'option_1' => '/8/1/81420380_2495558257209043_2125716526110605312_o_1.jpg',
                'option_2' => '#7a0d7a',
                'option_3' => ''
            ]
        ];
        $defaultVisual = [0 => 'option_1'];
        $editAttributeVisualOptionsPostValue = $postValue;
        $editAttributeVisualOptionsPostValue['optionvisual'] = $optionsVisual;
        $editAttributeVisualOptionsPostValue['swatchvisual'] = $swatchVisual;
        $editAttributeVisualOptionsPostValue['defaultvisual'] = $defaultVisual;
        $editAttributeVisualOptionsPostValue['attribute_id'] = '1';
        $editAttributeVisualOptionsPostValue['frontend_input'] = 'select_visual';
        $editAttributeVisualOptionsPostValue['default_value'] = false;

        $resultEditAttributeVisualOptions = $data;
        $resultEditAttributeVisualOptions['optionvisual'] = $optionsVisualResult;
        $resultEditAttributeVisualOptions['swatchvisual'] = $swatchVisual;
        $resultEditAttributeVisualOptions['defaultvisual'] = $defaultVisual;
        $resultEditAttributeVisualOptions['attribute_id'] = '1';
        $resultEditAttributeVisualOptions['frontend_input'] = 'select_visual';
        $resultEditAttributeVisualOptions['default_value'] = false;
        $resultEditAttributeVisualOptions['additional_data'] = '{"option_0":{"swatch_value":"","swatch_type":3},"option_1":{"swatch_value":"\/8\/1\/81420380_2495558257209043_2125716526110605312_o_1.jpg","swatch_type":2},"option_2":{"swatch_value":"#7a0d7a","swatch_type":1},"option_3":{"swatch_value":"","swatch_type":3}}';
        $resultEditAttributeVisualOptions['options'] = '{"optionvisual":{"order":{"option_0":"1","option_1":"2","option_2":"3","option_3":"4"},"value":{"option_0":["Admin1","",""],"option_1":["Admin 2","",""],"option_2":["Admin3","",""]},"delete":{"option_0":"","option_1":"","option_2":"","option_3":"1"}},"defaultvisual":["option_1"]}';

        $swatch = [
            'option_0' => [
                'swatch_value' => '',
                'swatch_type' => 3,
            ],
            'option_1' => [
                'swatch_value' => '/8/1/81420380_2495558257209043_2125716526110605312_o_1.jpg',
                'swatch_type' => 2,
            ],
            'option_2' => [
                'swatch_value' => '#7a0d7a',
                'swatch_type' => 1,
            ],
            'option_3' => [
                'swatch_value' => '',
                'swatch_type' => 3,
            ]
        ];

        $optionsVisual = [
            'optionvisual' => [
                'order' => [
                    'option_0' => '1',
                    'option_1' => '2',
                    'option_2' => '3',
                    'option_3' => '4',
                ],
                'value' => [
                    'option_0' => [
                        0 => 'Admin1',
                        1 => '',
                        2 => '',
                    ],
                    'option_1' => [
                        0 => 'Admin 2',
                        1 => '',
                        2 => '',
                    ],
                    'option_2' => [
                        0 => 'Admin3',
                        1 => '',
                        2 => '',
                    ],
                ],
                'delete' => [
                    'option_0' => '',
                    'option_1' => '',
                    'option_2' => '',
                    'option_3' => '1',
                ],
            ],
            'defaultvisual' => [0 => 'option_1'],
        ];

        return [
            [
                $resultNewAttribute,
                $newAttributePostValue,
                [
                    'defaultValueField' => 'default_value_date',
                    'defaultValueFieldResult' => '05/22/2020',
                    'back' => true,
                    'backendTypeInput' => 'datetime'
                ]
            ],
            [
                $resultEditAttribute,
                $editAttributePostValue,
                [
                    'defaultValueField' => 'default_value_date',
                    'defaultValueFieldResult' => '05/22/2020',
                    'back' => true,
                    'backendTypeInput' => 'datetime'
                ]
            ],
            [
                $resultEditAttributeWithShippingDepend,
                $editAttributePostValueWithShippingDepend,
                [
                    'defaultValueField' => 'default_value_date',
                    'defaultValueFieldResult' => '05/22/2020',
                    'back' => false,
                    'backendTypeInput' => 'datetime'
                ]
            ],
            [
                $resultEditAttributeOptions,
                $editAttributeOptionsPostValue,
                [
                    'defaultValueField' => 'default_value_select',
                    'defaultValueFieldResult' => false,
                    'back' => true,
                    'backendTypeInput' => 'varchar',
                    'unserialize' => [
                        'option' => [
                            'order' => [
                                'option_0' => '1',
                                'option_1' => '2',
                                'option_2' => '3',
                            ],
                            'value' => [
                                'option_0' =>
                                    [
                                        0 => 'A',
                                        1 => '',
                                        2 => '',
                                    ],
                                'option_1' =>
                                    [
                                        0 => 'B',
                                        1 => '',
                                        2 => '',
                                    ],
                                'option_2' =>
                                    [
                                        0 => 'C',
                                        1 => '',
                                        2 => '',
                                    ],
                            ],
                            'delete' => [
                                'option_0' => '',
                                'option_1' => '',
                                'option_2' => '',
                            ]
                        ],
                        'default' => [
                            'option_1'
                        ]
                    ]
                ]
            ],
            [
                $resultEditAttributeVisualOptions,
                $editAttributeVisualOptionsPostValue,
                [
                    'defaultValueField' => 'default_value_select_visual',
                    'defaultValueFieldResult' => false,
                    'back' => true,
                    'backendTypeInput' => 'varchar',
                    'swatches' => $swatch,
                    'optionVisual' => $optionsVisual
                ]
            ]
        ];
    }

    /**
     * @param array $result
     * @param array $postValue
     * @param array $customData
     *
     * @dataProvider providerTestExecute
     *
     * @throws Zend_Validate_Exception
     */
    public function testExecute($result, $postValue, $customData)
    {
        $attributeId = $postValue['attribute_id'];
        $frontendInput = $postValue['frontend_input'];
        $tooltips = $postValue['tooltips'];
        $tooltipsJson = $result['tooltips'];
        $labels = $postValue['labels'];
        $labelsJson = $result['labels'];
        $swatchVisual = isset($customData['swatches']) ? $customData['swatches'] : [];
        $swatchVisualJson = isset($result['additional_data']) ? $result['additional_data'] : [];
        $serializedOptions = $postValue['serialized_options'];
        $option = isset($customData['unserialize']) ? $customData['unserialize'] : [];
        $defaultValueField = $customData['defaultValueField'];
        $defaultValueFieldResult = $customData['defaultValueFieldResult'];
        $isBackEdit = $customData['back'];
        $backendTypeInput = $customData['backendTypeInput'];

        $this->requestMock->expects($this->once())->method('getPostValue')->willReturn($postValue);
        $resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()->getMock();

        $this->helperDataMock->expects($this->once())
            ->method('versionCompare')
            ->with('2.2.6')
            ->willReturn(true);

        $this->formDataMock->expects($this->once())
            ->method('unserialize')
            ->with($serializedOptions)
            ->willReturn($option);

        $this->requestMock
            ->method('getParam')
            ->withConsecutive(['attribute_id'], [$defaultValueField], ['back', false])
            ->willReturnOnConsecutiveCalls($attributeId, $defaultValueFieldResult, $isBackEdit);

        $attributeMock = $this->getMockBuilder(Attribute::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderAttributeMock->expects($this->once())->method('create')->willReturn($attributeMock);

        if ($attributeId) {
            $attributeMock->expects($this->once())->method('load')->with($attributeId)->willReturnSelf();
            $attributeMock->expects($this->once())->method('getFrontendInput')->willReturn($frontendInput);
        } else {
            $this->helperDataMock->expects($this->once())
                ->method('getBackendTypeByInputType')
                ->with($frontendInput)
                ->willReturn($backendTypeInput);
        }

        if ($swatchVisual) {
            $this->helperDataMock->method('jsonEncodeData')
                ->withConsecutive([$labels], [$tooltips], [$swatchVisual], [$customData['optionVisual']])
                ->willReturnOnConsecutiveCalls($labelsJson, $tooltipsJson, $swatchVisualJson, $result['options']);
        } elseif ($option) {
            $this->helperDataMock->method('jsonEncodeData')
                ->withConsecutive([$labels], [$tooltips], [$option])
                ->willReturnOnConsecutiveCalls($labelsJson, $tooltipsJson, $result['options']);
        } else {
            $this->helperDataMock->method('jsonEncodeData')
                ->withConsecutive([$labels], [$tooltips], [$swatchVisual])
                ->willReturnOnConsecutiveCalls($labelsJson, $tooltipsJson, $swatchVisualJson);
        }

        $this->helperDataMock->expects($this->once())
            ->method('getDefaultValueByInput')
            ->with($frontendInput)
            ->willReturn($defaultValueField);
        $attributeMock->expects($this->once())->method('addData')->with($result)->willReturnSelf();
        $attributeMock->expects($this->once())->method('save')->willReturnSelf();
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with('mporderattributes_attribute_save', ['attribute' => $attributeMock]);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(new Phrase('The attribute has been saved.'))->willReturnSelf();
        $this->backendSessionMock->expects($this->once())
            ->method('setAttributeData')
            ->with(false);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($resultRedirectMock);

        $attributeMock->method('getId')->willReturn($attributeId);

        $path = 'mporderattributes/*/';
        $params = [];
        if ($isBackEdit) {
            $path .= 'edit';
            $params = [
                'id' => $attributeId,
                '_current' => true
            ];
        }
        $resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with($path, $params)
            ->willReturnSelf();

        $this->saveController->execute();
    }
}
