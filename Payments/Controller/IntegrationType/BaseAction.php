<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\IntegrationType;

class BaseAction
{
    /** @var string */
    private $orderIncrementId;

    /** @var bool */
    private $challengeRequired = false;

    /** @var string */
    private $url3ds;

    /** @var bool */
    private $directToSuccess = false;

    /**
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\GeneratedPaymentOrderManagement $generatedPaymentOrderManagement,
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        protected \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Magento\Framework\Message\ManagerInterface $messageManagerInterface,
        protected \Magento\Framework\Controller\ResultFactory $resultFactory,
        protected \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        protected \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->executeRestRequest = $executeRestRequest;
        $this->generatedPaymentOrderManagement = $generatedPaymentOrderManagement;
        $this->configHelperData = $configHelperData;
        $this->requestInterface = $requestInterface;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->resultFactory = $resultFactory;
        $this->historyFactory = $historyFactory;
        $this->orderManagementInterface = $orderManagementInterface;
        $this->logger = $logger;
    }

    /**
     * @param string $incrementId
     * @return \stdClass | \Magento\Framework\Controller\ResultFactory
     */
    public function baseRequest(string $incrementId)
    {
        try {
            $criteria = $this->searchCriteriaBuilder
                ->addFilter(\Magento\Sales\Api\Data\OrderInterface::INCREMENT_ID, $incrementId)
                ->create();
            $orders = $this->orderRepositoryInterface->getList($criteria);
            $order = current($orders->getItems());

            $order->setStatus(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $order->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
            $this->setIncrementId($order->getIncrementId());

            $this->orderRepositoryInterface->save($order);

            $saveCard = $this->requestInterface->getParam('saveCard');
            $sourceUUID = $this->requestInterface->getParam('suuid');

            $paymentOrderRequestObject = $this->generatedPaymentOrderManagement->generatePaymentOrderRequest($order, (bool) $saveCard, $sourceUUID);
            $paymentOrderResponse = $this->executeRestRequest->executeRequest($paymentOrderRequestObject);
            $this->generatedPaymentOrderManagement->saveEntity($order, $paymentOrderResponse);

            $this->logger->debugLogs('GeneratePaymentOrder::GENERATE PAYMENT ORDER Request::Response::' . json_encode($paymentOrderResponse));

            $historyComment = $this->historyFactory->create()
                ->setStatus($order->getStatus())
                ->setComment(__('Generated payment order uuid %1', $paymentOrderResponse->order->uuid))
                ->setEntityName('order');
            $this->orderManagementInterface->addComment($order->getId(), $historyComment);

            if (!$this->configHelperData->getSecure() && $sourceUUID !== null) {
                $byWebServiceRequestObject = $this->generatedPaymentOrderManagement->generateByWebServiceRequest($order, $sourceUUID);
                $byWebServiceResponse = $this->executeRestRequest->executeRequest($byWebServiceRequestObject);

                $this->logger->debugLogs('GeneratePaymentOrder::GENERATE BY WEB SERVICE Request::Response::' . json_encode($byWebServiceResponse));

                if (isset($byWebServiceResponse->code)) {
                    $code = $byWebServiceResponse->code;

                    if ($code == 200) {
                        $this->setDirectToSuccess(true);
                    } elseif ($code == 303) {
                        $this->setChallengeRequired(true);
                        $this->set3dsUrl($byWebServiceRequestObject->details);
                    }
                }
            }

            return $paymentOrderResponse;
        } catch (\Exception $e) {
            $this->logger->debugLogs('GeneratePaymentOrder::ERROR::' . serialize($e->getMessage()));
        }

        $this->messageManagerInterface->addError(__('There was an error in the request. Please try again later or contact administrator.'));
        $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirect->setUrl('/checkout/cart');

        return $redirect;
    }

    /**
     *
     * @return string
     */
    public function getIncrementId(): string
    {
        return $this->orderIncrementId;
    }

    /**
     *
     * @param string $orderIncrementId
     * @return self
     */
    public function setIncrementId(string $orderIncrementId): self
    {
        $this->orderIncrementId = $orderIncrementId;

        return $this;
    }

    /**
     *
     * @return boolean|null
     */
    public function getChallengeRequired(): ?bool
    {
        return $this->challengeRequired;
    }

    /**
     *
     * @param boolean $challengeRequired
     * @return self
     */
    public function setChallengeRequired(bool $challengeRequired): self
    {
        $this->challengeRequired = $challengeRequired;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function get3dsUrl(): ?string
    {
        return $this->url3ds;
    }

    /**
     *
     * @param string $url3ds
     * @return self
     */
    public function set3dsUrl(string $url3ds): self
    {
        $this->url3ds = $url3ds;

        return $this;
    }

    /**
     *
     * @return boolean|null
     */
    public function getDirectToSuccess(): ?bool
    {
        return $this->directToSuccess;
    }

    /**
     * Undocumented function
     *
     * @param boolean $directToSuccess
     * @return self
     */
    public function setDirectToSuccess(bool $directToSuccess): self
    {
        $this->directToSuccess = $directToSuccess;

        return $this;
    }
}
