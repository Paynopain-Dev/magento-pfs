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
use Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;
use Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterfaceFactory;
use Paynopain\Payments\Api\Data\GeneratedPaymentOrderSearchResultsInterfaceFactory;
use Paynopain\Payments\Api\GeneratedPaymentOrderRepositoryInterface;
use Paynopain\Payments\Model\ResourceModel\GeneratedPaymentOrder as ResourceGeneratedPaymentOrder;
use Paynopain\Payments\Model\ResourceModel\GeneratedPaymentOrder\CollectionFactory as GeneratedPaymentOrderCollectionFactory;

class GeneratedPaymentOrderRepository implements GeneratedPaymentOrderRepositoryInterface
{
    /**
     * @param ResourceGeneratedPaymentOrder $resource
     * @param GeneratedPaymentOrderInterfaceFactory $generatedPaymentOrderFactory
     * @param GeneratedPaymentOrderCollectionFactory $generatedPaymentOrderCollectionFactory
     * @param GeneratedPaymentOrderSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        protected ResourceGeneratedPaymentOrder $resource,
        protected GeneratedPaymentOrderInterfaceFactory $generatedPaymentOrderFactory,
        protected GeneratedPaymentOrderCollectionFactory $generatedPaymentOrderCollectionFactory,
        protected GeneratedPaymentOrderSearchResultsInterfaceFactory $searchResultsFactory,
        protected CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->generatedPaymentOrderFactory = $generatedPaymentOrderFactory;
        $this->generatedPaymentOrderCollectionFactory = $generatedPaymentOrderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder
    ): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface {
        try {
            $this->resource->save($generatedPaymentOrder);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Could not save the GeneratedPaymentOrder: %1',
                $e->getMessage()
            ));
        }
        return $generatedPaymentOrder;
    }

    /**
     * @inheritDoc
     */
    public function get($generatedPaymentOrderId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
    {
        $generatedPaymentOrder = $this->generatedPaymentOrderFactory->create();
        $this->resource->load($generatedPaymentOrder, $generatedPaymentOrderId);
        if (!$generatedPaymentOrder->getId()) {
            throw new NoSuchEntityException(__('GeneratedPaymentOrder with id "%1" does not exist.', $generatedPaymentOrderId));
        }
        return $generatedPaymentOrder;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ): \Magento\Framework\Api\SearchResults {
        $collection = $this->generatedPaymentOrderCollectionFactory->create();

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
        GeneratedPaymentOrderInterface $generatedPaymentOrder
    ): bool {
        try {
            $generatedPaymentOrderModel = $this->generatedPaymentOrderFactory->create();
            $this->resource->load($generatedPaymentOrderModel, $generatedPaymentOrder->getId());
            $this->resource->delete($generatedPaymentOrderModel);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__(
                'Could not delete the GeneratedPaymentOrder: %1',
                $e->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($generatedPaymentOrderId): bool
    {
        return $this->delete($this->get($generatedPaymentOrderId));
    }
}
