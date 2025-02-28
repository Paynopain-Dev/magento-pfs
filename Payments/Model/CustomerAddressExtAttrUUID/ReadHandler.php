<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\CustomerAddressExtAttrUUID;

class ReadHandler
{
    /** @var string */
    private $uuid = '';

    /**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        protected \Magento\Framework\App\ResourceConnection $resourceConnection,
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param integer|null $customerAddressId
     * @return string
     */
    public function getUUID(?int $customerAddressId): string
    {
        $connection = $this->resourceConnection->getConnection();
        $table = $this->resourceConnection->getTableName('paynopain_customer_address_uuid');
        $resultQuery = $connection->fetchAll(
            "SELECT uuid FROM $table WHERE customer_address_id = $customerAddressId"
        );

        foreach ($resultQuery as $row) {
            $this->uuid = $row['uuid'];
        }

        return $this->uuid;
    }
}
