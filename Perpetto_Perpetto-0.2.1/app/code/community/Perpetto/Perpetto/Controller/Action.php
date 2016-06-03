<?php

/**
 * Class Perpetto_Perpetto_Controller_Action
 */
class Perpetto_Perpetto_Controller_Action extends Mage_Core_Controller_Front_Action
{
    /**
     * @throws Perpetto_Perpetto_Exception
     */
    protected function _validateRequest()
    {
        $request = $this->getRequest();
        $perpetto = Mage::helper('perpetto');

        if (!$perpetto->isActive()) {
            $headers['HTTP/1.1'] = '403 Forbidden';
            throw new Perpetto_Perpetto_Exception('Perpetto is not activated.');
        }

        $accountId = $request->getParam('account_id');
        $secret = $request->getParam('secret');

        if ($accountId != $perpetto->getAccountId() || $secret != $perpetto->getSecret()) {
            $headers['HTTP/1.1'] = '401 Unauthorized';
            throw new Perpetto_Perpetto_Exception('Account ID and secret do not match local configuration.');
        }
    }

}
