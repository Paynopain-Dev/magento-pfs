<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Block\IntegrationType;

use \Magento\Framework\View\Element\Template;

class Iframe extends Template
{
    /** @var array */
    private $redirectEndpoints = [
        'payment/process',
        'payment/tokenized',
    ];

    /** @var string */
    private $incrementId;

    /** @var ?string */
    private $url3ds = null;

    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param string|null $token
     * @param array $data
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        public \Magento\Framework\View\Element\Template\Context $context,
        protected ?string $token = null,
        array $data = []
    ) {
        $this->configHelperData = $configHelperData;
        $this->requestInterface = $requestInterface;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getIframeUrl(): string
    {
        if ($this->get3dsUrl() !== null) {
            return $this->get3dsUrl();
        }

        $environmentUrl = \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest::SANDBOX_ENVIRONMENT;
        if ($this->configHelperData->getEnvironment() == 'REAL') {
            $environmentUrl = \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest::REAL_ENVIRONMENT;
        }

        $endpoint = $this->redirectEndpoints[0];
        if ($this->requestInterface->getParam('tokenized')) {
            $endpoint = $this->redirectEndpoints[1];
        }

        return $environmentUrl . $endpoint . '/' . $this->getToken();
    }

    /**
     * @return string
     */
    public function getIncrementId(): string
    {
        return $this->incrementId;
    }

    /**
     * @param string $incrementId
     * @return self
     */
    public function setIncrementId(string $incrementId): self
    {
        $this->incrementId = $incrementId;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function get3dsUrl(): ?string
    {
        return $this->url3ds;
    }

    /**
     *
     * @param string $url3ds
     * @return self
     */
    public function set3dsUrl(string $url3ds): self
    {
        $this->url3ds = $url3ds;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     *
     * @param string $token
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
