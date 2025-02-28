<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

class RefundManagement
{
    /**
     * @param \Paynopain\Payments\Model\PaylandsAPI\Refund $refund
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\PaylandsAPI\Refund $refund,
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->refund = $refund;
        $this->configHelperData = $configHelperData;
        $this->logger = $logger;
    }

    /**
     * @param string $orderUUID
     * @param integer $totalRefunded
     * @return \Paynopain\Payments\Model\PaylandsAPI\Refund
     */
    public function generateRefundRequest(
        string $orderUUID,
        int $totalRefunded
    ): \Paynopain\Payments\Model\PaylandsAPI\Refund {
        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->order_uuid = $orderUUID;
        $object->requestObject->amount = $totalRefunded;

        $this->refund->setEnvironment($this->configHelperData->getEnvironment());
        $this->refund->setAuthorization($object->authorization);
        $this->refund->setRequestObject($object->requestObject);

        $this->logger->debugLogs('CardToken::REFUND Request::Environment::' . $this->refund->getEnvironment());
        $this->logger->debugLogs('CardToken::REFUND Request::HTTP-METHOD::' . $this->refund->getHTTPMethod());
        $this->logger->debugLogs('CardToken::REFUND Request::EndPoint::' . $this->refund->getEndPoint());
        $this->logger->debugLogs('CardToken::REFUND Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('CardToken::REFUND Request::' . json_encode($object));

        return $this->refund;
    }
}
