<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI\ClientManagement;

use Paynopain\Payments\Model\PaylandsAPI\ClientManagement;

class DeleteAddress extends ClientManagement
{
    /** @var string */
    protected string $httpMethod = 'DELETE';

    /** @var string */
    protected string $endPoint = 'customer/address';
}
