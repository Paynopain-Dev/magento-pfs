<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI;

class BaseRequest implements RequestInterface
{
    /** @var string */
    protected string $httpMethod = 'POST';

    /** @var string */
    protected string $endPoint = 'payment';

    /** @var string */
    protected string $environment = \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest::SANDBOX_ENVIRONMENT;

    /**
     * @param \stdClass $requestObject
     * @param string|null $authorization
     * @param \Magento\Sales\Api\Data\OrderInterface|null $orderInterface
     */
    public function __construct(
        protected \stdClass $requestObject,
        protected ?string $authorization = null
    ) {
        $this->requestObject = $requestObject;
        $this->authorization = $authorization;
    }

    /**
     * @param string $environment
     * @return self
     */
    public function setEnvironment(string $environment): self
    {
        if ($environment == 'REAL') {
            $this->environment = \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest::REAL_ENVIRONMENT;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @return string
     */
    public function getHTTPMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @param string $httpMethod
     * @return self
     */
    public function setHTTPMethod(string $httpMethod): self
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    /**
     * @param integer $endPoint
     * @return self
     */
    public function setEndPoint(string $endPoint): self
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorization(): string
    {
        return $this->authorization;
    }

    /**
     * @param string $authorization
     * @return self
     */
    public function setAuthorization(string $authorization): self
    {
        $this->authorization = $authorization;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getRequestObject(): \stdClass
    {
        return $this->requestObject;
    }

    /**
     * @param \stdClass $requestObject
     * @return self
     */
    public function setRequestObject(\stdClass $requestObject): self
    {
        $this->requestObject = $requestObject;

        return $this;
    }

}
