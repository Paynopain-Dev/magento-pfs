<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI\ClientManagement;

use Paynopain\Payments\Model\PaylandsAPI\ClientManagement;

class CreateAddress extends ClientManagement
{
    /** @var int */
    private $customerAddressId = 0;

    /** @var string */
    protected string $endPoint = 'customer/address';

    /**
     * @return integer
     */
    public function getCustomerAddressId(): int
    {
        return $this->customerAddressId;
    }

    /**
     * @param integer $customerAddressId
     * @return self
     */
    public function setCustomerAddressId(int $customerAddressId): self
    {
        $this->customerAddressId = $customerAddressId;

        return $this;
    }
}
