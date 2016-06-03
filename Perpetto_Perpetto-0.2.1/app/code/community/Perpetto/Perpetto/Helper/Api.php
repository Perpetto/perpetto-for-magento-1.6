<?php

/**
 * Class Perpetto_Perpetto_Helper_Api
 */
class Perpetto_Perpetto_Helper_Api extends Mage_Core_Helper_Abstract
{
    const ERROR_PREFIX = 'Perpetto API Error: ';

    /**
     * @var array
     */
    protected $_clients = array();

    /**
     * @param null $accountId
     * @param null $secret
     * @return Perpetto_Client
     */
    protected function _getApiClient($accountId = null, $secret = null)
    {
        $accountId = is_null($accountId)
            ? Mage::helper('perpetto')->getAccountId()
            : $accountId;

        $secret = is_null($secret)
            ? Mage::helper('perpetto')->getSecret()
            : $secret;

        $key = sprintf('%s_%s', $accountId, $secret);

        if (!array_key_exists($key, $this->_clients)) {
            $api = new Perpetto_Client($accountId, $secret);
            $this->_clients[$key] = $api;
        }

        return $this->_clients[$key];
    }

    /**
     * @param null $accountId
     * @param null $secret
     * @return $this
     * @throws Perpetto_Perpetto_Exception
     */
    public function updateInfo($accountId = null, $secret = null)
    {
        $config = Mage::getModel('core/config');
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_ACTIVE_FLAG, 0, 'default', 0);
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_ACCOUNT_ID, $accountId, 'default', 0);
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_SECRET, $secret, 'default', 0);

        $api = $this->_getApiClient($accountId, $secret);

        try {
            $response = $api->loadInfo();
        } catch (Perpetto_Exception $e) {
            $code = $e->getCode();
            $message = self::ERROR_PREFIX . $e->getMessage();
            throw new Perpetto_Perpetto_Exception($message, $code, $e);
        }

        if ($response->hasError()) {
            $message = self::ERROR_PREFIX . $response->getError();
            throw new Perpetto_Perpetto_Exception($message);
        }

        $infoJSON = $response->getJSON();
        $storeId = $response->getData('store/id');
        $embedJsUrl = $response->getData('store/embedjs_uri');
        $accountRealId = $response->getData('store/account_id');

        if (!is_numeric($storeId)) {
            throw new Perpetto_Perpetto_Exception('Invalid store ID.');
        }

        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_ACCOUNT_REAL_ID, $accountRealId, 'default', 0);
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_ACTIVE_FLAG, 1, 'default', 0);
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_STORE_ID, $storeId, 'default', 0);
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_EMBED_JS_URI, $embedJsUrl, 'default', 0);
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_INFO_JSON, $infoJSON, 'default', 0);

        $data = array(
            'account_id' => $accountId,
            'secret' => $secret,
            'store_id' => $storeId,
            'embedjs_uri' => $embedJsUrl,
            'info_json' => $infoJSON,
        );

        Mage::dispatchEvent('perpetto_info_update_after', $data);

        return $this;
    }

    /**
     * @param null $accountId
     * @param null $secret
     * @return $this
     * @throws Perpetto_Perpetto_Exception
     */
    public function updateSlots($accountId = null, $secret = null)
    {
        $api = $this->_getApiClient($accountId, $secret);

        try {
            $response = $api->loadSlots();
        } catch (Perpetto_Exception $e) {
            $code = $e->getCode();
            $message = self::ERROR_PREFIX . $e->getMessage();
            throw new Perpetto_Perpetto_Exception($message, $code, $e);
        }

        if ($response->hasError()) {
            $message = self::ERROR_PREFIX . $response->getError();
            throw new Perpetto_Perpetto_Exception($message);
        }

        $slotsJSON = $response->getJSON();

        $config = Mage::getModel('core/config');
        $config->saveConfig(Perpetto_Perpetto_Helper_Data::XML_PATH_SLOTS_JSON, $slotsJSON, 'default', 0);

        // Get slots entities by Perpetto slot ID
        $slotsCollection = Mage::getResourceModel('perpetto/slot_collection');
        $slotsById = array();
        foreach ($slotsCollection as $slot) {
            $slotsById[$slot->getPerpettoId()] = $slot;
        }

        // Iterate slots
        $slots = array();
        $slotsData = (array)$response->getData('slots');
        foreach ($slotsData as $index => &$slotData) {
            $slotData = (array)$slotData;
            if (!array_key_exists('id', $slotData) || !is_numeric($slotData['id'])) {
                $message = sprintf('Invalid ID for slot index %d', $index);
                throw new Perpetto_Perpetto_Exception($message);
            }

            $perpettoId = $slotData['id'];
            unset($slotData['id']);

            $slotData['perpetto_id'] = $perpettoId;

            $slot = array_key_exists($slotData['perpetto_id'], $slotsById)
                ? $slotsById[$slotData['perpetto_id']]
                : Mage::getModel('perpetto/slot');

            $slot->addData($slotData);
            $slot->save();

            array_push($slots, $slot);
            unset($slotsById[$perpettoId]);
        }

        // Delete old slots
        foreach ($slotsById as $slot) {
            /** @var $slot Perpetto_Perpetto_Model_Slot */
            $slot->delete();
        }

        $data = array(
            'slots' => $slots
        );

        Mage::dispatchEvent('perpetto_slots_update_after', $data);

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return $this
     * @throws Perpetto_Perpetto_Exception
     */
    public function updateProduct(Mage_Catalog_Model_Product $product)
    {
        $imageUrl = Mage::helper('catalog/image')->init($product, 'image')->__toString();

        $helper = Mage::helper('perpetto/catalog');
        $categories = $product->getCategoryCollection();
        $paths = array();

        $defaultStoreId = Mage::app()->getDefaultStoreView()->getId();
        $breakId = Mage::app()->getStore($defaultStoreId)->getRootCategoryId();

        foreach ($categories as $category) {
            /** @var Mage_Catalog_Model_Category $category */
            $paths[] = $helper->getCategoryPath($category, $breakId);
        }

        $price = $helper->getProductPrice($product);

        $perpettoProduct = new Perpetto_Product();
        $perpettoProduct->setId($product->getId())
            ->setName($product->getName())
            ->setImage($imageUrl)
            ->setUrl($product->getProductUrl())
            ->setListPrice($price)
            ->setCurrency($product->getStore()->getCurrentCurrencyCode())
            ->setAvailability($product->isAvailable())
            ->setCategories($paths)
            ->setPrice($product->getFinalPrice())
            ->setBrand($product->getBrand())
            ->setSummary($product->getDescription());

        $api = $this->_getApiClient();

        try {
            $response = $api->updateProduct($perpettoProduct);
        } catch (Perpetto_Exception $e) {
            $code = $e->getCode();
            $message = self::ERROR_PREFIX . $e->getMessage();
            throw new Perpetto_Perpetto_Exception($message, $code, $e);
        }

        if ($response->hasError()) {
            $message = self::ERROR_PREFIX . $response->getError();
            throw new Perpetto_Perpetto_Exception($message);
        }

        return $this;
    }

}
