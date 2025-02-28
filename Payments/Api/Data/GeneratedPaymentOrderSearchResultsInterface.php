<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Api\Data;

interface GeneratedPaymentOrderSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get GeneratedPaymentOrder list.
     * @return \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
