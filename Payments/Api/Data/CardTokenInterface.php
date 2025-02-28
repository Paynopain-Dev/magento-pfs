<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Api\Data;

interface CardTokenInterface
{
    /** @var string */
    const TABLE = 'paynopain_card_token';

    /** @var string */
    const ENTITY_ID = 'entity_id';

    /** @var string */
    const CUSTOMER_ENTITY_ID = 'customer_entity_id';

    /** @var string */
    const CODE = 'code';

    /** @var string */
    const MESSAGE = 'message';

    /** @var string */
    const CURRENT_TIME = 'current_time';

    /** @var string */
    const SOURCE_UUID = 'source_uuid';

    /** @var string */
    const TYPE = 'type';

    /** @var string */
    const CLIENT_UUID = 'client_uuid';

    /** @var string */
    const BRAND = 'brand';

    /** @var string */
    const HOLDER = 'holder';

    /** @var string */
    const BIN = 'bin';

    /** @var string */
    const LAST4 = 'last4';

    /** @var string */
    const EXPIRE_MONTH = 'expire_month';

    /** @var string */
    const EXPIRE_YEAR = 'expire_year';

    /** @string */
    const ADDITIONAL = 'additional';

    /** @var string */
    const VALIDATION_DATE = 'validation_date';

    /** @var string */
    const CREATION_DATE = 'creation_date';

    /** @var string */
    const TOKEN = 'token';

    /**
     * @return integer
     */
    public function getCustomerEntityId(): int;

    /**
     * @param integer $customerEntityId
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setCustomerEntityId(int $customerEntityId): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return integer
     */
    public function getCode(): int;

    /**
     * @param integer $code
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setCode(int $code): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getMessage(): string;

    /**
     * @param string $message
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setMessage(string $message): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getCurrentTime(): string;

    /**
     * @param string $currentTime
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setCurrentTime(string $currentTime): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getSourceUUID(): string;

    /**
     * @param string $sourceUUID
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setSourceUUID(string $sourceUUID): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string|null
     */
    public function getType(): ?string;

    /**
     * @param string|null $type
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setType(?string $type): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string|null
     */
    public function getClientUUID(): ?string;

    /**
     * @param string|null $clientUUID
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setClientUUID(?string $clientUUID): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getBrand(): string;

    /**
     * @param string $brand
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setBrand(string $brand): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getHolder(): string;

    /**
     * @param string $holder
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setHolder(string $holder): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getBin(): string;

    /**
     * @param string $bin
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setBin(string $bin): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getLast4(): string;

    /**
     * @param string $last4
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setLast4(string $last4): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getExpireMonth(): string;

    /**
     * @param string $expireMonth
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setExpireMonth(string $expireMonth): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getExpireYear(): string;

    /**
     * @param string $expireYear
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setExpireYear(string $expireYear): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string|null
     */
    public function getAdditional(): ?string;

    /**
     * @param string|null $additional
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setAdditional(?string $additional): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string|null
     */
    public function getValidationDate(): ?string;

    /**
     * @param string|null $validationDate
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setValidationDate(?string $validationDate): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getCreationDate(): string;

    /**
     * @param string $creationDate
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setCreationDate(string $creationDate): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * @return string
     */
    public function getToken(): string;

    /**
     * @param string $token
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function setToken(string $token): \Paynopain\Payments\Api\Data\CardTokenInterface;
}
