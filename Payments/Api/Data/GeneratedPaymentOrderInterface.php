<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Api\Data;

interface GeneratedPaymentOrderInterface
{
    /** @var string */
    const TABLE = 'paynopain_generated_payment_order';

    /** @var string */
    const ENTITY_ID = 'entity_id';

    /** @var string */
    const SALES_ORDER_ENTITY_ID = 'sales_order_entity_id';

    /** @var string */
    const CUSTOMER_ENTITY_ID = 'customer_entity_id';

    /** @var string */
    const CURRENT_TIME = 'current_time';

    /** @var string */
    const CLIENT_UUID = 'client_uuid';

    /** @var string */
    const ORDER_SERVICE_UUID = 'order_service_uuid';

    /** @var string */
    const IP = 'ip';

    /** @var string */
    const CODE = 'code';

    /** @var string */
    const MESSAGE = 'message';

    /** @var string */
    const TOKEN = 'token';

    /** @var string */
    const ORDER_UUID = 'order_uuid';

    /** @var string */
    const ORDER_CREATED = 'order_created';

    /** @var string */
    const ORDER_CREATED_FROM_CLIENT_TIMEZONE = 'order_created_from_client_timezone';

    /** @var string */
    const ORDER_PAID = 'order_paid';

    /** @var string */
    const ORDER_STATUS = 'order_status';

    /** @var string */
    const ORDER_SAFE = 'order_safe';

    /** @var string */
    const ORDER_SERVICE = 'order_service';

    /** @var string */
    const ORDER_CUSTOMER = 'order_customer';

    /** @var string */
    const ORDER_URLS = 'order_urls';

    /** @var string */
    const VALIDATION_HASH = 'validation_hash';

    /**
     * @return integer
     */
    public function getSalesOrderEntityId(): int;

    /**
     * @param integer $salesOrderEntityId
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setSalesOrderEntityId(int $salesOrderEntityId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return integer
     */
    public function getCustomerEntityId(): int;

    /**
     * @param integer $customerEntityId
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setCustomerEntityId(int $customerEntityId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getCurrentTime(): string;

    /**
     * @param string $currentTime
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setCurrentTime(string $currentTime): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderServiceUUID(): string;

    /**
     * @param string $orderServiceUUID
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderServiceUUID(string $orderServiceUUID): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getIP(): string;

    /**
     * @param string|null $ip
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setIP(?string $ip): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return integer
     */
    public function getCode(): int;

    /**
     * @param integer $code
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setCode(int $code): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setMessage(string $message): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @param string $token
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setToken(string $token): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderUUID(): string;

    /**
     * @param string $orderUUID
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderUUID(string $orderUUID): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderCreated(): string;

    /**
     * @param string $orderCreated
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderCreated(string $orderCreated): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderCreatedFromClientTimeZone(): string;

    /**
     * @param string $orderCreatedFromClientTimeZone
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderCreatedFromClientTimeZone(string $orderCreatedFromClientTimeZone): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return integer
     */
    public function getOrderPaid(): int;

    /**
     * -
     *
     * @param integer $orderPaid
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderPaid(int $orderPaid): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderStatus(): string;

    /**
     * @param string $orderStatus
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderStatus(string $orderStatus): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;
    /**
     * @return string
     */
    public function getOrderSafe(): int;

    /**
     * @param int $orderSafe
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderSafe(int $orderSafe): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderService(): string;

    /**
     * @param string $orderService
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderService(string $orderService): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderCustomer(): string;

    /**
     * @param string|null $orderCustomer
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderCustomer(?string $orderCustomer): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getOrderUrls(): string;

    /**
     * @param string $orderUrls
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setOrderUrls(string $orderUrls): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * @return string
     */
    public function getValidationHash(): string;

    /**
     * @param string $validationHash
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     */
    public function setValidationHash(string $validationHash): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;
}
