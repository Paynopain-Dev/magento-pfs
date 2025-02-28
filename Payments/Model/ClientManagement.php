<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

class ClientManagement
{
    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Paynopain\Payments\Model\PaylandsAPI\ClientManagement $clientManagement
     * @param \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainProfile $obtainProfile
     * @param \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateProfile $createProfile
     * @param \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainAddress $obtainAddress
     * @param \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateAddress $createAddress
     * @param \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\DeleteAddress $deleteAddress
     * @param \Paynopain\Payments\Model\CustomerAddressExtAttrUUID\ReadHandler $paynopainAddressUUIDReadHandler
     * @param \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirerInterface
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Paynopain\Payments\Model\PaylandsAPI\ClientManagement $clientManagement,
        protected \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainProfile $obtainProfile,
        protected \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateProfile $createProfile,
        protected \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainAddress $obtainAddress,
        protected \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateAddress $createAddress,
        protected \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\DeleteAddress $deleteAddress,
        protected \Paynopain\Payments\Model\CustomerAddressExtAttrUUID\ReadHandler $paynopainAddressUUIDReadHandler,
        protected \Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirerInterface,
        protected \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        protected \Magento\Customer\Api\AddressRepositoryInterface $addressRepositoryInterface,
        protected \Magento\Customer\Model\Session $customerSession,
        protected \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->configHelperData = $configHelperData;
        $this->clientManagement = $clientManagement;
        $this->obtainProfile = $obtainProfile;
        $this->createProfile = $createProfile;
        $this->obtainAddress = $obtainAddress;
        $this->createAddress = $createAddress;
        $this->deleteAddress = $deleteAddress;
        $this->paynopainAddressUUIDReadHandler = $paynopainAddressUUIDReadHandler;
        $this->countryInformationAcquirerInterface = $countryInformationAcquirerInterface;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->addressRepositoryInterface = $addressRepositoryInterface;
        $this->customerSession = $customerSession;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * @return \Paynopain\Payments\Model\PaylandsAPI\ClientManagement
     */
    public function generateCreateClientRequest(): \Paynopain\Payments\Model\PaylandsAPI\ClientManagement
    {
        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->customer_ext_id = $this->customerSession->getId();

        $this->clientManagement->setEnvironment($this->configHelperData->getEnvironment());
        $this->clientManagement->setAuthorization($object->authorization);
        $this->clientManagement->setRequestObject($object->requestObject);

        $this->logger->debugLogs('Client::CREATE CLIENT Request::Environment::' . $this->clientManagement->getEnvironment());
        $this->logger->debugLogs('Client::CREATE CLIENT Request::HTTP-METHOD::' . $this->clientManagement->getHTTPMethod());
        $this->logger->debugLogs('Client::CREATE CLIENT Request::EndPoint::' . $this->clientManagement->getEndPoint());
        $this->logger->debugLogs('Client::CREATE CLIENT Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('Client::CREATE CLIENT Request::' . json_encode($object));

        return $this->clientManagement;
    }

    /**
     * @return \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainProfile
     */
    public function generateObtainProfileRequest(): \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainProfile
    {
        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $this->obtainProfile->setEnvironment($this->configHelperData->getEnvironment());
        $this->obtainProfile->setAuthorization($object->authorization);

        $endPoint = str_replace('{external_id}', $this->customerSession->getId(), $this->obtainProfile->getEndPoint());
        $this->obtainProfile->setEndPoint($endPoint);

        $this->logger->debugLogs('Client::OBTAIN PROFILE Request::Environment::' . $this->obtainProfile->getEnvironment());
        $this->logger->debugLogs('Client::OBTAIN PROFILE Request::HTTP-METHOD::' . $this->obtainProfile->getHTTPMethod());
        $this->logger->debugLogs('Client::OBTAIN PROFILE Request::EndPoint::' . $this->obtainProfile->getEndPoint());
        $this->logger->debugLogs('Client::OBTAIN PROFILE Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('Client::OBTAIN PROFILE Request::' . json_encode($object));

        return $this->obtainProfile;
    }

    /**
     * @return \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateProfile
     */
    public function generateCreateProfileRequest(): \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateProfile
    {
        $customer = $this->customerRepositoryInterface->getById($this->customerSession->getId());
        $billingAddress = $this->addressRepositoryInterface->getById($customer->getDefaultBilling());

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->external_id = $this->customerSession->getId();
        $object->requestObject->first_name = $customer->getFirstName();
        $object->requestObject->last_name = $customer->getLastName();

        $phone = new \stdClass();
        $phone->number = $billingAddress->getTelephone();
        $object->requestObject->phone = $phone;

        $this->createProfile->setEnvironment($this->configHelperData->getEnvironment());
        $this->createProfile->setAuthorization($object->authorization);
        $this->createProfile->setRequestObject($object->requestObject);

        $this->logger->debugLogs('Client::CREATE PROFILE Request::Environment::' . $this->createProfile->getEnvironment());
        $this->logger->debugLogs('Client::CREATE PROFILE Request::HTTP-METHOD::' . $this->createProfile->getHTTPMethod());
        $this->logger->debugLogs('Client::CREATE PROFILE Request::EndPoint::' . $this->createProfile->getEndPoint());
        $this->logger->debugLogs('Client::CREATE PROFILE Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('Client::CREATE PROFILE Request::' . json_encode($object));

        return $this->createProfile;
    }

    /**
     * @return \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainAddress
     */
    public function generateObtainAddressRequest(): \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\ObtainAddress
    {
        $customer = $this->customerRepositoryInterface->getById($this->customerSession->getId());
        $defaultBillingAddressId = $customer->getDefaultBilling();
        $uuid = $this->paynopainAddressUUIDReadHandler->getUUID((int) $defaultBillingAddressId);

        if ($uuid === '') {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('There is no UUID for the given address %1', $defaultBillingAddressId));
        }

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $this->obtainAddress->setEnvironment($this->configHelperData->getEnvironment());
        $this->obtainAddress->setAuthorization($object->authorization);

        $endPoint = str_replace('{uuid}', $uuid, $this->obtainAddress->getEndPoint());
        $this->obtainAddress->setEndPoint($endPoint);

        $this->logger->debugLogs('Client::OBTAIN ADDRESS Request::Environment::' . $this->obtainAddress->getEnvironment());
        $this->logger->debugLogs('Client::OBTAIN ADDRESS Request::HTTP-METHOD::' . $this->obtainAddress->getHTTPMethod());
        $this->logger->debugLogs('Client::OBTAIN ADDRESS Request::EndPoint::' . $this->obtainAddress->getEndPoint());
        $this->logger->debugLogs('Client::OBTAIN ADDRESS Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('Client::OBTAIN ADDRESS Request::' . json_encode($object));

        return $this->obtainAddress;
    }

    /**
     * @return \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateAddress
     */
    public function generateCreateAddressRequest(): \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\CreateAddress
    {
        $customer = $this->customerRepositoryInterface->getById($this->customerSession->getId());
        $defaultBillingId = $customer->getDefaultBilling();
        $billingAddress = $this->addressRepositoryInterface->getById($defaultBillingId);

        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();

        $street = $billingAddress->getStreet();
        $object->requestObject->address1 = $street[0];
        $object->requestObject->address2 = isset($street[1]) ? $street[1] : '';
        $object->requestObject->city = $billingAddress->getCity();

        $countryData = $this->countryInformationAcquirerInterface->getCountryInfo($billingAddress->getCountryId());
        $object->requestObject->country = $countryData->getThreeLetterAbbreviation();

        $object->requestObject->default = true;
        $object->requestObject->external_id = $this->customerSession->getId();
        $object->requestObject->state_code = ($billingAddress->getRegion()->getRegion() === null) ? '' : $billingAddress->getRegion()->getRegion();
        $object->requestObject->type = 'BILLING';
        $object->requestObject->zip_code = $billingAddress->getPostcode();

        $this->createAddress->setCustomerAddressId((int) $billingAddress->getId());
        $this->createAddress->setEnvironment($this->configHelperData->getEnvironment());
        $this->createAddress->setAuthorization($object->authorization);
        $this->createAddress->setRequestObject($object->requestObject);

        $this->logger->debugLogs('Client::CREATE ADDRESS Request::Environment::' . $this->createAddress->getEnvironment());
        $this->logger->debugLogs('Client::CREATE ADDRESS Request::HTTP-METHOD::' . $this->createAddress->getHTTPMethod());
        $this->logger->debugLogs('Client::CREATE ADDRESS Request::EndPoint::' . $this->createAddress->getEndPoint());
        $this->logger->debugLogs('Client::CREATE ADDRESS Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('Client::CREATE ADDRESS Request::' . json_encode($object));

        return $this->createAddress;
    }

    /**
     * @param string $uuid
     * @return \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\DeleteAddress
     */
    public function generateDeleteAddressRequest(string $uuid): \Paynopain\Payments\Model\PaylandsAPI\ClientManagement\DeleteAddress
    {
        $object = new \stdClass();
        $object->authorization = $this->configHelperData->getAuthorization();

        $object->requestObject = new \stdClass();
        $object->requestObject->signature = $this->configHelperData->getSignature();
        $object->requestObject->uuid = $uuid;

        $this->deleteAddress->setEnvironment($this->configHelperData->getEnvironment());
        $this->deleteAddress->setAuthorization($object->authorization);
        $this->deleteAddress->setRequestObject($object->requestObject);

        $this->logger->debugLogs('Client::DELETE ADDRESS Request::Environment::' . $this->deleteAddress->getEnvironment());
        $this->logger->debugLogs('Client::DELETE ADDRESS Request::HTTP-METHOD::' . $this->deleteAddress->getHTTPMethod());
        $this->logger->debugLogs('Client::DELETE ADDRESS Request::EndPoint::' . $this->deleteAddress->getEndPoint());
        $this->logger->debugLogs('Client::DELETE ADDRESS Request::Authorization::' . $object->authorization);
        $this->logger->debugLogs('Client::DELETE ADDRESS Request::' . json_encode($object));

        return $this->deleteAddress;
    }
}
