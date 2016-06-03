<?php

/**
 * Class Perpetto_Perpetto_Helper_Data
 */
class Perpetto_Perpetto_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ACTIVE_FLAG = 'perpetto/general/active';
    const XML_PATH_ACCOUNT_ID = 'perpetto/general/account_id';
    const XML_PATH_ACCOUNT_REAL_ID = 'perpetto/general/account_real_id'; // This is lame
    const XML_PATH_SECRET = 'perpetto/general/secret';
    const XML_PATH_STORE_ID = 'perpetto/general/store_id';
    const XML_PATH_EMBED_JS_URI = 'perpetto/general/embed_js_uri';
    const XML_PATH_INFO_JSON = 'perpetto/general/info_json';
    const XML_PATH_SLOTS_JSON = 'perpetto/general/slots_json';

    /**
     * @return string
     */
    public function getAccountId()
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_ID);

        return $accountId;
    }

    /**
     * @return string
     */
    public function getAccountRealId()
    {
        $accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT_REAL_ID);

        return $accountId;
    }

    /**
     * @return mixed
     */
    public function getAccountInfo()
    {
        $json = Mage::getStoreConfig(self::XML_PATH_INFO_JSON);
        $info = json_decode($json, JSON_OBJECT_AS_ARRAY);

        return $info;
    }

    /**
     * @return string
     */
    public function getEmbedJsUrl()
    {
        $jsUrl = Mage::getStoreConfig(self::XML_PATH_EMBED_JS_URI);

        return $jsUrl;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        $secret = Mage::getStoreConfig(self::XML_PATH_SECRET);

        return $secret;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        $storeId = Mage::getStoreConfig(self::XML_PATH_STORE_ID);

        return $storeId;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $isActive = (bool)Mage::getStoreConfig(self::XML_PATH_ACTIVE_FLAG);

        return $isActive;
    }

    /**
     * @return bool
     */
    public function isTrial()
    {
        $info = $this->getAccountInfo();
        $store = $info['data']['store'];
        $isTrial = array_key_exists('trial_days_left', $store) && !empty($store['trial_days_left']);

        return $isTrial;
    }

    /**
     * @return int
     */
    public function getTrialDaysLeft()
    {
        $days = 0;

        $info = $this->getAccountInfo();
        if (array_key_exists('trial_days_left', $info)) {
            $days = (int)$info['trial_days_left'];
            $days = max(0, $days);
        }

        return $days;
    }
}
