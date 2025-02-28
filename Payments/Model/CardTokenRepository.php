<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Paynopain\Payments\Api\Data\CardTokenInterface;
use Paynopain\Payments\Api\Data\CardTokenInterfaceFactory;
use Paynopain\Payments\Api\Data\CardTokenSearchResultsInterfaceFactory;
use Paynopain\Payments\Api\CardTokenRepositoryInterface;
use Paynopain\Payments\Model\ResourceModel\CardToken as ResourceCardToken;
use Paynopain\Payments\Model\ResourceModel\CardToken\CollectionFactory as CardTokenCollectionFactory;

class CardTokenRepository implements CardTokenRepositoryInterface
{
    /**
     * @param ResourceCardToken $resource
     * @param CardTokenInterfaceFactory $cardTokenFactory
     * @param CardTokenCollectionFactory $cardTokenCollectionFactory
     * @param CardTokenSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        protected ResourceCardToken $resource,
        protected CardTokenInterfaceFactory $cardTokenFactory,
        protected CardTokenCollectionFactory $cardTokenCollectionFactory,
        protected CardTokenSearchResultsInterfaceFactory $searchResultsFactory,
        protected CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->cardTokenFactory = $cardTokenFactory;
        $this->cardTokenCollectionFactory = $cardTokenCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        \Paynopain\Payments\Api\Data\CardTokenInterface $cardToken
    ): \Paynopain\Payments\Api\Data\CardTokenInterface {
        try {
            $this->resource->save($cardToken);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Could not save the CardToken: %1',
                $e->getMessage()
            ));
        }
        return $cardToken;
    }

    /**
     * @inheritDoc
     */
    public function get($cardTokenId): \Paynopain\Payments\Api\Data\CardTokenInterface
    {
        $cardToken = $this->cardTokenFactory->create();
        $this->resource->load($cardToken, $cardTokenId);
        if (!$cardToken->getId()) {
            throw new NoSuchEntityException(__('CardToken with id "%1" does not exist.', $cardTokenId));
        }
        return $cardToken;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ): \Magento\Framework\Api\SearchResults {
        $collection = $this->cardTokenCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(
        CardTokenInterface $cardToken
    ): bool {
        try {
            $cardTokenModel = $this->cardTokenFactory->create();
            $this->resource->load($cardTokenModel, $cardToken->getId());
            $this->resource->delete($cardTokenModel);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CardToken: %1',
                $e->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($cardTokenId): bool
    {
        return $this->delete($this->get($cardTokenId));
    }
}
