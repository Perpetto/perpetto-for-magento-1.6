<?php

/**
 * Class Perpetto_Perpetto_RecommendationsController
 */
class Perpetto_Perpetto_RecommendationsController extends Perpetto_Perpetto_Controller_Action
{
    /**
     * Slots action
     */
    public function slotsAction()
    {
        $response = $this->getResponse();
        $core = Mage::helper('core');

        $headers = array();

        try {
            $this->_validateRequest();

            $api = Mage::helper('perpetto/api');
            $api->updateInfo();
            $api->updateSlots();

            $data = array('status' => 'success');

        } catch (Exception $e) {
            $data = array(
                'status' => 'error',
                'error' => $e->getMessage(),
            );

            if (!array_key_exists('HTTP/1.1', $headers)) {
                $headers['HTTP/1.1'] = '500 Internal Server Error';
            }
        }

        $json = $core->jsonEncode($data);
        $response->setBody($json);

        $response->clearHeaders();
        $response->setHeader('Content-Type', 'application/json', true);
        foreach ($headers as $name => $value) {
            $response->setHeader($name, $value);
        }
    }

}
