<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;

class BeforeCustomerAddressDelete implements ObserverInterface
{
    /**
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\ClientManagement $clientManagement
     * @param \Paynopain\Payments\Model\CustomerAddressExtAttrUUID\ReadHandler $readHandler
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\ClientManagement $clientManagement,
        protected \Paynopain\Payments\Model\CustomerAddressExtAttrUUID\ReadHandler $readHandler,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->executeRestRequest = $executeRestRequest;
        $this->clientManagement = $clientManagement;
        $this->readHandler = $readHandler;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        try {
            $customerAddress = $observer->getEvent()->getData('customer_address');
            $uuid = $this->readHandler->getUUID((int) $customerAddress->getId());

            $requestObject = $this->clientManagement->generateDeleteAddressRequest($uuid);
            $response = $this->executeRestRequest->executeRequest($requestObject);

            $this->logger->debugLogs('Client::DELETE ADDRESS::RESPONSE::' . json_encode($response));
        } catch (\Exception $e) {
            $this->logger->debugLogs('Client::DELETE ADDRESS::ERROR::' . serialize($e->getMessage()));
        }

        return;
    }
}
