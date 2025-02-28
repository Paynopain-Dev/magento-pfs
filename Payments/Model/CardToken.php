<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

use Magento\Framework\Model\AbstractModel;
use Paynopain\Payments\Api\Data\CardTokenInterface;

class CardToken extends AbstractModel implements CardTokenInterface
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Paynopain\Payments\Model\ResourceModel\CardToken::class);
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
    public function setCustomerEntityId(int $customerEntityId): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::CUSTOMER_ENTITY_ID, $customerEntityId);
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
    public function setCode(int $code): \Paynopain\Payments\Api\Data\CardTokenInterface
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
    public function setMessage(string $message): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::MESSAGE, $message);
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
    public function setCurrentTime(string $currentTime): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::CURRENT_TIME, $currentTime);
    }

    /**
     * @inheritDoc
     */
    public function getSourceUUID(): string
    {
        return $this->getData(self::SOURCE_UUID);
    }

    /**
     * @inheritDoc
     */
    public function setSourceUUID(string $sourceUUID): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::SOURCE_UUID, $sourceUUID);
    }

    /**
     * @inheritDoc
     */
    public function getType(): ?string
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType(?string $type): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getClientUUID(): ?string
    {
        return $this->getData(self::CLIENT_UUID);
    }

    /**
     * @inheritDoc
     */
    public function setClientUUID(?string $clientUUID): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::CLIENT_UUID, $clientUUID);
    }

    /**
     * @inheritDoc
     */
    public function getBrand(): string
    {
        return $this->getData(self::BRAND);
    }

    /**
     * @inheritDoc
     */
    public function setBrand(string $brand): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::BRAND, $brand);
    }

    /**
     * @inheritDoc
     */
    public function getHolder(): string
    {
        return $this->getData(self::HOLDER);
    }

    /**
     * @inheritDoc
     */
    public function setHolder(string $holder): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::HOLDER, $holder);
    }

    /**
     * @inheritDoc
     */
    public function getBin(): string
    {
        return $this->getData(self::BIN);
    }

    /**
     * @inheritDoc
     */
    public function setBin(string $bin): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::BIN, $bin);
    }

    /**
     * @inheritDoc
     */
    public function getLast4(): string
    {
        return $this->getData(self::LAST4);
    }

    /**
     * @inheritDoc
     */
    public function setLast4(string $last4): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::LAST4, $last4);
    }

    /**
     * @inheritDoc
     */
    public function getExpireMonth(): string
    {
        return $this->getData(self::EXPIRE_MONTH);
    }

    /**
     * @inheritDoc
     */
    public function setExpireMonth(string $expireMonth): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::EXPIRE_MONTH, $expireMonth);
    }

    /**
     * @inheritDoc
     */
    public function getExpireYear(): string
    {
        return $this->getData(self::EXPIRE_YEAR);
    }

    /**
     * @inheritDoc
     */
    public function setExpireYear(string $expireYear): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::EXPIRE_YEAR, $expireYear);
    }

    /**
     * @inheritDoc
     */
    public function getAdditional(): ?string
    {
        return $this->getData(self::ADDITIONAL);
    }

    /**
     * @inheritDoc
     */
    public function setAdditional(?string $additional): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::ADDITIONAL, $additional);
    }

    /**
     * @inheritDoc
     */
    public function getValidationDate(): ?string
    {
        return $this->getData(self::VALIDATION_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setValidationDate(?string $validationDate): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::VALIDATION_DATE, $validationDate);
    }

    /**
     * @inheritDoc
     */
    public function getCreationDate(): string
    {
        return $this->getData(self::CREATION_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setCreationDate(string $creationDate): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::CREATION_DATE, $creationDate);
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
    public function setToken(string $token): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        return $this->setData(self::TOKEN, $token);
    }
}
