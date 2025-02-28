<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

class GeneratedPaymentOrderManagement
{
    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Paynopain\Payments\Api\GeneratedPaymentOrderRepositoryInterface $repositoryInterface
     * @param \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterfaceFactory $entityInterface
     * @param \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement $generateOrderPaymentManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\CaptureConfirmation $captureConfirmation
     * @param \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\ByWebService $byWebService
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Sales\Api\Data\OrderInterfaceFactory $orderInterfaceFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Paynopain\Payments\Api\GeneratedPaymentOrderRepositoryInterface $repositoryInterface,
        protected \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterfaceFactory $entityInterface,
        protected \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement $generateOrderPaymentManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\CaptureConfirmation $captureConfirmation,
        protected \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\ByWebService $byWebService,
        protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        protected \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirerInterface,
        protected \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        protected \Magento\Framework\UrlInterface $urlBuilder,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        protected \Magento\Sales\Api\Data\OrderInterfaceFactory $orderInterfaceFactory,
        protected \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->configHelperData = $configHelperData;
        $this->repositoryInterface = $repositoryInterface;
        $this->entityInterface = $entityInterface;
        $this->generateOrderPaymentManagement = $generateOrderPaymentManagement;
        $this->captureConfirmation = $captureConfirmation;
        $this->byWebService = $byWebService;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->countryInformationAcquirerInterface = $countryInformationAcquirerInterface;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->urlBuilder = $urlBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderInterfaceFactory = $orderInterfaceFactory;
        $this->remoteAddress = $remoteAddress;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \stdClass $response
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function saveEntity(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \stdClass $response
    ): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface {
        try {
            /** @var \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder */
            $generatedPaymentOrder = $this->entityInterface->create();
            $generatedPaymentOrder->setSalesOrderEntityId((int) $order->getId());
            $generatedPaymentOrder->setCurrentTime($response->current_time);
            $generatedPaymentOrder->setCode($response->code);
            $generatedPaymentOrder->setMessage($response->message);
            $generatedPaymentOrder->setToken($response->order->token);
            $generatedPaymentOrder->setOrderUUID($response->order->uuid);
            $generatedPaymentOrder->setOrderCreated($response->order->created);
            $generatedPaymentOrder->setOrderCreatedFromClientTimeZone($response->order->created_from_client_timezone);
            $generatedPaymentOrder->setOrderPaid((int) $response->order->paid);
            $generatedPaymentOrder->setOrderStatus($response->order->status);
            $generatedPaymentOrder->setOrderCustomer($response->order->customer);
            $generatedPaymentOrder->setValidationHash($response->validation_hash);

            $this->repositoryInterface->save($generatedPaymentOrder);

            return $generatedPaymentOrder;
        } catch (\Exception $e) {
            $this->logger->debugLogs('GeneratedPaymentOrder::SAVE ENTITY::ERROR::' . serialize($e->getMessage()));
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param boolean $saveCard
     * @param string|null $sourceUUID
     * @return \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement
     */
    public function generatePaymentOrderRequest(
        \Magento\Sales\Api\Data\OrderInterface $order,
        bool $saveCard = false,
        ?string $sourceUUID = null
    ): \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement {
        $this->configHelperData->setPaymentMethodCode($order->getPayment()->getMethodInstance()->getCode());

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->service = $this->configHelperData->getService();
        $object->requestObject->operative = $this->configHelperData->getOperative();
        $object->requestObject->secure = $this->configHelperData->getSecure();

        $object->requestObject->customer_ext_id = $order->getCustomerEmail();
        if (!$order->getCustomerIsGuest()) {
            $object->requestObject->customer_ext_id = $order->getCustomerId();
        }

        if ($sourceUUID != null && $object->requestObject->secure) {
            $object->requestObject->source_uuid = $sourceUUID;
        } else {
            $object->requestObject->save_card = $saveCard;
        }

        $object->requestObject->reference = $order->getIncrementId();
        $object->requestObject->description = $this->storeManagerInterface->getWebsite()->getName();
        $object->requestObject->description .= ' ' . $this->configHelperData->getBaseUrl();

        $urlBuilderData = [];
        if ($object->requestObject->secure) {
            $urlBuilderData = [
                '_secure' => $this->storeManagerInterface->getStore()->isCurrentlySecure(),
            ];
        }
        $queryParamsUrlKo = $urlBuilderData;
        $queryParamsUrlKo['_query'] = [
            'soe_increment_id' => $order->getIncrementId(),
        ];

        $onePageSuccessUrl = 'checkout/onepage/success';
        $queryParamsUrlIframeSuccess = [];
        if ($this->configHelperData->getIntegrationType() == 'iframe') {
            $onePageSuccessUrl = 'paynopain/success/iframe';
            $queryParamsUrlIframeSuccess = $queryParamsUrlKo;
        }

        $object->requestObject->url_post = $this->urlBuilder->getUrl('paynopain/response/process', $urlBuilderData);
        $object->requestObject->url_ok = $this->urlBuilder->getUrl($onePageSuccessUrl, $queryParamsUrlIframeSuccess);
        $object->requestObject->url_ko = $this->urlBuilder->getUrl('paynopain/response/ko', $queryParamsUrlKo);

        $object->requestObject->amount = intval(strval(number_format((float) str_replace(',', '.', $order->getGrandTotal()), 2, '.', '') * 100));

        $skus = [];
        foreach ($order->getAllVisibleItems() as $itemId => $item) {
            $skus[] = $item->getSku() . ' x ' . $item->getQtyOrdered();
        }
        $object->requestObject->additional = implode("\n", $skus);

        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();
        $defaultShippingId = 0;
        $defaultBillingId = 0;

        try {
            $customer = $this->customerRepositoryInterface->getById($order->getCustomerId());
            $defaultShippingId = $customer->getDefaultShipping();
            $defaultBillingId = $customer->getDefaultBilling();
        } catch (\Exception $e) {
            //...
        }

        $extraData = new \stdClass();

        $profile = new \stdClass();
        $profile->first_name = $order->getCustomerFirstName();
        $profile->last_name = $order->getCustomerLastName();
        $profile->email = $order->getCustomerEmail();
        $phone = new \stdClass();
        $phone->number = $billingAddress->getTelephone();
        $profile->phone = $phone;
        $extraData->profile = $profile;

        $address = new \stdClass();
        $address->address1 = $billingAddress->getStreetLine(1);
        $address->address2 = $billingAddress->getStreetLine(2);
        $address->city = $billingAddress->getCity();

        $countryData = $this->countryInformationAcquirerInterface->getCountryInfo($billingAddress->getCountryId());
        $address->country = $countryData->getThreeLetterAbbreviation();

        $address->default = $billingAddress->getId() == $defaultBillingId ? true : false;
        $address->state_code = ($billingAddress->getRegion() === null) ? '' : $billingAddress->getRegion();
        $address->type = $billingAddress->getAddressType();
        $address->zip_code = $billingAddress->getPostcode();
        $extraData->address = $address;

        $billing_address = new \stdClass();
        $billing_address->address1 = $billingAddress->getStreetLine(1);
        $billing_address->address2 = $billingAddress->getStreetLine(2);
        $billing_address->city = $billingAddress->getCity();
        $countryData = $this->countryInformationAcquirerInterface->getCountryInfo($billingAddress->getCountryId());
        $billing_address->country = $countryData->getThreeLetterAbbreviation();

        $billing_address->default = $billingAddress->getId() == $defaultBillingId ? true : false;
        $billing_address->state_code = ($billingAddress->getRegion() === null) ? '' : $billingAddress->getRegion();
        $billing_address->type = $billingAddress->getAddressType();
        $billing_address->zip_code = $billingAddress->getPostcode();
        $extraData->billing_address = $billing_address;

        $shipping_address = new \stdClass();
        $shipping_address->address1 = $shippingAddress->getStreetLine(1);
        $shipping_address->address2 = $shippingAddress->getStreetLine(2);
        $shipping_address->city = $shippingAddress->getCity();

        $countryData = $this->countryInformationAcquirerInterface->getCountryInfo($shippingAddress->getCountryId());
        $shipping_address->country = $countryData->getThreeLetterAbbreviation();

        $shipping_address->default = $shippingAddress->getId() == $defaultShippingId ? true : false;
        $shipping_address->state_code = ($shippingAddress->getRegion() === null) ? '' : $shippingAddress->getRegion();
        $shipping_address->type = $shippingAddress->getAddressType();
        $shipping_address->zip_code = $shippingAddress->getPostcode();
        $extraData->shipping_address = $shipping_address;

        $checkoutUUID = $this->configHelperData->getCheckoutUUID();
        if ($checkoutUUID) {
            $availablePaymentMethods = [
                'PAYMENT_CARD',
            ];

            $extraDataCheckout = new \stdClass();

            $extraDataCheckout->uuid = $checkoutUUID;
            $extraDataCheckout->payment_methods = $availablePaymentMethods;

            $extraData->checkout = $extraDataCheckout;
        }

        $object->requestObject->extra_data = $extraData;

        $this->generateOrderPaymentManagement->setEnvironment($this->configHelperData->getEnvironment());
        $this->generateOrderPaymentManagement->setAuthorization($object->authorization);
        $this->generateOrderPaymentManagement->setRequestObject($object->requestObject);

        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE PAYMENT ORDER Request::Environment::' . $this->generateOrderPaymentManagement->getEnvironment());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE PAYMENT ORDER Request::HTTP-METHOD::' . $this->generateOrderPaymentManagement->getHTTPMethod());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE PAYMENT ORDER Request::EndPoint::' . $this->generateOrderPaymentManagement->getEndPoint());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE PAYMENT ORDER Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE PAYMENT ORDER Request::' . json_encode($object));

        return $this->generateOrderPaymentManagement;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\CaptureConfirmation
     */
    public function generateCaptureConfirmationRequest(
        \Magento\Sales\Api\Data\OrderInterface $order,
    ): \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\CaptureConfirmation {
        $this->configHelperData->setPaymentMethodCode($order->getPayment()->getMethodInstance()->getCode());

        /** @var \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder */
        $generatedPaymentOrder = $this->getBySalesOrderEntityId((int) $order->getId());

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->order_uuid = $generatedPaymentOrder->getOrderUUID();
        $object->requestObject->amount = intval(strval(number_format((float) str_replace(',', '.', (string) $order->getGrandTotal()), 2, '.', '') * 100));

        $this->captureConfirmation->setEnvironment($this->configHelperData->getEnvironment());
        $this->captureConfirmation->setAuthorization($object->authorization);
        $this->captureConfirmation->setRequestObject($object->requestObject);

        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE CAPTURE CONFIRMATION Request::Environment::' . $this->captureConfirmation->getEnvironment());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE CAPTURE CONFIRMATION Request::HTTP-METHOD::' . $this->captureConfirmation->getHTTPMethod());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE CAPTURE CONFIRMATION Request::EndPoint::' . $this->captureConfirmation->getEndPoint());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE CAPTURE CONFIRMATION Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE CAPTURE CONFIRMATION Request::' . json_encode($object));

        return $this->captureConfirmation;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param string $sourceId
     * @return \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\ByWebService
     */
    public function generateByWebServiceRequest(
        \Magento\Sales\Api\Data\OrderInterface $order,
        string $sourceId
    ): \Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement\ByWebService {
        $this->configHelperData->setPaymentMethodCode($order->getPayment()->getMethodInstance()->getCode());

        /** @var \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder */
        $generatedPaymentOrder = $this->getBySalesOrderEntityId((int) $order->getId());

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->order_uuid = $generatedPaymentOrder->getOrderUUID();
        $object->requestObject->card_uuid = $sourceId;
        $object->requestObject->customer_ip = $this->remoteAddress->getRemoteAddress();

        $this->byWebService->setEnvironment($this->configHelperData->getEnvironment());
        $this->byWebService->setAuthorization($object->authorization);
        $this->byWebService->setRequestObject($object->requestObject);

        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE BY WEB SERVICE Request::Environment::' . $this->byWebService->getEnvironment());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE BY WEB SERVICE Request::HTTP-METHOD::' . $this->byWebService->getHTTPMethod());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE BY WEB SERVICE Request::EndPoint::' . $this->byWebService->getEndPoint());
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE BY WEB SERVICE Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('GeneratePaymentOrder::GENERATE BY WEB SERVICE Request::' . json_encode($object));

        return $this->byWebService;
    }

    /**
     * @param string $orderUUID
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getSalesOrderEntityIdByOrderUUID(string $orderUUID): \Magento\Sales\Api\Data\OrderInterface
    {
        return $this->orderRepositoryInterface->get($this->getByOrderUUID($orderUUID)->getSalesOrderEntityId());
    }

    /**
     * @param string $orderUUID
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function getByOrderUUID(string $orderUUID): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface::ORDER_UUID,
            $orderUUID
        );

        $result = $this->repositoryInterface->getList($this->searchCriteriaBuilder->create());

        if (!$result->getTotalCount()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Generated payment order could not be found by its order uuid id %1', $orderUUID));
        }

        return current($result->getItems());
    }

    /**
     * @param integer $salesOrderEntityId
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function getBySalesOrderEntityId(int $salesOrderEntityId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface::SALES_ORDER_ENTITY_ID,
            $salesOrderEntityId
        );

        $result = $this->repositoryInterface->getList($this->searchCriteriaBuilder->create());

        if (!$result->getTotalCount()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Generated payment order could not be found by its sales order entity id %1', $salesOrderEntityId));
        }

        return current($result->getItems());
    }
}
