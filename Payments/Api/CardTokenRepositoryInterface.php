<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CardTokenRepositoryInterface
{
    /**
     * Save CardToken
     * @param \Paynopain\Payments\Api\Data\CardTokenInterface $cardToken
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Paynopain\Payments\Api\Data\CardTokenInterface $cardToken
    ): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * Retrieve CardToken
     * @param string $cardTokenId
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($cardTokenId): \Paynopain\Payments\Api\Data\CardTokenInterface;

    /**
     * Retrieve CardToken matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Paynopain\Payments\Api\Data\CardTokenSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ): \Magento\Framework\Api\SearchResults;

    /**
     * Delete CardToken
     * @param \Paynopain\Payments\Api\Data\CardTokenInterface $cardToken
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Paynopain\Payments\Api\Data\CardTokenInterface $cardToken
    ): bool;

    /**
     * Delete CardToken by ID
     * @param string $cardTokenId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($cardTokenId): bool;
}
