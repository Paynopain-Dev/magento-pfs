<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\Payment;

use Magento\Payment\Model\Method\Online\GatewayInterface;

class PaynopainPayment extends \Magento\Payment\Model\Method\AbstractMethod implements GatewayInterface
{
    public const METHOD_CODE = 'paynopain_payment';

    /** @var string */
    protected $_code = self::METHOD_CODE;

    /** @var bool */
    protected $_isOffline = false;

    /** @var bool */
    protected $_canAuthorize = true;

    /** @var bool */
    protected $_canCapture = true;

    /** @var bool */
    protected $_canCapturePartial = false;

    /** @var bool */
    protected $_canCaptureOnce = true;

    /** @var bool */
    protected $_canRefund = true;

    /** @var bool */
    protected $_canRefundInvoicePartial = true;

    /** @var bool */
    protected $_isGateway = true;

    /** @var bool */
    protected $_canUseInternal = false;

    /** @var bool */
    protected $_canVoid = true;

    /** @var bool */
    protected $_canReviewPayment = true;

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        protected \Magento\Framework\UrlInterface $urlBuilder,
        protected \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data
        );

        $this->urlBuilder = $urlBuilder;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * @inheritDoc
     */
    public function postRequest(
        \Magento\Framework\DataObject $request,
        \Magento\Payment\Model\Method\ConfigInterface $config
    ): \Magento\Framework\DataObject {
        // Do nothing
    }
}
