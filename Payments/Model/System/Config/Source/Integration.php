<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\System\Config\Source;

class Integration implements \Magento\Framework\Data\OptionSourceInterface

{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'redirect', 'label' => __('Redirect')],
            ['value' => 'iframe', 'label' => __('Iframe')],
        ];
    }
}
