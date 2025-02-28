<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

class CardTokenManagement
{
    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Paynopain\Payments\Api\CardTokenRepositoryInterface $repository
     * @param \Paynopain\Payments\Api\Data\CardTokenInterfaceFactory $entityInterface
     * @param \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement $cardTokenManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\DeleteSourceUUID $deleteSourceUUID
     * @param \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\UpdateAdditional $updateAdditional
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Paynopain\Payments\Api\CardTokenRepositoryInterface $repository,
        protected \Paynopain\Payments\Api\Data\CardTokenInterfaceFactory $entityInterface,
        protected \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement $cardTokenManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\DeleteSourceUUID $deleteSourceUUID,
        protected \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\UpdateAdditional $updateAdditional,
        protected \Magento\Customer\Model\Session $customerSession,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->configHelperData = $configHelperData;
        $this->repository = $repository;
        $this->entityInterface = $entityInterface;
        $this->cardTokenManagement = $cardTokenManagement;
        $this->deleteSourceUUID = $deleteSourceUUID;
        $this->updateAdditional = $updateAdditional;
        $this->customerSession = $customerSession;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * @param \stdClass $response
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function saveEntity(\stdClass $response): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        /** @var \Paynopain\Payments\Api\Data\CardTokenInterface $cardToken */
        $cardToken = $this->entityInterface->create();

        try {
            $cardToken->setCode($response->code);
            $cardToken->setMessage($response->message);
            $cardToken->setCurrentTime($response->current_time);
            $cardToken->setCustomerEntityId((int) $response->Customer->external_id);
            $cardToken->setSourceUUID($response->Source->uuid);
            $cardToken->setType($response->Source->type);
            $cardToken->setBrand($response->Source->brand);
            $cardToken->setHolder($response->Source->holder);
            $cardToken->setBin((string) $response->Source->bin);
            $cardToken->setLast4($response->Source->last4);
            $cardToken->setExpireMonth($response->Source->expire_month);
            $cardToken->setExpireYear($response->Source->expire_year);
            $cardToken->setAdditional($response->Source->additional);
            $cardToken->setValidationDate($response->Source->validation_date);
            $cardToken->setCreationDate($response->Source->creation_date);
            $cardToken->setToken($response->Source->token);

            $this->repository->save($cardToken);
        } catch (\Exception $e) {
            $this->logger->debugLogs('CardToken::SAVE ENTITY::ERROR::' . serialize($e->getMessage()));
        }

        return $cardToken;
    }

    /**
     * @param string $additional
     * @param integer $entityId
     * @return \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\UpdateAdditional
     */
    public function generateUpdateAdditionalRequest(
        string $additional,
        int $entityId
    ): \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\UpdateAdditional {
        $this->checkSourceUUIDCustomerEntity($entityId);

        /** @var \Paynopain\Payments\Api\Data\CardTokenInterface $sourceUUID */
        $sourceUUID = $this->getByEntityId($entityId);

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->source_uuid = $sourceUUID->getSourceUUID();
        $object->requestObject->additional = $additional;

        $this->updateAdditional->setEnvironment($this->configHelperData->getEnvironment());
        $this->updateAdditional->setAuthorization($object->authorization);
        $this->updateAdditional->setRequestObject($object->requestObject);

        $this->logger->debugLogs('CardToken::UPDATE ADDITIONAL Request::Environment::' . $this->updateAdditional->getEnvironment());
        $this->logger->debugLogs('CardToken::UPDATE ADDITIONAL Request::HTTP-METHOD::' . $this->updateAdditional->getHTTPMethod());
        $this->logger->debugLogs('CardToken::UPDATE ADDITIONAL Request::EndPoint::' . $this->updateAdditional->getEndPoint());
        $this->logger->debugLogs('CardToken::UPDATE ADDITIONAL Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('CardToken::UPDATE ADDITIONAL Request::' . json_encode($object));

        return $this->updateAdditional;
    }

    /**
     * @param integer $entityId
     * @return \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\DeleteSourceUUID
     */
    public function generateDeleteCardTokenRequest(
        int $entityId,
    ): \Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement\DeleteSourceUUID {
        $this->checkSourceUUIDCustomerEntity($entityId);

        $sourceUUID = $this->getByEntityId($entityId);

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->card_uuid = $sourceUUID->getSourceUUID();
        $object->requestObject->customer_external_id = $this->customerSession->getId();

        $this->deleteSourceUUID->setEnvironment($this->configHelperData->getEnvironment());
        $this->deleteSourceUUID->setAuthorization($object->authorization);
        $this->deleteSourceUUID->setRequestObject($object->requestObject);

        $this->logger->debugLogs('CardToken::DELETE Request::Environment::' . $this->deleteSourceUUID->getEnvironment());
        $this->logger->debugLogs('CardToken::DELETE Request::HTTP-METHOD::' . $this->deleteSourceUUID->getHTTPMethod());
        $this->logger->debugLogs('CardToken::DELETE Request::EndPoint::' . $this->deleteSourceUUID->getEndPoint());
        $this->logger->debugLogs('CardToken::DELETE Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('CardToken::DELETE Request::' . json_encode($object));

        return $this->deleteSourceUUID;
    }

    /**
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface[]
     */
    public function getByCustomerEntityId()
    {
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\CardTokenInterface::CUSTOMER_ENTITY_ID,
            $this->customerSession->getId()
        );

        return $this->repository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * @param string $sourceUUID
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function getBySourceUUID(string $sourceUUID): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\CardTokenInterface::SOURCE_UUID,
            $sourceUUID
        );

        $result = $this->repository->getList($this->searchCriteriaBuilder->create());

        if (!$result->getTotalCount()) {
            return $this->entityInterface->create();
        }

        return current($result->getItems());
    }

    /**
     * @param string $entityId
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     */
    public function getByEntityId(int $entityId): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\CardTokenInterface::ENTITY_ID,
            $entityId
        );

        $result = $this->repository->getList($this->searchCriteriaBuilder->create());

        if (!$result->getTotalCount()) {
            return $this->entityInterface->create();
        }

        return current($result->getItems());
    }

    /**
     * @param integer $entityId
     * @return boolean
     */
    public function deleteById(int $entityId): bool
    {
        return $this->repository->deleteById($entityId);
    }

    /**
     * @param integer $entityId
     * @return boolean
     */
    public function updateAdditional(string $additional, int $entityId): bool
    {
        $cardToken = $this->getByEntityId($entityId);
        $cardToken->setAdditional($additional);
        $this->repository->save($cardToken);

        return true;
    }

    /**
     * @param string $sourceUUID
     * @return boolean
     */
    public function sourceUUIDALreadyExists(string $sourceUUID): bool
    {
        $storedCard = $this->getBySourceUUID($sourceUUID);
        if ($storedCard->getId() !== null) {
            return true;
        }

        return false;
    }

    /**
     * @param integer $entityId
     * @return void
     */
    private function checkSourceUUIDCustomerEntity(int $entityId): bool
    {
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\CardTokenInterface::ENTITY_ID,
            $entityId
        );
        $this->searchCriteriaBuilder->addFilter(
            \Paynopain\Payments\Api\Data\CardTokenInterface::CUSTOMER_ENTITY_ID,
            $this->customerSession->getId()
        );

        if (!$this->repository->getList($this->searchCriteriaBuilder->create())->getTotalCount()) {
            return false;
        }

        return true;
    }
}
