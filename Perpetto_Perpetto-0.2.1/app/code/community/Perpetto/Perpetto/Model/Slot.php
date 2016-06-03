<?php

/**
 * Class Perpetto_Perpetto_Model_Slot
 */
class Perpetto_Perpetto_Model_Slot extends Mage_Core_Model_Abstract
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'perpetto_slot';

    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init('perpetto/slot');
    }

}
