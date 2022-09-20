<?php

namespace Smartosc\MidSplitPayment\Model;

use RealexPayments\HPP\Model\Config\Source\DMFields;
use RealexPayments\HPP\Model\Config\Source\FraudMode;
use RealexPayments\HPP\Model\Config\Source\SettleMode;
use Magento\Framework\DataObject;
use Magento\Payment\Model\Method\ConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PaymentMethod extends \RealexPayments\HPP\Model\PaymentMethod
{
    const METHOD_CODE = 'realexpayments_hpp';
    const NOT_AVAILABLE = 'N/A';
    const MERCHANT_UKVI = 'eshop_payment_method/eshop_merchant_id/merchant_account_ukvi';
    const MERCHANT_TLSCONTACT = 'eshop_payment_method/eshop_merchant_id/merchant_account_tlscontact';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var GUEST_ID , used when order is placed by guests
     */
    const GUEST_ID = 'guest';
    /**
     * @var CUSTOMER_ID , used when order is placed by customers
     */
    const CUSTOMER_ID = 'customer';

    /**
     * @var string
     */
    protected $_infoBlockType = 'RealexPayments\HPP\Block\Info\Info';

    /**
     * Payment Method feature.
     *
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canCaptureOnce = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_isInitializeNeeded = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = false;

    /**
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @var bool
     */
    protected $_canReviewPayment = true;

    /**
     * @var \RealexPayments\HPP\Helper\Data
     */
    private $_helper;

    /**
     * @var \RealexPayments\HPP\API\RemoteXMLInterface
     */
    private $_remoteXml;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $_urlBuilder;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    private $_resolver;

    /**
     * @var \RealexPayments\HPP\Logger\Logger
     */
    private $_realexLogger;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $_productMetadata;

    /**
     * @var \Magento\Framework\Module\ResourceInterface
     */
    protected $_resourceInterface;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_session;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $_customerRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * PaymentMethod constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \RealexPayments\HPP\Helper\Data $helper
     * @param \RealexPayments\HPP\API\RemoteXMLInterface $remoteXml
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $resolver
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \RealexPayments\HPP\Logger\Logger $realexLogger
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\ResourceInterface $resourceInterface
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder,
        \RealexPayments\HPP\Helper\Data $helper,
        \RealexPayments\HPP\API\RemoteXMLInterface $remoteXml,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $resolver,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \RealexPayments\HPP\Logger\Logger $realexLogger,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\ResourceInterface $resourceInterface,
        \Magento\Checkout\Model\Session $session,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $request,
            $urlBuilder,
            $helper,
            $remoteXml,
            $storeManager,
            $resolver,
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $realexLogger,
            $productMetadata,
            $resourceInterface,
            $session,
            $customerRepository,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_urlBuilder = $urlBuilder;
        $this->_helper = $helper;
        $this->_remoteXml = $remoteXml;
        $this->_storeManager = $storeManager;
        $this->_resolver = $resolver;
        $this->_request = $request;
        $this->_realexLogger = $realexLogger;
        $this->_productMetadata = $productMetadata;
        $this->_resourceInterface = $resourceInterface;
        $this->_session = $session;
        $this->_customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $paymentAction
     * @param object $stateObject
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initialize($paymentAction, $stateObject)
    {
        /*
         * do not send order confirmation mail after order creation wait for
         * result confirmation from realex
         */
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);

        $stateObject->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $stateObject->setStatus($this->_helper->getConfigData('order_status'));
        $stateObject->setIsNotified(false);
    }

    /**
     * Assign data to info model instance.
     *
     * @param \Magento\Framework\DataObject|mixed $data
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function assignData(\Magento\Framework\DataObject $data)
    {
        parent::assignData($data);

        if (!$data instanceof \Magento\Framework\DataObject) {
            $data = new \Magento\Framework\DataObject($data);
        }

        $additionalData = $data->getAdditionalData();
        $infoInstance = $this->getInfoInstance();

        return $this;
    }

    /**
     * Checkout redirect URL.
     *
     * @see \Magento\Checkout\Controller\Onepage::savePaymentAction()
     * @see \Magento\Quote\Model\Quote\Payment::getCheckoutRedirectUrl()
     *
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return $this->_urlBuilder->getUrl(
            'realexpayments_hpp/process/process',
            ['_secure' => $this->_getRequest()->isSecure()]
        );
    }

    /**
     * Retrieve request object.
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    protected function _getRequest()
    {
        return $this->_request;
    }

    /**
     * Post request to gateway and return response.
     *
     * @param DataObject $request
     * @param ConfigInterface $config
     */
    public function postRequest(DataObject $request, ConfigInterface $config)
    {
        // Do nothing
        $this->_helper->logDebug('Gateway postRequest called');
    }

    /**
     * @desc Get hpp form url
     *
     * @return string
     */
    public function getFormUrl()
    {
        return $this->_helper->getFormUrl();
    }

    /**
     * @desc Sets all the fields that is posted to HPP
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormFields()
    {
        $paymentInfo = $this->getInfoInstance();
        $order = $paymentInfo->getOrder();

        foreach ($order->getAllItems() as $item) {
            $product_id = $item->getData('product_id');
            if ($this->productRepository->getById($product_id)->getData('is_regulated') == 'regulated') {
                $merchantAccount = $this->scopeConfig->getValue(self::MERCHANT_UKVI, ScopeInterface::SCOPE_STORE, $order->getStoreId());
            } else {
                $merchantAccount = $this->scopeConfig->getValue(self::MERCHANT_TLSCONTACT, ScopeInterface::SCOPE_STORE, $order->getStoreId());
            }
            break;
        }
        $timestamp = strftime('%Y%m%d%H%M%S');
        $merchantId = trim($this->_helper->getConfigData('merchant_id'));
        if (empty($merchantAccount)) {
            $merchantAccount = trim($this->_helper->getConfigData('merchant_account'));
        }
        $realOrderId = $order->getRealOrderId();
        $fieldOrderId = $realOrderId . '_' . $timestamp;
        $orderCurrencyCode = $order->getBaseCurrencyCode();
        $amount = $this->_helper->amountFromMagento($order->getBaseGrandTotal(), $orderCurrencyCode);
        $customerId = $order->getCustomerId();
        $settleMode = $this->_helper->getConfigData('settle_mode');
        switch ($settleMode) {
            case SettleMode::SETTLEMODE_AUTO:
                $autoSettle = '1';
                break;
            case SettleMode::SETTLEMODE_MULTI:
                $autoSettle = 'MULTI';
                break;
            default:
                $autoSettle = '0';
                break;
        }
        $cardPaymentText = $this->_helper->getConfigData('payment_btn_text');
        $realexLang = $this->_helper->getConfigData('lang');
        $paymentMethods = $this->_helper->getConfigData('payment_methods');
        $varRef = $this->_helper->getConfigData('hpp_desc');

        // Set Var Ref by GWF
        $customerFirstName = $order->getCustomerFirstname();
        if ($customerFirstName != null) {
            $varRef = $customerFirstName;
        }

        $prodId = '';
        $shopperLocale = $this->_resolver->getLocale();
        $otbEnabled = $this->_helper->getConfigData('otb_enabled');
        $iframeEnabled = $this->_helper->getConfigData('iframe_enabled');

        if ($order->getBillingAddress()) {
            $billingCountryCode = $order->getBillingAddress()->getCountryId();
            $street = $order->getBillingAddress()->getStreet();
            if (isset($street[0]) && $billingCountryCode == "GB") {
                $addresBit = preg_replace('/\D/', '', $street[0]);
                if (strlen($addresBit) > 5) {
                    $addresBit = substr($addresBit, 0, 5);
                }
            } else {
                $addresBit = $street[0];
            }
            $postalBit = $order->getBillingAddress()->getPostcode();
            if ($billingCountryCode == "GB") {
                $postalBit = preg_replace('/\D/', '', $postalBit);
                if (strlen($postalBit) > 5) {
                    $postalBit = substr($postalBit, 0, 5);
                }
            }
            $billingPostalCode = $postalBit . '|' . $addresBit;
        } else {
            $billingCountryCode = '';
            $billingPostalCode = '';
        }
        if ($order->getShippingAddress()) {
            $shippingCountryCode = $order->getShippingAddress()->getCountryId();
            $street = $order->getShippingAddress()->getStreet();
            if (isset($street[0]) && $shippingCountryCode == "GB") {
                $addresBit = preg_replace('/\D/', '', $street[0]);
                if (strlen($addresBit) > 5) {
                    $addresBit = substr($addresBit, 0, 5);
                }
            } else {
                $addresBit = $street[0];
            }
            $postalBit = $order->getShippingAddress()->getPostcode();
            if ($shippingCountryCode == "GB") {
                $postalBit = preg_replace('/\D/', '', $postalBit);
                if (strlen($postalBit) > 5) {
                    $postalBit = substr($postalBit, 0, 5);
                }
            }
            $shippingPostalCode = $postalBit . '|' . $addresBit;
        } else {
            $shippingCountryCode = '';
            $shippingPostalCode = '';
        }

        $formFields = [];
        $formFields['MERCHANT_ID'] = $merchantId;
        $formFields['ACCOUNT'] = $merchantAccount;
        $formFields['ORDER_ID'] = $fieldOrderId;
        $formFields['AMOUNT'] = $amount;
        $formFields['CURRENCY'] = $orderCurrencyCode;
        $formFields['TIMESTAMP'] = $timestamp;
        $formFields['AUTO_SETTLE_FLAG'] = $autoSettle;
        $formFields['SHIPPING_CODE'] = $shippingPostalCode;
        $formFields['SHIPPING_CO'] = $shippingCountryCode;
        $formFields['BILLING_CODE'] = $billingPostalCode;
        $formFields['BILLING_CO'] = $billingCountryCode;
        $formFields['CUST_NUM'] = empty($customerId) ? self::GUEST_ID : $customerId;
        $formFields['VAR_REF'] = $varRef;
        $formFields['PROD_ID'] = $prodId;
        $formFields['HPP_VERSION'] = '2';
        if (isset($realexLang) && !empty($realexLang)) {
            $formFields['HPP_LANG'] = $realexLang;
        }
        if (isset($cardPaymentText) && !empty($cardPaymentText)) {
            $formFields['CARD_PAYMENT_BUTTON'] = $cardPaymentText;
        }
        if (isset($paymentMethods) && !empty($paymentMethods)) {
            $formFields['PM_METHODS'] = $paymentMethods;
        }
        if ($otbEnabled) {
            $formFields['VALIDATE_CARD_ONLY'] = '1';
        }
        $baseUrl = $this->_storeManager->getStore($this->getStore())
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);

        if ($iframeEnabled) {
            $formFields['HPP_POST_DIMENSIONS'] = $baseUrl;
        }
        $formFields['COMMENT1'] = $baseUrl;
        $formFields = $this->setDMFields($formFields, $order);
        $formFields = $this->setAPMFields($formFields, $order->getShippingAddress());
        $formFields['MERCHANT_RESPONSE_URL'] = $baseUrl . 'realexpayments_hpp/process/result';

        $cardStoreEnabled = $this->_helper->getConfigData('card_storage_enabled');
        if ($cardStoreEnabled && !empty($customerId)) {
            //Load payer ref customer attribute
            $payerAttr = $this->_customerRepository->getById($customerId)
                ->getCustomAttribute('realexpayments_hpp_payerref');
            $payerRef = (isset($payerAttr) && $payerAttr != null) ? $payerAttr->getValue() : '';

            $formFields = $this->setCardStorageFields($formFields, $payerRef);
            $fieldsToSign = "$timestamp.$merchantId.$fieldOrderId.$amount.$orderCurrencyCode.$payerRef.";
        } else {
            $fieldsToSign = "$timestamp.$merchantId.$fieldOrderId.$amount.$orderCurrencyCode";
        }
        //Fraud mode
        $fraudMode = $this->_helper->getConfigData('fraud_mode');
        if (isset($fraudMode) && !empty($fraudMode) && $fraudMode != FraudMode::FRAUDMODE_DEFAULT) {
            $formFields['HPP_FRAUDFILTER_MODE'] = $fraudMode;
            $fieldsToSign = $fieldsToSign . '.' . $fraudMode;
        }
        $sha1hash = $this->_helper->signFields($fieldsToSign);
        $this->_helper->logDebug('Gateway Request:' .
            print_r($this->_helper->stripFields($formFields), true));

        $formFields['SHA1HASH'] = $sha1hash;
        // Sort the array by key using SORT_STRING order
        ksort($formFields, SORT_STRING);

        return $formFields;
    }

    /**
     * Set Alternate Payment Method Fields.
     *
     * @param array $formFields
     * @param \Magento\Sales\Model\Order\Address|null $shipping
     *
     * @return $array
     */
    private function setAPMFields($formFields, $shipping)
    {
        $desc = $this->_helper->getConfigData('hpp_desc');
        $formFields['HPP_DESCRIPTOR'] = isset($desc) && !empty($desc) ? $desc : $formFields['VAR_REF'];
        $formFields['SHIPPING_ADDRESS_ENABLE'] = $this->_helper->getConfigData('apm_pass_shipping');
        $formFields['ADDRESS_OVERRIDE'] = $this->_helper->getConfigData('apm_address_override');
        if ($shipping) {
            $lastName = $shipping->getLastname();
            $lastName = isset($lastName) && !empty($lastName) ? ' ' . $lastName : '';
            $city = $shipping->getCity();
            $state = $shipping->getRegionCode();
            $postalCode = $shipping->getPostcode();
            $country = $shipping->getCountryId();
            $phone = $shipping->getTelephone();
            $name = $shipping->getFirstname() . $lastName;
            $street = $shipping->getStreet();
            $formFields['HPP_NAME'] = isset($name) ? $name : self::NOT_AVAILABLE;
            $formFields['HPP_STREET'] = isset($street[0]) ? $street[0] : self::NOT_AVAILABLE;
            $formFields['HPP_STREET2'] = isset($street[1]) ? $street[1] : self::NOT_AVAILABLE;
            $formFields['HPP_CITY'] = isset($city) ? $city : self::NOT_AVAILABLE;
            $formFields['HPP_STATE'] = isset($state) ? $state : self::NOT_AVAILABLE;
            $formFields['HPP_ZIP'] = isset($postalCode) ? $postalCode : self::NOT_AVAILABLE;
            $formFields['HPP_COUNTRY'] = isset($country) ? $country : self::NOT_AVAILABLE;
            $formFields['HPP_PHONE'] = isset($phone) ? $phone : self::NOT_AVAILABLE;
        } else {
            $formFields['SHIPPING_ADDRESS_ENABLE'] = 0;
        }

        return $formFields;
    }

    private function setDMFields($formFields, $order)
    {
        $enabled = $this->_helper->getConfigData('dm_enabled');
        $fields = $this->_helper->getConfigData('dm_fields');
        if (!isset($fields) || empty($fields) || !isset($enabled) || !$enabled) {
            return $formFields;
        }
        $dmProfile = $this->_helper->getConfigData('dm_profile');
        if (isset($dmProfile) && !empty($dmProfile)) {
            $formFields['HPP_FRAUD_DM_DECISIONMANAGERPROFILE'] = $dmProfile;
        }
        $sessionId = $this->_helper->getDMSessionId();
        $formFields['HPP_CUSTOMER_DEVICEFINGERPRINT'] = $sessionId;

        $fields = explode(',', $fields);
        if ($order->getBillingAddress()) {
            $formFields = $this->setDMBilling($formFields, $order, $fields);
        }
        if ($order->getShippingAddress()) {
            $formFields = $this->setDMShipping($formFields, $order, $fields);
        }
        $formFields = $this->setDMCustomer($formFields, $order, $fields);

        if (in_array(DMFields::DM_PRODUCTS_TOTAL, $fields)) {
            $formFields[DMFields::DM_PRODUCTS_TOTAL] = $order->getBaseTotalDue();
        }
        if (in_array(DMFields::DM_FRAUD_HOST, $fields)) {
            $formFields[DMFields::DM_FRAUD_HOST] = $_SERVER['HTTP_HOST'];
        }
        if (in_array(DMFields::DM_FRAUD_COOKIES, $fields)) {
            $formFields[DMFields::DM_FRAUD_COOKIES] = 'Yes';
        }
        if (in_array(DMFields::DM_FRAUD_BROWSER, $fields)) {
            $formFields[DMFields::DM_FRAUD_BROWSER] = $_SERVER['HTTP_USER_AGENT'];
        }
        if (in_array(DMFields::DM_FRAUD_IP, $fields)) {
            $formFields[DMFields::DM_FRAUD_IP] = $order->getRemoteIp();
        }
        if (in_array(DMFields::DM_FRAUD_TENDER, $fields)) {
            $formFields[DMFields::DM_FRAUD_TENDER] = $order->getPayment()->getMethodInstance()->getCode();
        }

        return $formFields;
    }

    private function setDMBilling($formFields, $order, $fields)
    {
        $billing = $order->getBillingAddress();
        $street = $billing->getStreet();
        if (in_array(DMFields::DM_BILL_STR1, $fields)) {
            $formFields[DMFields::DM_BILL_STR1] = isset($street[0]) ? $street[0] : self::NOT_AVAILABLE;
        }
        if (in_array(DMFields::DM_BILL_STR2, $fields)) {
            $formFields[DMFields::DM_BILL_STR2] = isset($street[1]) ? $street[1] : self::NOT_AVAILABLE;
        }
        if (in_array(DMFields::DM_BILL_CITY, $fields)) {
            $formFields[DMFields::DM_BILL_CITY] = $billing->getCity();
        }
        if (in_array(DMFields::DM_BILL_POSTAL, $fields)) {
            $formFields[DMFields::DM_BILL_POSTAL] = $billing->getPostcode();
        }
        if (in_array(DMFields::DM_BILL_STATE, $fields)) {
            $formFields[DMFields::DM_BILL_STATE] = $billing->getRegionCode();
        }
        if (in_array(DMFields::DM_BILL_COUNTRY, $fields)) {
            $formFields[DMFields::DM_BILL_COUNTRY] = $billing->getCountryId();
        }

        return $formFields;
    }

    private function setDMShipping($formFields, $order, $fields)
    {
        $shipping = $order->getShippingAddress();
        $street = $shipping->getStreet();
        if (in_array(DMFields::DM_SHIPPING_FIRST, $fields)) {
            $formFields[DMFields::DM_SHIPPING_FIRST] = $shipping->getFirstname();
        }
        if (in_array(DMFields::DM_SHIPPING_LAST, $fields)) {
            $formFields[DMFields::DM_SHIPPING_LAST] = $shipping->getLastname();
        }
        if (in_array(DMFields::DM_SHIPPING_PHONE, $fields)) {
            $formFields[DMFields::DM_SHIPPING_PHONE] = $shipping->getTelephone();
        }
        if (in_array(DMFields::DM_CUSTOMER_PHONE, $fields)) {
            $formFields[DMFields::DM_CUSTOMER_PHONE] = $shipping->getTelephone();
        }
        if (in_array(DMFields::DM_SHIPPING_METHOD, $fields)) {
            $formFields[DMFields::DM_SHIPPING_METHOD] = $order->getShippingDescription();
        }
        if (in_array(DMFields::DM_SHIPPING_STR1, $fields)) {
            $formFields[DMFields::DM_SHIPPING_STR1] = isset($street[0]) ? $street[0] : self::NOT_AVAILABLE;
        }
        if (in_array(DMFields::DM_SHIPPING_STR2, $fields)) {
            $formFields[DMFields::DM_SHIPPING_STR2] = isset($street[1]) ? $street[1] : self::NOT_AVAILABLE;
        }
        if (in_array(DMFields::DM_SHIPPING_CITY, $fields)) {
            $formFields[DMFields::DM_SHIPPING_CITY] = $shipping->getCity();
        }
        if (in_array(DMFields::DM_SHIPPING_POSTAL, $fields)) {
            $formFields[DMFields::DM_SHIPPING_POSTAL] = $shipping->getPostcode();
        }
        if (in_array(DMFields::DM_SHIPPING_STATE, $fields)) {
            $formFields[DMFields::DM_SHIPPING_STATE] = $shipping->getRegionCode();
        }
        if (in_array(DMFields::DM_SHIPPING_COUNTRY, $fields)) {
            $formFields[DMFields::DM_SHIPPING_COUNTRY] = $shipping->getCountryId();
        }

        return $formFields;
    }

    private function setDMCustomer($formFields, $order, $fields)
    {
        if (in_array(DMFields::DM_CUSTOMER_ID, $fields)) {
            $formFields[DMFields::DM_CUSTOMER_ID] = $order->getCustomerId();
        }
        if (in_array(DMFields::DM_CUSTOMER_DOB, $fields)) {
            //return dob with time portion removed. note the space in front
            $formFields[DMFields::DM_CUSTOMER_DOB] = str_replace(' 00:00:00', '', $order->getCustomerDOB());
        }
        if (in_array(DMFields::DM_CUSTOMER_EMAIL_DOMAIN, $fields)) {
            $email = $order->getCustomerEmail();
            if (isset($email)) {
                $atIndex = strpos($email, '@');
                if ($atIndex > 0) {
                    $formFields[DMFields::DM_CUSTOMER_EMAIL_DOMAIN] = substr($email, $atIndex + 1);
                }
            }
        }
        if (in_array(DMFields::DM_CUSTOMER_EMAIL, $fields)) {
            $formFields[DMFields::DM_CUSTOMER_EMAIL] = $order->getCustomerEmail();
        }
        if (in_array(DMFields::DM_CUSTOMER_FIRST, $fields)) {
            $name = $order->getCustomerFirstname();
            if (!isset($name) || empty($name)) {
                if ($order->getBillingAddress()) {
                    $name = $order->getBillingAddress()->getFirstname();
                }
            }
            $formFields[DMFields::DM_CUSTOMER_FIRST] = $name;
        }
        if (in_array(DMFields::DM_CUSTOMER_LAST, $fields)) {
            $lastName = $order->getCustomerLastname();
            if (!isset($lastName) || empty($lastName)) {
                if ($order->getBillingAddress()) {
                    $lastName = $order->getBillingAddress()->getLastname();
                }
            }
            $formFields[DMFields::DM_CUSTOMER_LAST] = $lastName;
        }

        return $formFields;
    }

    /**
     * Set Card Storage Fields.
     *
     * @param array $formFields
     * @param string $payerRef
     *
     * @return $array
     */
    private function setCardStorageFields($formFields, $payerRef)
    {
        return $this->_helper->setCardStorageFields($formFields, $payerRef);
    }

    /**
     * Capture.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        parent::capture($payment, $amount);

        $currencyCode = $payment->getOrder()->getBaseCurrencyCode();
        $realexAmount = $this->_helper->amountFromMagento($amount, $currencyCode);
        if ($payment->getAdditionalInformation('AUTO_SETTLE_FLAG') != SettleMode::SETTLEMODE_MULTI) {
            $response = $this->_remoteXml->settle($payment, $realexAmount);
        } else {
            $response = $this->_remoteXml->multisettle($payment, $realexAmount);
        }
        if (!isset($response) || !$response) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The capture action failed'));
        }
        $fields = $response->toArray();
        if ($fields['RESULT'] != '00') {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The capture action failed. Gateway Response - Error ' . $fields['RESULT'] . ': ' .
                    $fields['MESSAGE'])
            );
        }
        $payment->setTransactionId($fields['PASREF'])
            ->setTransactionApproved(true)
            ->setParentTransactionId($payment->getAdditionalInformation('PASREF'))
            ->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $fields);

        return $this;
    }


    /**
     * Refund specified amount for payment.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function void(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::void($payment);
        $response = $this->_remoteXml->void($payment, []);
        if (!isset($response) || !$response) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The void action failed'));
        }
        $fields = $response->toArray();
        if ($fields['RESULT'] != '00') {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The void action failed. Gateway Response - Error ' . $fields['RESULT'] . ': ' .
                    $fields['MESSAGE'])
            );
        }
        $payment->setTransactionId($fields['PASREF'])
            ->setParentTransactionId($payment->getAdditionalInformation('PASREF'))
            ->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $fields);

        return $this;
    }

    /**
     * Accept under review payment.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     *
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function acceptPayment(\Magento\Payment\Model\InfoInterface $payment)
    {
        parent::acceptPayment($payment);
        $response = $this->_remoteXml->releasePayment($payment, []);
        if (!isset($response) || !$response) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The accept payment action failed'));
        }
        $fields = $response->toArray();
        if ($fields['RESULT'] != '00') {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The accept payment action failed. Gateway Response - Error ' . $fields['RESULT'] .
                    ': ' . $fields['MESSAGE'])
            );
        }
        $payment->setTransactionId($fields['PASREF'])
            ->setParentTransactionId($payment->getAdditionalInformation('PASREF'))
            ->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $fields);

        return $this;
    }

    public function hold(\Magento\Payment\Model\InfoInterface $payment)
    {
        $response = $this->_remoteXml->holdPayment($payment, []);

        if (!isset($response) || !$response) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The hold action failed'));
        }
        $fields = $response->toArray();
        if ($fields['RESULT'] != '00') {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The hold action failed. Gateway Response - Error ' . $fields['RESULT'] . ': ' .
                    $fields['MESSAGE'])
            );
        }
        $payment->setTransactionId($fields['PASREF'])
            ->setParentTransactionId($payment->getAdditionalInformation('PASREF'))
            ->setTransactionAdditionalInfo(\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS, $fields);

        return $this;
    }
}
