<?php

/**
 * Class Perpetto_Perpetto_Block_Recommendations
 */
class Perpetto_Perpetto_Block_Recommendations extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    /**
     * @var string
     */
    protected $_class = 'ptto-rec-slot-token';

    /**
     * @var Perpetto_Perpetto_Model_Slot
     */
    protected $_slot;

    /**
     * Init
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/recommendations.phtml');
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * @return Perpetto_Perpetto_Model_Slot
     */
    public function getSlot()
    {
        return $this->_slot;
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $slotId = $this->getData('slot_id');
        $class = $this->getData('attr_class');

        $slot = Mage::getModel('perpetto/slot')->load($slotId, 'perpetto_id');
        if ($slot->getId()) {
            $this->_slot = $slot;
            $engine = $slot->getEngineName();
            $class = sprintf('ptto-%s %s', strtolower(str_replace('_', '-', $engine)), $class);
        }

        $this->_class .= ' ' . $class;

        parent::_beforeToHtml();
    }


    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_slot instanceof Perpetto_Perpetto_Model_Slot) {
            return '';
        }

        return parent::_toHtml();
    }

}
