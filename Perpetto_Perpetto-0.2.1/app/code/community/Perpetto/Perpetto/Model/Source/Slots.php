<?php

/**
 * Class Perpetto_Perpetto_Model_Source_Slots
 */
class Perpetto_Perpetto_Model_Source_Slots
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $slots = Mage::getResourceModel('perpetto/slot_collection');

        $groups = array();

        foreach ($slots as $slot) {
            $page = $slot->getPage();

            if (!array_key_exists($page, $groups)) {
                $groups[$page] = array();
            }

            array_push($groups[$page], $slot);
        }

        $options = array(
            array(
                'label' => '-- Select Perpetto Slot --',
                'value' => '',
            ),
        );

        foreach ($groups as $page => $slots) {
            $pageLabel = ucwords(str_replace('_', ' ', $page));

            foreach ($slots as $slot) {
                $label = sprintf('%s - %s', $pageLabel, $slot->getTitle());
                $value = $slot->getPerpettoId();

                $option = array(
                    'label' => $label,
                    'value' => $value,
                );

                array_push($options, $option);
            }
        }

        return $options;
    }

}
