<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI;

interface RequestInterface
{
    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @param string $environment
     * @return self
     */
    public function setEnvironment(string $environment): self;

    /**
     * @return string
     */
    public function getHTTPMethod(): string;

    /**
     * @return string
     */
    public function setHTTPMethod(string $httpMethod): self;

    /**
     * @return string
     */
    public function getEndPoint(): string;

    /**
     * @return string
     */
    public function setEndPoint(string $endPoint): self;

    /**
     * @return string
     */
    public function getAuthorization(): string;

    /**
     * @param string $apiKey
     * @return self
     */
    public function setAuthorization(string $apiKey): self;

    /**
     * @return \stdClass
     */
    public function getRequestObject(): \stdClass;

    /**
     * @param string $requestObject
     * @return self
     */
    public function setRequestObject(\stdClass $requestObject): self;
}
