<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\Response;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;

class Process implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /** @var bool */
    private $create_invoice = false;

    /** @var bool */
    private $invoice_created = false;

    /** @var bool */
    private $sendEmail = false;

    /** @var array */
    private $transactions_created = [];

    /** @var string  */
    private $parentTransactionUUID = '';

    /** @var \Magento\Sales\Api\Data\OrderInterface */
    private $salesOrderObject;

    /** @var ?string */
    private $final_order_status;

    /** @var ?string */
    private $final_order_state;

    /** @var string */
    private $transactionType;

    /** @var string */
    private $transactionMessage;

    /**
     * @param \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement
     * @param \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Sales\Api\Data\TransactionSearchResultInterfaceFactory $transactionSearchResultInterfaceFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializerInterface
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilderInterface
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Framework\App\Response\Http $http
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement,
        protected \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement,
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        protected \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepositoryInterface,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Magento\Framework\Serialize\SerializerInterface $serializerInterface,
        protected \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        protected \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        protected \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilderInterface,
        protected \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        protected \Magento\Framework\App\Response\Http $http,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->generatedPaymentOrderManagement = $generatedPaymentOrderManagement;
        $this->cardTokenManagement = $cardTokenManagement;
        $this->configHelperData = $configHelperData;
        $this->orderManagementInterface = $orderManagementInterface;
        $this->requestInterface = $requestInterface;
        $this->transactionRepositoryInterface = $transactionRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->serializerInterface = $serializerInterface;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->transactionBuilderInterface = $transactionBuilderInterface;
        $this->historyFactory = $historyFactory;
        $this->http = $http;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        $ipn = $this->serializerInterface->unserialize($this->requestInterface->getContent());
        $ipnOrderStatus = $ipn['order']['status'];
        $ipnOrderUUID = $ipn['order']['uuid'];
        $ipnAuthorizationTransactionUUID = '';

        $this->salesOrderObject = $this->generatedPaymentOrderManagement->getSalesOrderEntityIdByOrderUUID($ipnOrderUUID);
        $salesOrderId = $this->salesOrderObject->getId();

        $this->setOrderStatus($ipnOrderStatus);

        //Search for the transaction UUID of "AUTHORIZATION"
        foreach ($ipn['order']['transactions'] as $transaction) {
            if ($transaction['operative'] == 'AUTHORIZATION' || $transaction['operative'] == 'CONFIRMATION') {
                $ipnAuthorizationTransactionUUID = $transaction['uuid'];

                $this->logger->debugLogs('IPN::OrderID::' . $salesOrderId . '::AUTHORIZATION or CONFIRMATION transactions ID ' . $ipnAuthorizationTransactionUUID);
                break; //Only one
            }
        }

        foreach ($ipn['order']['transactions'] as $transaction) {
            $transactionOperative = $transaction['operative'];
            $transactionStatus = $transaction['status'];
            $paymentTransactionId = $transaction['uuid'];
            $transactionAmount = $transaction['amount'];

            $this->setTransactionOperativeType($transactionOperative, $transactionStatus, $paymentTransactionId, $ipnAuthorizationTransactionUUID, $transactionAmount);

            $this->searchCriteriaBuilder->addFilter('txn_id', $paymentTransactionId);
            $transactionAlreadyCreated = $this->transactionRepositoryInterface->getList(
                $this->searchCriteriaBuilder->create()
            );
            $this->createTransaction($paymentTransactionId, $ipn);
            $this->transactions_created[$transactionOperative . '-' . $paymentTransactionId] = [
                'type' => $this->transactionType,
                'invoice_created' => false,
                'source_saved' => [
                    'status' => false,
                    'message' => 'is_saved: false',
                ],
            ];

            if ($this->configHelperData->getCreateInvoice() && $this->create_invoice && $this->salesOrderObject->canInvoice()) {
                if ($ipnAuthorizationTransactionUUID != '') {
                    $this->createInvoice($ipnAuthorizationTransactionUUID);
                } else {
                    $this->createInvoice($paymentTransactionId);
                }

                $this->transactions_created[$transactionOperative . '-' . $paymentTransactionId]['invoice_created'] = $this->invoice_created;
                $this->invoice_created = false;
            }

            if (isset($transaction['source'])) {
                $source = $transaction['source'];

                if ($source['object'] == 'CARD' && $source['is_saved'] && $this->salesOrderObject->getCustomerId()) {
                    if (!$this->cardTokenManagement->sourceUUIDALreadyExists($source['uuid'])) {
                        $cardSourceStd = new \stdClass();
                        $cardSourceStd->code = 200;
                        $cardSourceStd->message = 'OK';
                        $cardSourceStd->current_time = $ipn['order']['created'];

                        $cardSourceStd->Source = new \stdClass();
                        $cardSourceStd->Source->uuid = $source['uuid'];
                        $cardSourceStd->Source->type = $source['type'];
                        $cardSourceStd->Source->brand = $source['brand'];
                        $cardSourceStd->Source->holder = $source['holder'];
                        $cardSourceStd->Source->bin = $source['bin'];
                        $cardSourceStd->Source->last4 = $source['last4'];
                        $cardSourceStd->Source->expire_month = $source['expire_month'];
                        $cardSourceStd->Source->expire_year = $source['expire_year'];
                        $cardSourceStd->Source->additional = $source['additional'];
                        $cardSourceStd->Source->validation_date = $source['validation_date'];
                        $cardSourceStd->Source->creation_date = $source['creation_date'];
                        $cardSourceStd->Source->token = $source['token'];

                        $cardSourceStd->Customer = new \stdClass();
                        $cardSourceStd->Customer->external_id = $this->salesOrderObject->getCustomerId();

                        $this->cardTokenManagement->saveEntity($cardSourceStd);

                        $this->transactions_created[$transactionOperative . '-' . $paymentTransactionId]['source_saved']['status'] = true;
                        $this->transactions_created[$transactionOperative . '-' . $paymentTransactionId]['source_saved']['message'] = 'is_saved: true';
                    } else {
                        $this->transactions_created[$transactionOperative . '-' . $paymentTransactionId]['source_saved']['status'] = false;
                        $this->transactions_created[$transactionOperative . '-' . $paymentTransactionId]['source_saved']['message'] = 'Already have this one registered';
                    }
                }
            }
        }

        if (!$this->salesOrderObject->getEmailSent() && $this->sendEmail) {
            $this->orderManagementInterface->notify($salesOrderId);
        }

        $historyComment = $this->historyFactory->create()
            ->setStatus($this->salesOrderObject->getStatus())
            ->setComment(__('IPN completed with Order UUID %1', $ipnOrderUUID))
            ->setEntityName('order');
        $this->orderManagementInterface->addComment($this->salesOrderObject->getId(), $historyComment);

        $this->salesOrderObject->save();

        $this->logger->debugLogs('IPN:OrderID::' . $salesOrderId . '::Returned data ' . serialize([
            'success' => true,
            'final_order_status' => $this->salesOrderObject->getStatus(),
            'email_sent' => $this->salesOrderObject->getEmailSent() ? true : false,
            'transactions_created' => $this->transactions_created,
        ]));

        return $result->setData(
            [
                'success' => true,
                'final_order_status' => $this->salesOrderObject->getStatus(),
                'email_sent' => $this->salesOrderObject->getEmailSent() ? true : false,
                'transactions_created' => $this->transactions_created,
            ]
        );
    }

    /**
     * @param string $ipnOrderStatus
     * @return void
     */
    private function setOrderStatus(string $ipnOrderStatus): void
    {
        switch ($ipnOrderStatus) {
            case 'THREEDS_EXPIRED':
            case 'USER_CANCELLED':
            case 'EXPIRED':
            case 'BLACKLISTED':
            case 'CANCELLED':
            case 'REFUSED':
                $this->orderManagementInterface->cancel($this->salesOrderObject->getId());
                $this->final_order_status = \Magento\Sales\Model\Order::STATE_CANCELED;
                $this->final_order_state = $this->final_order_status;
                break;

            case 'FRAUD':
                $this->orderManagementInterface->hold($this->salesOrderObject->getId());
                $this->final_order_status = \Magento\Sales\Model\Order::STATE_HOLDED;
                $this->final_order_state = $this->final_order_status;
                break;

            case 'PENDING_CARD':
            case 'PENDING_PROCESSOR_RESPONSE':
            case 'PENDING_3DS_RESPONSE':
            case 'PENDING_CONFIRMATION':
                $this->final_order_status = $this->configHelperData->getOrderStatusForAuthorized();

                if ($this->final_order_status == 'pending') {
                    $this->final_order_state = 'new';
                }
            case 'PENDING_PAYMENT':
                break;

            case 'CREATED':
                break;

            case 'REFUNDED':
                break;

            case 'PARTIALLY_REFUNDED':
                $this->final_order_status = 'processing';
                $this->final_order_state = $this->final_order_status;
                break;

            case 'PARTIALLY_CONFIRMED':
                break;

            case 'REDIRECTED_TO_3DS':
                break;

            case 'AUTHENTICATION_REQUIRED':
                break;

            case 'SUCCESS':
                $this->final_order_status = $this->configHelperData->getOrderStatusForAuthorized();
                $this->final_order_state = $this->final_order_status;
                if ($this->final_order_status == 'pending') {
                    $this->final_order_state = 'new';
                }

                $this->sendEmail = true;
                break;
            default:
                throw new \Magento\Framework\Webapi\Exception(__('"%1" payment status couldn\'t be handled', $ipnOrderStatus));
        }

        if ($this->final_order_status !== null) {
            $this->salesOrderObject->setState($this->final_order_state);
            $this->salesOrderObject->setStatus($this->final_order_status);
        }
    }

    /**
     * @param string $transactionOperative
     * @param string $transactionStatus
     * @return void
     */
    private function setTransactionOperativeType(
        string $transactionOperative,
        string $transactionStatus,
        string $paymentTransactionId,
        string $ipnAuthorizationTransactionUUID,
        int $amount,
    ): void {
        switch ($transactionOperative) {
            case 'AUTHORIZATION':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE;
                $this->create_invoice = true;
                break;
            case 'DEFERRED':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH;
                $this->create_invoice = false;
                break;
            case 'REFUND':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND;
                $this->parentTransactionUUID = $ipnAuthorizationTransactionUUID;
                break;
            case 'CONFIRMATION':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE;
                $this->create_invoice = true;
                break;
            case 'CANCELLATION':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID;
                break;
            case 'PAYOUT':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT;
                break;
            case 'TRANSFER':
                $this->transactionType = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT;
                break;
            default:
                throw new \Magento\Framework\Webapi\Exception(__('"%1" transaction operative couldn\'t be handled', $transactionOperative));
        }

        $this->transactionMessage = __(
            '%1 amount of %2 with status %3 and transaction UUID %4',
            $transactionOperative,
            $this->salesOrderObject->getBaseCurrency()->formatTxt($amount / 100),
            $transactionStatus,
            $paymentTransactionId
        );
    }

    /**
     * @param string $paymentTransactionId
     * @param array $ipn
     * @return void
     */
    private function createTransaction(string $paymentTransactionId, array $ipn): void
    {
        try {
            $payment = $this->salesOrderObject->getPayment();
            $payment->setLastTransId($paymentTransactionId);
            $payment->setTransactionId($paymentTransactionId);
            if ($this->parentTransactionUUID !== '') {
                $payment->setParentTransactionId($this->parentTransactionUUID);
            }

            $transactionBuilder = $this->transactionBuilderInterface;
            $transactionBuilder->setPayment($payment);
            $transactionBuilder->setOrder($this->salesOrderObject);
            $transactionBuilder->setTransactionId($paymentTransactionId);
            $transactionBuilder->setAdditionalInformation(
                [
                    \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS =>
                    $this->includedFields($ipn),
                ]
            );

            $transactionBuilder->setFailSafe(true);
            $transaction = $transactionBuilder->build($this->transactionType);

            switch ($this->transactionType) {
                case \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH:
                    $transaction->setIsClosed(false);
                    break;
                case \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND:
                    $transaction->setIsClosed(true);
                    break;
                case \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT:
                    $transaction->setIsClosed(true);
                    break;
                case \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE:
                case \Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID:
                    $transaction->setIsClosed(true);
                    break;
                default:
                    $transaction->setIsClosed(true);
            }

            $historyComment = $this->historyFactory->create()
                ->setStatus($this->salesOrderObject->getStatus())
                ->setComment($this->transactionMessage)
                ->setEntityName('order');
            $this->orderManagementInterface->addComment($this->salesOrderObject->getId(), $historyComment);

            $this->salesOrderObject->setPayment($payment);

            $transaction->save();
        } catch (\Exception $e) {
            $this->logger->debugLogs('IPN::ERROR::CreateTransaction::' . $e->getMessage());

            throw new \Magento\Framework\Exception\LocalizedException(__('Error creating transaction'));
        }
    }

    /**
     * @param string $paymentTransactionId
     * @return void
     */
    private function createInvoice(string $paymentTransactionId): void
    {
        $invoice = $this->salesOrderObject->prepareInvoice();
        $invoice->getOrder()->setIsInProcess(true);

        $invoice->setTransactionId($paymentTransactionId);
        $invoice->register()->pay()->save();

        $message = __(
            'Invoiced amount of %1 Transaction ID: %2',
            $this->salesOrderObject->getBaseCurrency()->formatTxt($this->salesOrderObject->getGrandTotal()),
            $paymentTransactionId
        );

        $historyComment = $this->historyFactory->create()
            ->setStatus($this->salesOrderObject->getStatus())
            ->setComment($message)
            ->setEntityName('order');
        $this->orderManagementInterface->addComment($this->salesOrderObject->getId(), $historyComment);

        $this->invoice_created = true;
    }

    /**
     * @param array $ipn
     * @return array
     */
    private function includedFields(array $ipn): array
    {
        $excludedFields = [
            'threeds_data',
            'transactions',
        ];

        $includedFields = [];
        if ($this->configHelperData->getDebugLogs()) {
            $includedFields['more_details'] = __('For more details, please see logs file.');
        }

        foreach ($ipn['order'] as $key => $value) {
            if (!in_array($key, $excludedFields)) {
                $includedFields[$key] = $value;
            }
        }
        $includedFields['client'] = $ipn['client']['uuid'];
        $includedFields['validation_hash'] = $ipn['validation_hash'];

        return $includedFields;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\Request\InvalidRequestException|null
     */
    public function createCsrfValidationException(\Magento\Framework\App\RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return boolean|null
     */
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return true;
    }
}
