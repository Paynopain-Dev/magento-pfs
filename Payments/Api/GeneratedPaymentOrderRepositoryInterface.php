<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GeneratedPaymentOrderRepositoryInterface
{
    /**
     * Save GeneratedPaymentOrder
     * @param \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder
    ): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * Retrieve GeneratedPaymentOrder
     * @param string $entityId
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId): \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface;

    /**
     * Retrieve GeneratedPaymentOrder matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResults
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Magento\Framework\Api\SearchResults;

    /**
     * Delete GeneratedPaymentOrder
     * @param \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface $generatedPaymentOrder
    ): bool;

    /**
     * Delete GeneratedPaymentOrder by ID
     * @param string $generatedPaymentOrderId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId): bool;
}
