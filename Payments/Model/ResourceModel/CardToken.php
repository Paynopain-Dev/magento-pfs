<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CardToken extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Paynopain\Payments\Api\Data\CardTokenInterface::TABLE,
            \Paynopain\Payments\Api\Data\CardTokenInterface::ENTITY_ID,
        );
    }
}
