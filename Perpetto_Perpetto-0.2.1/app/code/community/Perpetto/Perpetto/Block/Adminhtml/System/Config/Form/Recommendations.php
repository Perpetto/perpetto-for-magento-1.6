<?php

/**
 * Class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Recommendations
 */
class Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Recommendations extends Perpetto_Perpetto_Block_Adminhtml_System_Config_Form_Abstract
{
    /**
     * @var Perpetto_Perpetto_Model_Resource_Slot_Collection
     */
    protected $_slots;

    /**
     * Init
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('perpetto/adminhtml/system/config/form/recommendations.phtml');
    }

    /**
     * Retrieve slots collection
     *
     * @return Perpetto_Perpetto_Model_Resource_Slot_Collection
     */
    public function getSlotsCollection()
    {
        if (is_null($this->_slots)) {
            $slots = Mage::getResourceModel('perpetto/slot_collection');

            $this->_slots = $slots;
        }

        return $this->_slots;
    }

    /**
     * Retrieve slots grouped by page
     *
     * @return array
     */
    public function getGroupedSlots()
    {
        $slots = $this->getSlotsCollection();

        $data = array();

        foreach ($slots as $slot) {
            /** @var $slot Perpetto_Perpetto_Model_Slot */

            $page = $slot->getPage();

            if (!array_key_exists($page, $data)) {
                $data[$page] = array();
            }

            array_push($data[$page], $slot);
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDashboardUrl()
    {
        $url = Mage::helper('perpetto/url')->getDashboardUrl();

        return $url;
    }

    /**
     * @return string
     */
    public function getContactsUrl()
    {
        $url = Mage::helper('perpetto/url')->getContactsUrl();

        return $url;
    }

    /**
     * @return string
     */
    public function getBillingUrl()
    {
        $url = Mage::helper('perpetto/url')->getBillingUrl();

        return $url;
    }

    /**
     * @return string
     */
    public function getRandomProductPreviewUrl()
    {
        $url = Mage::helper('perpetto/url')->getRandomProductPreviewUrl();

        return $url;
    }

    /**
     * @return string
     */
    public function getRecommendationsUrl()
    {
        $url = Mage::helper('perpetto/url')->getRecommendationsUrl();

        return $url;
    }

    /**
     * @return bool
     */
    public function showLinks()
    {
        $showLinks = Mage::helper('perpetto')->isActive();

        return $showLinks;
    }

    /**
     * @return bool
     */
    public function showSlots()
    {
        $showSlots = Mage::helper('perpetto')->isActive();

        return $showSlots;
    }

    /**
     * @return string
     */
    public function getBillingTitle()
    {
        $helper = Mage::helper('perpetto');
        $title = 'Billing Information';

        if ($helper->isTrial()) {
            $days = $helper->getTrialDaysLeft();
            $title = $this->__('Free Trial (%d Day%s Left)', $days, $days != 1 ? 's' : '');
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getBillingText()
    {
        $helper = Mage::helper('perpetto');
        $text = $helper->isTrial()
            ? 'Don\'t forget to add your billing information before the trial ends.'
            : 'You can see your current bill and the forecast for this month.';

        return $text;
    }

    /**
     * @return string
     */
    public function getBillingButtonText()
    {
        $helper = Mage::helper('perpetto');
        $text = $helper->isTrial()
            ? 'Add Billing Information Securely'
            : 'Check Current Bill';

        return $text;
    }

}
