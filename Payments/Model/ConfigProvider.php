<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{

    /** @var array */
    protected $methodCodes = [
        \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE => [],
    ];

    /** @var array */
    protected $methods = [];

    /**
     * @param \Paynopain\Payments\Helper\ConfigHelperData $configHelperData
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     */
    public function __construct(
        protected \Paynopain\Payments\Helper\ConfigHelperData $configHelperData,
        protected \Magento\Framework\UrlInterface $urlBuilder,
        protected \Magento\Payment\Helper\Data $paymentHelper,
        protected \Magento\Framework\View\Asset\Repository $assetRepo,
        protected \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    ) {
        $this->configHelperData = $configHelperData;
        $this->urlBuilder = $urlBuilder;
        $this->paymentHelper = $paymentHelper;
        $this->assetRepo = $assetRepo;
        $this->storeManagerInterface = $storeManagerInterface;

        foreach ($this->methodCodes as $code => $data) {
            $this->methods[$code] = $this->paymentHelper->getMethodInstance($code);
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = [];

        $integrationType = $this->configHelperData->getIntegrationType();

        $integrationTypeControllerUrl = $this->urlBuilder->getUrl(
            'paynopain/integrationType/redirect',
            [
                '_secure' => $this->storeManagerInterface->getStore()->isCurrentlySecure(),
            ]
        );

        if ($integrationType == 'iframe') {
            $integrationTypeControllerUrl = $this->urlBuilder->getUrl(
                'paynopain/integrationType/iframe',
                [
                    '_secure' => $this->storeManagerInterface->getStore()->isCurrentlySecure(),
                ]
            );
        }
        $config['payment']['paynopain_payments']['integrationType'] = $integrationType;
        $config['payment']['paynopain_payments']['integrationTypeControllerUrl'] = $integrationTypeControllerUrl;
        $config['payment']['paynopain_payments']['secure'] = $this->configHelperData->getSecure();

        foreach ($this->methodCodes as $code => $data) {
            if ($this->methods[$code]->isAvailable()) {
                $this->configHelperData->setPaymentMethodCode($code);
                $config['payment']['paynopain_payments'][$code]['instructions'] = $this->configHelperData->getInstructions();
                $config['payment']['paynopain_payments'][$code]['icons'] = $this->createAsset('Paynopain_Payments::images/icon_cards.png')->getUrl();
            }
        }

        return $config;
    }

    /**
     * @param string $fileId
     * @return \Magento\Framework\View\Asset\File
     */
    public function createAsset(string $fileId): \Magento\Framework\View\Asset\File
    {
        $params = array_merge(['_secure' => $this->storeManagerInterface->getStore()->isCurrentlySecure()]);

        return $this->assetRepo->createAsset($fileId, [$params]);
    }
}
