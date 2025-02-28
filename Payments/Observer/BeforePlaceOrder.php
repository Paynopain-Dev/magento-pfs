<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;

class BeforePlaceOrder implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();

        switch ($paymentMethodCode) {
            case \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE:
                $order->setCanSendNewEmailFlag(false);
            default:
                //...
        }

        return;
    }
}
