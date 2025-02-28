<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    /** @var string */
    protected $loggerType = \Monolog\Logger::DEBUG;

    /** @var string */
    protected $fileName = 'var/log/paylands_paynopain/debug.log';
}
