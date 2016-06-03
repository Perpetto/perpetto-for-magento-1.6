<?php

/**
 * Class Perpetto_Perpetto_Model_Observer
 */
class Perpetto_Perpetto_Model_Observer
{
    protected $_perpettoSlotsUpdated = false;
    /**
     * @param Varien_Event_Observer $observer
     * @throws Perpetto_Perpetto_Exception
     */
    public function saveAdminSettings(Varien_Event_Observer $observer)
    {
        if ($this->_perpettoSlotsUpdated) {
            return;
        }

        $config = $observer->getObject();

        if ($config instanceof Mage_Adminhtml_Model_Config_Data && $config->getSection() == 'perpetto') {
            $groups = $config->getGroups();

            $accountId = trim(@$groups['general']['fields']['account_id']['value']);
            $secret = trim(@$groups['general']['fields']['secret']['value']);

            $apiHelper = Mage::helper('perpetto/api');
            $apiHelper->updateInfo($accountId, $secret);
            $apiHelper->updateSlots($accountId, $secret);

            $config->setGroups(array());
            $this->_perpettoSlotsUpdated = true;
        }

        /** @var Mage_Core_Model_Config_Data $config */
        $config = $observer->getConfigData();
        if ($config instanceof Mage_Core_Model_Config_Data && $config->getPath() == 'perpetto/general/account_id') {
            $groups = $config->getGroups();

            $accountId = trim(@$groups['general']['fields']['account_id']['value']);
            $secret = trim(@$groups['general']['fields']['secret']['value']);

            $apiHelper = Mage::helper('perpetto/api');
            $apiHelper->updateInfo($accountId, $secret);
            $apiHelper->updateSlots($accountId, $secret);

            $this->_perpettoSlotsUpdated = true;
        }
    }

    /**
     * Delete slot widgets
     */
    public function deleteSlotWidgets(Varien_Event_Observer $observer)
    {
        $slot = $observer->getObject();
        if ($slot instanceof Perpetto_Perpetto_Model_Slot) {
            $pttoWidget = Mage::helper('perpetto/widget');
            $pttoWidget->deleteSlotWidgets($slot);
        }
    }

    /**
     * Update slots widgets
     */
    public function updateSlotsWidgets()
    {
        $slotsCollection = Mage::getResourceModel('perpetto/slot_collection');

        $pagesSortOrders = array();

        foreach ($slotsCollection as $slot) {
            /** @var $slot Perpetto_Perpetto_Model_Slot */

            $page = $slot->getPage();
            if (!array_key_exists($page, $pagesSortOrders)) {
                $pagesSortOrders[$page] = 0;
            }

            $sortOrder = $pagesSortOrders[$page] += 10;

            Mage::helper('perpetto/widget')->createSlotWidgets($slot, $sortOrder);
        }
    }

    /**
     * Send product data to perpetto on product save
     *
     * @observer catalog_product_save_after
     * @param Varien_Event_Observer $observer
     */
    public function saveProduct(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();

        if ($product instanceof Mage_Catalog_Model_Product && $product->isVisibleInCatalog()) {
            try {
                $api = Mage::helper('perpetto/api');
                $api->updateProduct($product);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Enable the showing of the customer service block
     *
     * @observe customer_register_success
     * @param Varien_Event_Observer $observer
     */
    public function setShowCustomerServiceBlock(Varien_Event_Observer $observer)
    {
        $this->_setShowCustomerServiceBlock(true);
    }

    /**
     * Enable the showing of the customer service block
     *
     * @observe checkout_type_onepage_save_order_after
     * @param Varien_Event_Observer $observer
     */
    public function setShowCustomerServiceBlockAfterOrder(Varien_Event_Observer $observer)
    {
        $order = $observer->getData('order');

        if ($order instanceof Mage_Sales_Model_Order) {
            $session = $this->_getSession();
            $session->setData('custom_user_service_email', $order->getCustomerEmail());
            $session->setData('custom_user_service_firstname', $order->getCustomerFirstname());
            $session->setData('custom_user_service_lastname', $order->getCustomerLastname());

            $this->_setShowCustomerServiceBlock(true);
        }
    }

    /**
     * Enable the showing of the customer service block
     *
     * @observe customer_register_success
     * @param Varien_Event_Observer $observer
     */
    public function setShowCustomerServiceBlockEdit(Varien_Event_Observer $observer)
    {
        $request = Mage::app()->getRequest();
        $controllerName = $request->getControllerName();

        if ($controllerName == 'account') {
            $this->_setShowCustomerServiceBlock(true);
        }
    }

    /**
     * Enable the showing of the customer service block
     *
     * @observe newsletter_subscriber_save_commit_after
     * @param Varien_Event_Observer $observer
     */
    public function setShowCustomerServiceBlockOnNewsletterSubscription(Varien_Event_Observer $observer)
    {
        $subscriber = $observer->getSubscriber();

        if ($subscriber instanceof Mage_Newsletter_Model_Subscriber) {
            $subscriberStatus = $subscriber->getSubscriberStatus();
            if (Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED == $subscriberStatus) {
                $session = $this->_getSession();
                $session->setData('custom_user_service_email', $subscriber->getEmail());

                $this->_setShowCustomerServiceBlock(true);
            }
        }
    }

    /**
     * @param bool $status
     */
    protected function _setShowCustomerServiceBlock($status = true)
    {
        $session = $this->_getSession();
        $session->setData('_should_show_user_service_block', $status);
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        $session = Mage::getSingleton('customer/session');

        return $session;
    }

}
