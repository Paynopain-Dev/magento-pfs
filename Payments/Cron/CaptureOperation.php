<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Cron;

class CaptureOperation
{
    /**
     * @param \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->generatedPaymentOrderManagement = $generatedPaymentOrderManagement;
        $this->executeRestRequest = $executeRestRequest;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'state',
            'processing',
        )->addFilter(
            'status',
            'payment_review'
        )->setPageSize(5)->create();

        $paynopainOrderToCapture = $this->orderRepositoryInterface->getList($searchCriteria)->getItems();

        foreach ($paynopainOrderToCapture as $salesOrderObject) {
            $paymentMethodCode = $salesOrderObject->getPayment()->getMethodInstance()->getCode();

            switch ($paymentMethodCode) {
                case \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE:
                    try {
                        $requestObject = $this->generatedPaymentOrderManagement->generateCaptureConfirmationRequest($salesOrderObject);
                        $response = $this->executeRestRequest->executeRequest($requestObject);

                        $this->logger->debugLogs('Cron::CAPTURE Operation::Response::' . json_encode($response));
                    } catch (\Exception $e) {
                        $this->logger->debugLogs('Cron::CAPTURE Operation::ERROR::' . serialize($e->getMessage()));
                    }
                default:
                    //...
            }
        }

        return;
    }
}
