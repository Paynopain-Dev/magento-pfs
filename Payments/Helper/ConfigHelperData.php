<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class ConfigHelperData extends AbstractHelper
{
    /** @var string */
    const DEFAULT_PAYMENT_METHOD_CODE = \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE;

    /** @var string */
    protected $paymentMethodCode = \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE;

    /**
     * 1
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Payment\Model\Method\Factory $paymentMethodFactory
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        protected \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        protected \Magento\Payment\Model\Method\Factory $paymentMethodFactory,
        protected \Magento\Framework\App\Helper\Context $context
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->encryptor = $encryptor;
        $this->paymentMethodFactory = $paymentMethodFactory;

        parent::__construct($context);
    }

    /**
     * @param string $methodCode
     * @return self
     */
    public function setPaymentMethodCode(string $methodCode): self
    {
        $this->paymentMethodCode = $methodCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/environment',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getAuthorization(): string
    {
        return 'Bearer ' . $this->encryptor->decrypt($this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/api_key',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * @return string
     */
    public function getSignature(): string
    {
        return $this->encryptor->decrypt($this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/signature',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * @return string
     */
    public function getService(): string
    {
        return $this->encryptor->decrypt($this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/service',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * @return string
     */
    public function getOperative(): string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/payment_action',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function getSecure(): int
    {
        return (int) $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/secure',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string|null
     */
    public function getCheckoutUUID(): ?string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . $this->paymentMethodCode . '/checkout_uuid',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getOrderStatusForAuthorized(): string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . $this->paymentMethodCode . '/order_status_authorized',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getOrderCaptureStatus(): string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . $this->paymentMethodCode . '/order_capture',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function getCreateInvoice(): int
    {
        return (int) $this->scopeConfigInterface->getValue(
            'payment/' . $this->paymentMethodCode . '/generate_invoice',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getDebugLogs(): int
    {
        return (int) $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/debug_logs',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return integer
     */
    public function getKeepQuoteOnPaymentError(): int
    {
        return (int) $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/keep_quote_on_error',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getIntegrationType(): string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/integration_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getInstructions(): string
    {
        return $this->scopeConfigInterface->getValue(
            'payment/' . $this->paymentMethodCode . '/instructions',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getKeepQuoteOnPaymentErrorMessage(): string
    {
        $message = $this->scopeConfigInterface->getValue(
            'payment/' . self::DEFAULT_PAYMENT_METHOD_CODE . '/keep_quote_on_error_message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $methods = [];
        foreach ($this->scopeConfigInterface->getValue('payment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, null) as $code => $data) {
            if (isset($data['active'], $data['model']) && (bool) $data['active']) {
                $methodModel = $this->paymentMethodFactory->create($data['model']);
                $methodModel->setStore(null);
                if (str_contains($code, 'paynopain')) {
                    if ($methodModel->getConfigData('active', null)) {
                        $methods[] = $methodModel->getConfigData('title');
                    }
                }
            }
        }

        return str_replace('{{AVAILABLE_METHODS}}', implode(', ', $methods), $message);
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->scopeConfigInterface->getValue(
            'web/secure/base_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
