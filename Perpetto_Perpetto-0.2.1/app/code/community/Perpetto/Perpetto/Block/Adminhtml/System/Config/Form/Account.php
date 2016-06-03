<?php

/**
 * Class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Account
 */
class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Account extends Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Abstract
{
    /**
     * Init
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('perpetto/adminhtml/system/config/form/account.phtml');
    }

    /**
     * @return string
     */
    public function getSignInUrl()
    {
        $url = Mage::helper('perpetto/url')->getSignInUrl();

        return $url;
    }

    /**
     * @return string
     */
    public function getSignUpUrl()
    {
        $url = Mage::helper('perpetto/url')->getSignUpUrl();

        return $url;
    }

}
