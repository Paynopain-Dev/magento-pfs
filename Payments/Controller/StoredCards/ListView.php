<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\StoredCards;

use Magento\Framework\App\Action\HttpGetActionInterface;

class ListView extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Paynopain\Payments\Model\ClientManagement $clientManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest
     * @param \Paynopain\Payments\Model\CustomerAddressExtAttrUUID\SaveHandler $paynopainAddressUUIDSaveHandler
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Paynopain\Payments\Model\ClientManagement $clientManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\ExecuteRestRequest $executeRestRequest,
        protected \Paynopain\Payments\Model\CustomerAddressExtAttrUUID\SaveHandler $paynopainAddressUUIDSaveHandler,
        protected \Magento\Customer\Model\Session $customerSession,
        protected \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        public \Magento\Framework\App\Action\Context $context,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->configHelperData = $configHelperData;
        $this->clientManagement = $clientManagement;
        $this->executeRestRequest = $executeRestRequest;
        $this->paynopainAddressUUIDSaveHandler = $paynopainAddressUUIDSaveHandler;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }

        return parent::dispatch($request);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $resultPage = $this->resultPageFactory->create();
        $createProfile = false;
        $createAddress = false;

        /** Client create */
        try {
            $requestObject = $this->clientManagement->generateCreateClientRequest();
            $response = $this->executeRestRequest->executeRequest($requestObject);

            $this->logger->debugLogs('Client::Create::RESPONSE::' . json_encode($response));

            if (isset($response->Customer->token)) {
                $addNewBlock = $resultPage->getLayout()->getBlock('storedcards.add-new');
                $addNewBlock->setToken(
                    $response->Customer->token
                );

                $addNewBlock->setEnvironment(
                    $this->configHelperData->getEnvironment()
                );
            }
        } catch (\Exception $e) {
            $this->logger->debugLogs('Client::CREATE::ERROR::' . serialize($e->getMessage()));
        }

        /** Obtain profile */
        try {
            $requestObject = $this->clientManagement->generateObtainProfileRequest();
            $response = $this->executeRestRequest->executeRequest($requestObject);

            $this->logger->debugLogs('Client::OBTAIN PROFILE::RESPONSE::' . json_encode($response));
        } catch (\Exception $e) {
            $this->logger->debugLogs('Client::OBTAIN PROFILE::ERROR::' . serialize($e->getMessage()));
            $createProfile = true;
        }

        /** Create profile */
        if ($createProfile) {
            try {
                $requestObject = $this->clientManagement->generateCreateProfileRequest();
                $response = $this->executeRestRequest->executeRequest($requestObject);

                $this->logger->debugLogs('Client::CREATE PROFILE::RESPONSE::' . json_encode($response));
            } catch (\Exception $e) {
                $this->logger->debugLogs('Client::CREATE PROFILE::ERROR::' . serialize($e->getMessage()));
            }
        }

        /** Obtain Address */
        try {
            $requestObject = $this->clientManagement->generateObtainAddressRequest();
            $response = $this->executeRestRequest->executeRequest($requestObject);

            $this->logger->debugLogs('Client::OBTAIN ADDRESS::RESPONSE::' . json_encode($response));
        } catch (\Exception $e) {
            $this->logger->debugLogs('Client::OBTAIN ADDRESS::ERROR::' . serialize($e->getMessage()));
            $createAddress = true;
        }

        /** Create Address */
        if ($createAddress) {
            try {
                $requestObject = $this->clientManagement->generateCreateAddressRequest();
                $response = $this->executeRestRequest->executeRequest($requestObject);

                $this->logger->debugLogs('Client::CREATE ADDRESS::RESPONSE::' . json_encode($response));

                if (isset($response->customer_address->uuid)) {
                    $this->paynopainAddressUUIDSaveHandler->setUUID($requestObject->getCustomerAddressId(), $response->customer_address->uuid);
                }
            } catch (\Exception $e) {
                $this->logger->debugLogs('Client::CREATE ADDRESS::ERROR::' . serialize($e->getMessage()));
            }
        }

        return $resultPage;
    }
}
