<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Controller\StoredCards;

use Magento\Framework\App\Action\HttpPostActionInterface;

class AddNew implements HttpPostActionInterface
{
    /**
     * @param \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManagerInterface
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param \Magento\Framework\App\Response\Http $http
     * @param \Paynopain\Payments\Logger\Logger $logger
     */
    public function __construct(
        protected \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement,
        protected \Magento\Framework\Message\ManagerInterface $messageManagerInterface,
        protected \Magento\Framework\Serialize\Serializer\Json $serializer,
        protected \Magento\Framework\App\RequestInterface $requestInterface,
        protected \Magento\Framework\App\Response\Http $http,
        protected \Paynopain\Payments\Logger\Logger $logger
    ) {
        $this->cardTokenManagement = $cardTokenManagement;
        $this->serializer = $serializer;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->requestInterface = $requestInterface;
        $this->http = $http;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $source = $this->requestInterface->getParams();

            $cardSourceStd = new \stdClass();
            $cardSourceStd->code = (int) $source['code'];
            $cardSourceStd->message = 'OK';
            $cardSourceStd->current_time = $source['current_time'];

            $cardSourceStd->Source = new \stdClass();
            $cardSourceStd->Source->uuid = $source['source']['uuid'];
            $cardSourceStd->Source->type = $source['source']['type'];
            $cardSourceStd->Source->brand = $source['source']['brand'];
            $cardSourceStd->Source->holder = $source['source']['holder'] == 'UNKNOWN' ? '' : $source['source']['holder'];
            $cardSourceStd->Source->bin = $source['source']['bin'];
            $cardSourceStd->Source->last4 = $source['source']['last4'];
            $cardSourceStd->Source->expire_month = $source['source']['expire_month'];
            $cardSourceStd->Source->expire_year = $source['source']['expire_year'];
            $cardSourceStd->Source->additional = $source['source']['additional'];
            $cardSourceStd->Source->validation_date = $source['source']['validation_date'];
            $cardSourceStd->Source->creation_date = $source['source']['creation_date'];
            $cardSourceStd->Source->token = '';

            $cardSourceStd->Customer = new \stdClass();
            $cardSourceStd->Customer->external_id = (int) $source['customer']['external_id'];

            $this->logger->debugLogs('StoredCards::ADD NEW::Response::' . serialize($source));

            $this->cardTokenManagement->saveEntity($cardSourceStd);
            $this->messageManagerInterface->addSuccess(__('Card saved!'));

            return $this->jsonResponse(true);
        } catch (\Exception $e) {
            $this->logger->debugLogs('StoredCards::ADD NEW::ERROR::' . serialize($e->getMessage()));
            $this->messageManagerInterface->addErrorMessage(__('There was an error saving the card. Please contact administrator.'));
        }

        return $this->jsonResponse(false);
    }

    /**
     * Create json response
     *
     * @return ResultInterface
     */
    public function jsonResponse($response = '')
    {
        $this->http->getHeaders()->clearHeaders();
        $this->http->setHeader('Content-Type', 'application/json');
        return $this->http->setBody(
            $this->serializer->serialize($response)
        );
    }
}
