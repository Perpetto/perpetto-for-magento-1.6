<?php

/**
 * Class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Abstract
 */
abstract class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Abstract extends Mage_Adminhtml_Block_System_Config_Form
{
    /**
     * @var Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Header
     */
    protected $_header;

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $layout = $this->getLayout();
        $head = $layout->getBlock('head');
        /** @var $head Mage_Adminhtml_Block_Page_Head */

        $head->addCss('perpetto.css');

        $block = $layout->createBlock('perpetto/adminhtml_system_config_form_header');
        $this->_header = $block;

        return parent::_prepareLayout();
    }

    /**
     * Get form header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return $this->getHeaderBlock()->toHtml();
    }

    /**
     * @return Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Header|null
     */
    public function getHeaderBlock()
    {
        return $this->_header;
    }
}
