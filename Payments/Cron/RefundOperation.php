<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Cron;

class RefundOperation
{
    /**
     * @param \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\RefundManagement $refundManagement
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $creditMemoRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\RefundManagement $refundManagement,
        protected \Magento\Sales\Api\CreditmemoRepositoryInterface $creditMemoRepositoryInterface,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Magento\Framework\App\ResourceConnection $resourceConnection,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->generatedPaymentOrderManagement = $generatedPaymentOrderManagement;
        $this->executeRestRequest = $executeRestRequest;
        $this->refundManagement = $refundManagement;
        $this->creditMemoRepositoryInterface = $creditMemoRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'state',
            9996,
        )->setPageSize(5)->create();

        $creditMemos = $this->creditMemoRepositoryInterface->getList($searchCriteria)->getItems();

        foreach ($creditMemos as $creditMemo) {
            $creditMemoId = $creditMemo->getId();
            $order = $creditMemo->getOrder();
            $paymentMethodCode = $order->getPayment()->getMethodInstance()->getCode();

            switch ($paymentMethodCode) {
                case \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE:
                    try {
                        $totalRefunded = intval(strval(number_format((float) str_replace(',', '.', (string) $creditMemo->getGrandTotal()), 2, '.', '') * 100));

                        /** @var \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder */
                        $generatedPaymentOrder = $this->generatedPaymentOrderManagement->getBySalesOrderEntityId((int) $order->getId());

                        $requestObject = $this->refundManagement->generateRefundRequest($generatedPaymentOrder->getOrderUUID(), $totalRefunded);
                        $response = $this->executeRestRequest->executeRequest($requestObject);

                        $this->logger->debugLogs('Cron::REFUND Operation::Response::' . json_encode($response));

                        //** Does not work */
                        //$creditMemo->setState(1);
                        //$this->creditMemoRepositoryInterface->save($creditMemo);
                        $connection = $this->resourceConnection->getConnection();
                        $salesCreditMemoTable = $connection->getTableName('sales_creditmemo');
                        $query = "UPDATE $salesCreditMemoTable SET state = 1 WHERE entity_id = $creditMemoId";
                        $connection->query($query);
                    } catch (\Exception $e) {
                        $this->logger->debugLogs('Cron::REFUND Operation::ERROR::' . serialize($e->getMessage()));
                    }
                default:
                    //...
            }
        }

        return;
    }
}
