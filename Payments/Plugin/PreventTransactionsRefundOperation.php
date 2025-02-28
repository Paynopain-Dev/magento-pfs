<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Plugin;

class PreventTransactionsRefundOperation
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo\RefundOperation $subject
     * @param [type] $creditmemo
     * @param [type] $order
     * @param boolean $online
     * @return void
     */
    public function beforeExecute(
        \Magento\Sales\Model\Order\Creditmemo\RefundOperation $subject,
        $creditmemo,
        $order,
        $online = false
    ) {
        $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();
        switch ($paymentMethodCode) {
            case \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE:
                $creditmemo->setDoTransaction(false);
                $online = false;
            default:
                //...
        }

        return [$creditmemo, $order, $online];
    }
}
