<?php

/**
 * Class Perpetto_Perpetto_Model_Resource_Slot
 */
class Perpetto_Perpetto_Model_Resource_Slot extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init('perpetto/slots', 'slot_id');
    }

}
