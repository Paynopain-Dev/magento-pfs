<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement;

use Paynopain\Payments\Model\PaylandsAPI\GenerateOrderPaymentManagement;

class CaptureConfirmation extends GenerateOrderPaymentManagement
{
    /** @var string */
    protected string $endPoint = 'payment/confirmation';
}
