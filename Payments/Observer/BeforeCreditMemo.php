<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;

class BeforeCreditMemo implements ObserverInterface
{
    /**
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        $creditMemo = $observer->getEvent()->getData('creditmemo');
        $order = $creditMemo->getOrder();
        $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();

        switch ($paymentMethodCode) {
            case \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE:
                $creditMemo->setState(9996);
            default:
                //...
        }

        return;
    }
}
