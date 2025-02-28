<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

use Magento\Framework\Model\AbstractModel;
use Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

class GeneratedPaymentOrder extends AbstractModel implements GeneratedPaymentOrderInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Paynopain\Payments\Model\ResourceModel\GeneratedPaymentOrder::class);
    }

    /**
     * @inheritDoc
     */
    public function getSalesOrderEntityId(): int
    {
        return (int) $this->getData(self::SALES_ORDER_ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSalesOrderEntityId(int $salesOrderEntityId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::SALES_ORDER_ENTITY_ID, $salesOrderEntityId);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEntityId(): int
    {
        return (int) $this->getData(self::CUSTOMER_ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEntityId(int $customerEntityId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::CUSTOMER_ENTITY_ID, $customerEntityId);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentTime(): string
    {
        return $this->getData(self::CURRENT_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentTime(string $currentTime): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::CURRENT_TIME, $currentTime);
    }

    /**
     * @inheritDoc
     */
    public function getClientUUID(): string
    {
        return $this->getData(self::CLIENT_UUID);
    }

    /**
     * @inheritDoc
     */
    public function setClientUUID(string $clientUUID): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::CLIENT_UUID, $clientUUID);
    }

    /**
     * @inheritDoc
     */
    public function getOrderServiceUUID(): string
    {
        return $this->getData(self::ORDER_SERVICE_UUID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderServiceUUID(string $orderServiceUUID): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_SERVICE_UUID, $orderServiceUUID);
    }

    /**
     * @inheritDoc
     */
    public function getIP(): string
    {
        return $this->getData(self::IP);
    }

    /**
     * @inheritDoc
     */
    public function setIP(?string $ip): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::IP, $ip);
    }

    /**
     * @inheritDoc
     */
    public function getCode(): int
    {
        return (int) $this->getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode(int $code): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $message): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return $this->getData(self::TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setToken(string $token): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * @inheritDoc
     */
    public function getOrderUUID(): string
    {
        return $this->getData(self::ORDER_UUID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderUUID(string $token): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_UUID, $token);
    }

    /**
     * @inheritDoc
     */
    public function getOrderCreated(): string
    {
        return $this->getData(self::ORDER_CREATED);
    }

    /**
     * @inheritDoc
     */
    public function setOrderCreated(string $orderCreated): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_CREATED, $orderCreated);
    }

    /**
     * @inheritDoc
     */
    public function getOrderCreatedFromClientTimeZone(): string
    {
        return $this->getData(self::ORDER_CREATED_FROM_CLIENT_TIMEZONE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderCreatedFromClientTimeZone(string $orderCreatedFromClientTimeZone): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_CREATED_FROM_CLIENT_TIMEZONE, $orderCreatedFromClientTimeZone);
    }

    /**
     * @inheritDoc
     */
    public function getOrderPaid(): int
    {
        return (int) $this->getData(self::ORDER_PAID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderPaid(int $orderPaid): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_PAID, $orderPaid);
    }

    /**
     * @inheritDoc
     */
    public function getOrderStatus(): string
    {
        return $this->getData(self::ORDER_STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setOrderStatus(string $orderStatus): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_STATUS, $orderStatus);
    }

    /**
     * @inheritDoc
     */
    public function getOrderSafe(): int
    {
        return (int) $this->getData(self::ORDER_SAFE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderSafe(int $orderSafe): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_SAFE, $orderSafe);
    }

    /**
     * @inheritDoc
     */
    public function getOrderService(): string
    {
        return $this->getData(self::ORDER_SERVICE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderService(string $orderService): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_SERVICE, $orderService);
    }

    /**
     * @inheritDoc
     */
    public function getOrderCustomer(): string
    {
        return $this->getData(self::ORDER_CUSTOMER);
    }

    /**
     * @inheritDoc
     */
    public function setOrderCustomer(?string $orderCustomer): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_CUSTOMER, $orderCustomer);
    }

    /**
     * @inheritDoc
     */
    public function getOrderUrls(): string
    {
        return $this->getData(self::ORDER_URLS);
    }

    /**
     * @inheritDoc
     */
    public function setOrderUrls(string $orderUrls): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::ORDER_URLS, $orderUrls);
    }

    /**
     * @inheritDoc
     */
    public function getValidationHash(): string
    {
        return $this->getData(self::VALIDATION_HASH);
    }

    /**
     * @inheritDoc
     */
    public function setValidationHash(string $validationHash): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        return $this->setData(self::VALIDATION_HASH, $validationHash);
    }
}
