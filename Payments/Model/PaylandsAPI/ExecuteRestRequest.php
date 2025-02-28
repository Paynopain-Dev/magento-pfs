<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\PaylandsAPI;

class ExecuteRestRequest
{
    /** @var string */
    const REAL_ENVIRONMENT = 'https://api.paylands.com/v1/';

    /** @var string */
    const SANDBOX_ENVIRONMENT = 'https://api.paylands.com/v1/sandbox/';

    /** @var array */
    const ENDPOINTS = [
        'payment',
        'payment/process',
        'payment/tokenized',
        'payment-method/card',
        'payment-method/card/additional',
        'payment/refund',
        'payment/confirmation',
    ];

    /** @var string */
    public $environmentUrl;

    /**
     * @param \Paynopain\Payments\Model\PaylandsAPI\RequestInterface $requestInterface
     * @param string $endpoint
     * @param integer $environment
     * @return \stdClass
     */
    public function executeRequest(
        \Paynopain\Payments\Model\PaylandsAPI\RequestInterface $requestInterface,
    ): \stdClass {
        $endPoint = $requestInterface->getEndPoint();
        $jsonParams = json_encode($requestInterface->getRequestObject());
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $requestInterface->getEnvironment() . $endPoint . '/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $requestInterface->getHTTPMethod(),
                CURLOPT_POSTFIELDS => $jsonParams,
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $requestInterface->getAuthorization(),
                    'Content-Type: application/json',
                ],
            ]
        );

        try {
            $responseCurl = curl_exec($curl);
            $response = json_decode($responseCurl);
            curl_close($curl);

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpCode != 200 && $httpCode != 303) {
                if ($response === null) {
                    throw new \Paynopain\Payments\Model\PaylandsAPI\Exceptions\PaylandsException(
                        'WebService Error::HTTPCode::' . $httpCode . '::Message::JSON Decode returned null - ' . serialize($responseCurl)
                    );
                }

                $message = isset($response->details) ? $response->details : $response->message;
                throw new \Paynopain\Payments\Model\PaylandsAPI\Exceptions\PaylandsException(
                    'WebService Error::HTTPCode::' . $httpCode . '::Message::' . $message
                );
            }

            return $response;
        } catch (\Exception $e) {
            throw new \Paynopain\Payments\Model\PaylandsAPI\Exceptions\PaylandsException(
                $e->getMessage()
            );
        }
    }
}
