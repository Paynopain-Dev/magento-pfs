<?php
/**
 * Copyright © Redegal S.A All rights reserved.
 * See COPYING.txt for license details.
 */
declare (strict_types = 1);

namespace Paynopain\Payments\Model\System\Config\Field;

class Version extends \Magento\Config\Block\System\Config\Form\Field

{
    /**
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        protected \Magento\Framework\Module\ModuleListInterface $moduleList,
        protected \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_moduleList = $moduleList;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element): string
    {
        return (string) $this->_moduleList->getOne('Paynopain_Payments')['setup_version'];
    }
}
