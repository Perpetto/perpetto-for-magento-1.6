<?php

/**
 * Class Perpetto_Perpetto_Block_Service_User
 */
class Perpetto_Perpetto_Block_Service_User extends Mage_Core_Block_Template
{
    /**
     * Init
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/service/user.phtml');
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getEmail()
    {
        $session = $this->_getSession();
        $email = $session->getCustomUserServiceEmail();

        if (!$email) {
            $user = $this->getUser();
            $email = $user->getEmail();
        }

        return $email;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        $session = $this->_getSession();
        $firstname = $session->getCustomUserServiceFirstname();

        if (!$firstname) {
            $user = $this->getUser();
            $firstname = $user->getFirstname();
        }
        return $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        $session = $this->_getSession();
        $lastname = $session->getCustomUserServiceLastname();

        if (!$lastname) {
            $user = $this->getUser();
            $lastname = $user->getLastname();
        }

        return $lastname;
    }

    /**
     * Check if we should show customer service block
     *
     * @return bool
     */
    public function showBlock()
    {
        $session = $this->_getSession();
        $showBlock = (bool)$session->getData('_should_show_user_service_block');

        return $showBlock;
    }

    /**
     * Get the current logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getUser()
    {
        $session = $this->_getSession();
        $customer = $session->getCustomer();

        return $customer;
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $user = $this->getUser();

        if (!$user instanceof Mage_Customer_Model_Customer || !$this->showBlock()) {
            return '';
        }

        $session = $this->_getSession();
        $session->setData('_should_show_user_service_block', false);

        return parent::_toHtml();
    }

    /**
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $session = $this->_getSession();

        $session->setData('custom_user_service_email', false);
        $session->setData('custom_user_service_firstname', false);
        $session->setData('custom_user_service_lastname', false);

        return parent::_afterToHtml($html);
    }

}
