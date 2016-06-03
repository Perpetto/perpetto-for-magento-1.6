<?php

/**
 * Class Perpetto_Perpetto_Model_Resource_Slot_Collection
 */
class Perpetto_Perpetto_Model_Resource_Slot_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Init
     */
    protected function _construct()
    {
        $this->_init('perpetto/slot');
    }

}
