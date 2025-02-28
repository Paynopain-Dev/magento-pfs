<?php
declare (strict_types = 1);

namespace Paynopain\Payments\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class StoredCards implements SectionSourceInterface
{
    /**
     * @param \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
     */
    public function __construct(
        protected \Paynopain\Payments\Model\CardTokenManagement $cardTokenManagement
    ) {
        $this->cardTokenManagement = $cardTokenManagement;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        $storedCards = [];
        foreach ($this->cardTokenManagement->getByCustomerEntityId() as $storedCard) {
            $storedCards[$storedCard->getSourceUUID()] = [
                'source_uuid' => $storedCard->getSourceUUID(),
                'brand' => $storedCard->getBrand(),
                'bin' => $storedCard->getBin(),
                'last4' => $storedCard->getLast4(),
                'additional' => $storedCard->getAdditional(),
            ];
        }

        return [
            'stored_cards' => [
                \Paynopain\Payments\Model\Payment\PaynopainPayment::METHOD_CODE => $storedCards,
            ],
        ];
    }
}
