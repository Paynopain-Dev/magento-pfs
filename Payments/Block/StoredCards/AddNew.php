<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Block\StoredCards;

use \Magento\Framework\View\Element\Template;

class AddNew extends Template
{
    /** @var string */
    private $token;

    /** @var string */
    private $environment;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return self
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @param string $environment
     * @return self
     */
    public function setEnvironment(string $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaveUrl(): string
    {
        return $this->_urlBuilder->getUrl(
            'paynopain/storedcards/addnew',
            [
                '_secure' => true,
            ]
        );
    }
}
