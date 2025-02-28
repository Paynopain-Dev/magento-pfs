<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\IntegrationType;

use Magento\Framework\App\Action\HttpGetActionInterface;

class Iframe implements HttpGetActionInterface
{
    /**
     * @param \Paynopain\Payments\Controller\IntegrationType\BaseAction $baseAction
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        protected \Paynopain\Payments\Controller\IntegrationType\BaseAction $baseAction,
        protected \Magento\Checkout\Model\Session $checkoutSession,
        protected \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        protected \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        $this->baseAction = $baseAction;
        $this->checkoutSession = $checkoutSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultFactory = $resultFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $orderId = $this->checkoutSession->getLastRealOrderId();
        if (!$orderId) {
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/');

            return $redirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $response = $this->baseAction->baseRequest((int) $orderId);

        if ($this->baseAction->getDirectToSuccess()) {
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/checkout/onepage/success');

            return $redirect;
        }

        /** @var \Paynopain\Payments\Block\IntegrationType\Iframe $integrationTypeBlock */
        $integrationTypeBlock = $resultPage->getLayout()->getBlock('integration_type.iframe');
        $integrationTypeBlock->setIncrementId($this->baseAction->getIncrementId());
        if (isset($response->order->token)) {
            $integrationTypeBlock->setToken(
                $response->order->token
            );

            if ($this->baseAction->getChallengeRequired()) {
                $integrationTypeBlock->set3dsUrl($this->baseAction->get3dsUrl());
            }
        }

        return $resultPage;
    }
}
