<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\ResourceModel\GeneratedPaymentOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected $_idFieldName = \Paynopain\Payments\Api\Data\GeneratedPaymentOrderInterface::ENTITY_ID;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Paynopain\Payments\Model\GeneratedPaymentOrder::class,
            \Paynopain\Payments\Model\ResourceModel\GeneratedPaymentOrder::class
        );
    }
}
