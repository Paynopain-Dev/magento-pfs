<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\Response;

use Magento\Framework\App\Action\HttpGetActionInterface;

class Ko implements HttpGetActionInterface
{
    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        protected \Magento\Framework\Message\ManagerInterface $messageManagerInterface,
        protected \Magento\Sales\Api\OrderRepositoryInterface $orderRepositoryInterface,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        protected \Magento\Checkout\Model\Session $checkoutSession,
        protected \Magento\Framework\Controller\ResultFactory $resultFactory,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->configHelperData = $configHelperData;
        $this->requestInterface = $requestInterface;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->resultFactory = $resultFactory;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $soe_increment_id = $this->requestInterface->getParam('soe_increment_id');
        if ($soe_increment_id !== null && $this->configHelperData->getKeepQuoteOnPaymentError()) {
            $this->messageManagerInterface->addWarning($this->configHelperData->getKeepQuoteOnPaymentErrorMessage());

            $this->searchCriteriaBuilder->addFilter(
                \Magento\Sales\Api\Data\OrderInterface::INCREMENT_ID,
                $soe_increment_id
            );
            $searchResults = $this->orderRepositoryInterface->getList($this->searchCriteriaBuilder->create());

            /** @var \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder */
            foreach ($searchResults->getItems() as $salesOrderEntity) {
                try {
                    $quote = $this->quoteRepository->get($salesOrderEntity->getQuoteId());
                    $quote->setIsActive(1)->setReservedOrderId(null);
                    $this->quoteRepository->save($quote);
                    $this->checkoutSession->replaceQuote($quote)->unsLastRealOrderId();
                } catch (\Exception $e) {
                    $this->logger->debugLogs('KeepQuoteOnError::ERROR::' . serialize($e->getMessage()));
                    $this->messageManagerInterface->addError(__('We are sorry, cannot retrieve cart.'));
                }
            }
        }

        return $this->resultFactory->create(
            \Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT
        )->setUrl('/checkout/cart');
    }
}
