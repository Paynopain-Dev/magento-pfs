<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Block\Success;

use \Magento\Framework\View\Element\Template;

class Iframe extends Template
{
    /** @var string */
    private $incrementId = '';

    /**
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected \Magento\Framework\UrlInterface $urlBuilder,
        public \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getIncrementId(): string
    {
        return $this->incrementId;
    }

    /**
     * @param string $incrementId
     * @return self
     */
    public function setIncrementId(string $incrementId): self
    {
        $this->incrementId = $incrementId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSuccessPageUrl(): string
    {
        return $this->urlBuilder->getUrl('checkout/onepage/success');
    }
}
