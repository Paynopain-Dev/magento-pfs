<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement;

use Paynopain\Payments\Model\PaylandsAPI\CardTokenManagement;

class DeleteSourceUUID extends CardTokenManagement
{
    /** @var string */
    protected string $httpMethod = 'DELETE';
}
