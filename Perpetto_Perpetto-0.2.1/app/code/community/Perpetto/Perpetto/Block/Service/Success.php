<?php

/**
 * Class Perpetto_Perpetto_Block_Service_Success
 */
class Perpetto_Perpetto_Block_Service_Success extends Mage_Core_Block_Template
{
    /**
     * Init
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/service/success-page.phtml');
    }

    /**
     * @return int
     */
    public function getLastSuccessQuoteId()
    {
        $session = $this->getOnepage()->getCheckout();
        $lastOrder = $session->getLastRealOrder();

        return $lastOrder->getQuoteId();
    }

    /**
     * Get one page checkout model
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
}
