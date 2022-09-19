<?php

namespace Amasty\Stripe\Model;

use Amasty\Stripe\Api\CustomerRepositoryInterface;
use Amasty\Stripe\Model\Adapter\StripeAdapterProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Stripe\Error\InvalidRequest;
use Stripe\PaymentMethod;

/**
 * Managements for Stripe accounts
 */
class StripeAccountManagement
{
    /**
     * @var StripeAdapterProvider
     */
    private $adapterProvider;

    /**
     * @var SessionManagerInterface
     */
    private $session;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    public function __construct(
        StripeAdapterProvider $adapterProvider,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory,
        SessionManagerInterface $session
    ) {
        $this->adapterProvider = $adapterProvider;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->session = $session;
    }

    /**
     * @param array $stripeData
     * @param int|null $storeId
     * @return null|PaymentMethod
     */
    public function processSaveCard(array $stripeData, int $storeId = null): ?PaymentMethod
    {
        $paymentMethod = null;
        $account = $this->resolveStripeCustomer($storeId);
        if ($account) {
            $accountId = $account->id;
            $paymentMethod = $this->createCard($stripeData[0], $accountId, $storeId);
        }

        return $paymentMethod;
    }

    /**
     * @param string $stripeData
     */
    public function processDeleteCard(string $stripeData)
    {
        $this->deleteCard($stripeData);
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getAllCards(int $storeId = null): array
    {
        $cardsData = [];

        try {
            $stripeAdapter = $this->adapterProvider->get($storeId);
            /** @var \Amasty\Stripe\Model\Customer $customer */
            $customer = $this->customerRepository->getStripeCustomer(
                $this->session->getCustomerId(),
                $stripeAdapter->getAccountId()
            );
            $cards = $stripeAdapter->listOfCards($customer->getStripeCustomerId());

            if ($cards) {
                $cardsData = $cards->data;
            }
        } catch (NoSuchEntityException $exception) {
            return [];
        }

        return $cardsData;
    }

    /**
     * @param int|null $storeId
     * @return ?\Stripe\Customer
     */
    private function createAccount(int $storeId = null): ?\Stripe\Customer
    {
        $stripeAdapter = $this->adapterProvider->get($storeId);
        $customerEmail = $this->session->getQuote()
            ? $this->session->getQuote()->getCustomer()->getEmail()
            : $this->session->getCustomer()->getEmail();
        $accountData = [
            'email' => $customerEmail,
            'description' => 'Magento customer ID is ' . $this->session->getCustomerId()
        ];
        /** @var \Stripe\Customer $account */
        $account = $stripeAdapter->customerCreate($accountData);
        $accountId = $account->id;
        /** @var \Amasty\Stripe\Model\Customer $customer */
        $customer = $this->customerFactory->create();
        $customer->setCustomerId($this->session->getCustomerId());
        $customer->setStripeCustomerId($accountId);
        $customer->setAccountCustomerId($stripeAdapter->getAccountId());
        $this->customerRepository->save($customer);

        return $account;
    }

    /**
     * @param string $paymentMethod
     */
    private function deleteCard($paymentMethod)
    {
        $stripeAdapter = $this->adapterProvider->get();
        $stripeAdapter->detachPaymentMethod($paymentMethod);
    }

    /**
     * @param string $paymentMethod
     * @param string $accountId
     * @param int|null $storeId
     * @return bool|object|PaymentMethod|null
     */
    public function createCard(string $paymentMethod, string $accountId, int $storeId = null)
    {
        $stripeAdapter = $this->adapterProvider->get();
        /** @var \Stripe\Source $retrievedSource */
        $retrievedPayment = $stripeAdapter->paymentRetrieve($paymentMethod);
        $existingPaymentMethod = $this->paymentMethodResolver($retrievedPayment->card->fingerprint, $storeId);

        if ($existingPaymentMethod) {
            return $existingPaymentMethod;
        }
        if ($retrievedPayment
            && !$this->checkExistCard($retrievedPayment, $accountId)
        ) {
            $retrievedPayment->attach(['customer' => $accountId]);
        }
        return $retrievedPayment;
    }

    /**
     * @param string $fingerprint
     * @param int|null $storeId
     * @return bool|object
     */
    private function paymentMethodResolver(string $fingerprint, int $storeId = null)
    {
        $paymentMethods = $this->getAllCards($storeId);
        foreach ($paymentMethods as $paymentMethod) {
            if ($paymentMethod->card->fingerprint == $fingerprint) {
                return $paymentMethod;
            }
        }

        return false;
    }

    /**
     * @param \Stripe\Source $paymentMethod
     * @param string $accountId
     *
     * @return bool
     */
    private function checkExistCard(PaymentMethod $paymentMethod, $accountId)
    {
        $stripeAdapter = $this->adapterProvider->get();
        /** @var \Stripe\ApiResource $allCards */
        $allCards = $stripeAdapter->listOfCards($accountId);
        $isExist = false;

        $fingerPrint = $this->getFingerPrint($paymentMethod);

        if (!$fingerPrint) {
            return $isExist;
        }

        if ($allCards && $allCards->data) {
            /** @var \Stripe\Source $card */
            foreach ($allCards->data as $existCard) {
                $existFingerPrint = $this->getFingerPrint($existCard);

                if ($existFingerPrint === $fingerPrint) {
                    $isExist = true;
                    break;
                }
            }
        }

        return $isExist;
    }

    /**
     * @param string $source
     *
     * @return string|null
     */
    private function getFingerPrint($paymentMethod)
    {
        if ($paymentMethod->three_d_secure) {
            $fingerPrint = $paymentMethod->three_d_secure->fingerprint;
        } elseif ($paymentMethod->card) {
            $fingerPrint = $paymentMethod->card->fingerprint;
        } else {
            $fingerPrint = $paymentMethod->fingerprint;
        }

        return $fingerPrint;
    }

    /**
     * Return Stripe Customer Id associated with current Customer.
     * If no associated stripe customer then create new
     *
     * @param int|null $storeId
     * @return \Stripe\Customer|null
     */
    public function resolveStripeCustomer(int $storeId = null): ?\Stripe\Customer
    {
        $customerId = $this->session->getCustomerId();
        if (!$customerId) {
            return null;
        }

        try {
            $stripeAdapter = $this->adapterProvider->get($storeId);
            /** @var \Amasty\Stripe\Model\Customer $customer */
            $customer = $this->customerRepository->getStripeCustomer(
                $customerId,
                $stripeAdapter->getAccountId()
            );
            $customerId = $customer->getStripeCustomerId();
            $account = $stripeAdapter->customerRetrieve($customerId);
            if ($account->isDeleted()) {
                throw new LocalizedException(__('Stripe account was deleted'));
            }
        } catch (NoSuchEntityException $exception) {
            /** @var \Stripe\Customer $account */
            $account = $this->createAccount($storeId);
        } catch (LocalizedException $exception) {
            $this->customerRepository->delete($customer);
            /** @var \Stripe\Customer $account */
            $account = $this->createAccount($storeId);
        } catch (\Stripe\Exception\InvalidRequestException $stripeException) {
            $this->customerRepository->delete($customer);
            /** @var \Stripe\Customer $account */
            $account = $this->createAccount($storeId);
        } catch (InvalidRequest $exception) {
            $this->customerRepository->delete($customer);
            /** @var \Stripe\Customer $account */
            $account = $this->createAccount($storeId);
        }

        return $account;
    }

    /**
     * Get current customer ID For Stripe
     *
     * @param int|null $storeId
     * @return \Stripe\Customer|null
     */
    public function getCurrentStripeCustomerId(int $storeId = null): ?\Stripe\Customer
    {
        return $this->resolveStripeCustomer($storeId);
    }
}
