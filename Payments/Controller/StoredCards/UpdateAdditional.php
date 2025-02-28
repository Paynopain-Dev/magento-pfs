<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\StoredCards;

use Magento\Framework\App\Action\HttpPostActionInterface;

class UpdateAdditional implements HttpPostActionInterface
{
    /**
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
     * @param \Paynopain\Payments\Api\CardTokenRepositoryInterface $CardTokenRepositoryInterface
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement,
        protected \Paynopain\Payments\Api\CardTokenRepositoryInterface $CardTokenRepositoryInterface,
        protected \Magento\Framework\Message\ManagerInterface $messageManagerInterface,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        protected \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        protected \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->executeRestRequest = $executeRestRequest;
        $this->cardTokenManagement = $cardTokenManagement;
        $this->CardTokenRepositoryInterface = $CardTokenRepositoryInterface;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->requestInterface = $requestInterface;
        $this->redirectFactory = $redirectFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if ($this->formKeyValidator->validate($this->requestInterface)) {
            try {
                $additional = $this->requestInterface->getParam('additional');
                $entityId = (int) $this->requestInterface->getParam('entity_id');
                $requestObject = $this->cardTokenManagement->generateUpdateAdditionalRequest($additional, $entityId);
                $response = $this->executeRestRequest->executeRequest($requestObject);

                $this->logger->debugLogs('StoredCards::UPDATE ADDITIONAL::Response::' . json_encode($response));

                $this->cardTokenManagement->updateAdditional($additional, (int) $entityId);

                $this->messageManagerInterface->addSuccess(__('Card updated!'));
            } catch (\Exception $e) {
                $this->logger->debugLogs('StoredCards::UPDATE ADDITIONAL::ERROR::' . serialize($e->getMessage()));
                $this->messageManagerInterface->addErrorMessage(__('There was an error saving the card. Please contact administrator.'));
            }
        }

        $resultRedirect = $this->redirectFactory->create();
        $resultRedirect->setPath('*/*/listview');

        return $resultRedirect;
    }
}
