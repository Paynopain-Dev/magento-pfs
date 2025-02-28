<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;

class BeforeSalesOrderSave implements ObserverInterface
{
    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\RefundManagement $refundManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\RefundManagement $refundManagement,
        protected \Magento\Framework\Message\ManagerInterface $messageManagerInterface,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->configHelperData = $configHelperData;
        $this->generatedPaymentOrderManagement = $generatedPaymentOrderManagement;
        $this->executeRestRequest = $executeRestRequest;
        $this->refundManagement = $refundManagement;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderStatus = $order->getStatus();
        $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();
        $this->configHelperData->setPaymentMethodCode($paymentMethodCode);

        if ($this->configHelperData->getOperative() == 'DEFERRED') {
            switch ($paymentMethodCode) {
                case \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE:
                    if ($orderStatus == $this->configHelperData->getOrderCaptureStatus()) {
                        $order->setStatus('payment_review');
                    }
                default:
                    //...
            }
        }

        return;
    }
}
