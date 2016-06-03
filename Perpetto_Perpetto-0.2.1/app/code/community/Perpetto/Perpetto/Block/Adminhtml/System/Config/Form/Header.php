<?php

class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Header extends Mage_Adminhtml_Block_Abstract
{

    /**
     * @var string
     */
    protected $_messages;

    /**
     * Initialize block template
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/adminhtml/system/config/form/header.phtml');
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $layout = $this->getLayout();
        $messages = $layout->getBlock('messages');
        /** @var $messages Mage_Core_Block_Messages */

        $perpetto = Mage::helper('perpetto');

        if (!$messages->getMessageCollection()->count()) {
            if (!$perpetto->isActive()) {
                $messages->addWarning('No connection can be established to your account. Please check the Account ID and Secret in the Account Tab.');
            }
        }

        if ($perpetto->isActive()) {
            if ($perpetto->isTrial() && $perpetto->getTrialDaysLeft() < 1) {
                $messages->addWarning('Your trial period has ended. All results can be seen in the Perpetto Dashboard. Please add the billing information needed to continue using Perpetto.');
            } else {
                $messages->addSuccess('Your Perpetto account is connected and the recommendations are activated.');
            }
        }

        $this->_messages = $layout->getBlock('messages')->toHtml();
        $layout->unsetBlock('messages');

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getMessagesHtml()
    {
        return $this->_messages;
    }
}
