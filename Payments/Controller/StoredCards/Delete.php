<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\StoredCards;

use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete implements HttpPostActionInterface
{
    /**
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement,
        protected \Magento\Framework\Message\ManagerInterface $messageManagerInterface,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        protected \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->executeRestRequest = $executeRestRequest;
        $this->cardTokenManagement = $cardTokenManagement;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->requestInterface = $requestInterface;
        $this->redirectFactory = $redirectFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $entityId = (int) $this->requestInterface->getParam('entity_id');
            $requestObject = $this->cardTokenManagement->generateDeleteCardTokenRequest($entityId);
            $response = $this->executeRestRequest->executeRequest($requestObject);

            $this->logger->debugLogs('StoredCards::DELETE::Response::' . json_encode($response));

            $this->cardTokenManagement->deleteById($entityId);

            $this->messageManagerInterface->addSuccess(__('Card deleted!'));
        } catch (\Exception $e) {
            $this->logger->debugLogs('StoredCards::DELETE::ERROR::' . serialize($e->getMessage()));
            $this->messageManagerInterface->addErrorMessage(__('There was an error deleting the card. Please contact administrator.'));
        }

        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setPath('*/*/listview');

        return $resultRedirect;
    }
}
