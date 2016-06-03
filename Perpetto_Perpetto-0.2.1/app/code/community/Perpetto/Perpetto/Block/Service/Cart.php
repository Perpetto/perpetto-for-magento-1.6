<?php

/**
 * Class Perpetto_Perpetto_Block_Service_Cart
 */
class Perpetto_Perpetto_Block_Service_Cart extends Mage_Core_Block_Template
{
    /**
     * Init
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/service/checkout-cart.phtml');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $quote = $this->getQuote();
        if (!$quote->hasItems()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

}
