<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\System\Config\Source;

class PaymentAction implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var string */
    const ONLY_AUTHORIZATION = 'DEFERRED';

    /** @var string */
    const AUTHORIZATION_AND_CAPTURE = 'AUTHORIZATION';

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::ONLY_AUTHORIZATION, 'label' => __('Authorize')],
            ['value' => self::AUTHORIZATION_AND_CAPTURE, 'label' => __('Authorize & capture')],
        ];
    }
}
