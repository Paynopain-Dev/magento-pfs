<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Logger;

use Monolog\Logger as CoreLogger;

class Logger extends CoreLogger
{
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Paynopain\Payments\Logger\Handler $loggerHandler
     */
    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        protected \Paynopain\Payments\Logger\Handler $loggerHandler
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct('Paynopain_Payments', [$loggerHandler]);
    }

    /**
     * @param string $message
     * @return void
     */
    public function debugLogs(string $message): void
    {
        if ($this->isActiveLog()) {
            $this->debug($message);
        }
    }

    /**
     * @return integer
     */
    private function isActiveLog(): int
    {
        return (int) $this->scopeConfig->getValue(
            'payment/paynopain_payment/debug_logs',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
