<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Block\StoredCards;

use \Magento\Framework\View\Element\Template;

class ListView extends Template
{
    /**
     * @param \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement,
        public \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
    ) {
        $this->cardTokenManagement = $cardTokenManagement;

        parent::__construct($context, $data);
    }

    /**
     * @return \Paynopain\Payments\Api\Data\CardTokenInterface[]
     */
    public function getStoredCards()
    {
        return $this->cardTokenManagement->getByCustomerEntityId();
    }

    /**
     * @return string
     */
    public function getUpdateUrl(): string
    {
        return $this->_urlBuilder->getUrl(
            'paynopain/storedcards/updateadditional',
            [
                '_secure' => true,
            ]
        );
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->_urlBuilder->getUrl(
            'paynopain/storedcards/delete',
            [
                '_secure' => true,
            ]
        );
    }
}
