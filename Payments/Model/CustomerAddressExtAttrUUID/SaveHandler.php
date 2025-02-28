<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\CustomerAddressExtAttrUUID;

class SaveHandler
{
    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        protected \Magento\Framework\App\ResourceConnection $resourceConnection,
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param integer $customerAddressId
     * @param string $uuid
     * @return boolean
     */
    public function setUUID(int $customerAddressId, string $uuid): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('paynopain_customer_address_uuid');
        $connection->query(
            "INSERT INTO $table VALUES (null, $customerAddressId, '$uuid');"
        );

        return true;
    }
}
