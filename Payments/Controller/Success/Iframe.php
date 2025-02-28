<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\Success;

use Magento\Framework\App\Action\HttpGetActionInterface;

class Iframe implements HttpGetActionInterface
{
    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        protected \Magento\Framework\Controller\ResultFactory $resultFactory,
        protected \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        protected \Magento\Framework\App\RequestInterface $requestInterface
    ) {
        $this->resultFactory = $resultFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->requestInterface = $requestInterface;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $incrementId = $this->requestInterface->getParam('soe_increment_id');
        if ($incrementId === null) {
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('/checkout/cart');

            return $redirect;
        }

        $resultPage = $this->resultPageFactory->create();
        $successIframeBlock = $resultPage->getLayout()->getBlock('success.iframe');
        $successIframeBlock->setIncrementId($incrementId);

        return $resultPage;
    }
}
