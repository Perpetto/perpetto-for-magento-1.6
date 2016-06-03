<?php

/**
 * Class Perpetto_Perpetto_Helper_Url
 */
class Perpetto_Perpetto_Helper_Url extends Mage_Core_Helper_Abstract
{
    const BASE_URL = 'https://admin.perpetto.com';

    /**
     * @return string
     */
    public function getSignInUrl()
    {
        $url = self::BASE_URL . '/#/sign_in';

        return $url;
    }

    /**
     * @return string
     */
    public function getSignUpUrl()
    {
        $url = self::BASE_URL . '/#/sign_up/start/plugin_magento';

        return $url;
    }

    /**
     * @return string
     */
    public function getBillingUrl()
    {
        $url = '';

        $helper = Mage::helper('perpetto');
        if ($helper->isActive()) {
            $accountId = $helper->getAccountRealId();

            $path = sprintf('/#/account/%s/settings/billing', $accountId);
            $url = self::BASE_URL . $path;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getContactsUrl()
    {
        $url = '';

        $helper = Mage::helper('perpetto');
        if ($helper->isActive()) {
            $accountId = $helper->getAccountRealId();
            $storeId = $helper->getStoreId();

            $path = sprintf('/#/account/%s/store/details/%d/settings/intercom', $accountId, $storeId);
            $url = self::BASE_URL . $path;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getDashboardUrl()
    {
        $url = '';

        $helper = Mage::helper('perpetto');
        if ($helper->isActive()) {
            $accountId = $helper->getAccountRealId();
            $storeId = $helper->getStoreId();

            $path = sprintf('/#/account/%s/dashboard/%d/recommendations/', $accountId, $storeId);
            $url = self::BASE_URL . $path;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getRecommendationsUrl()
    {
        $url = '';

        $helper = Mage::helper('perpetto');
        if ($helper->isActive()) {
            $accountId = $helper->getAccountRealId();
            $storeId = $helper->getStoreId();

            $path = sprintf('/#/account/%s/recs/%d/list', $accountId, $storeId);
            $url = self::BASE_URL . $path;
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getRandomProductPreviewUrl()
    {
        $storeId = 0;
        foreach (Mage::app()->getWebsites(true) as $website) {
            /** @var Mage_Core_Model_Website $website */
            $storeId = $website->getDefaultStore()->getId();
            if ($website->getIsDefault()) {
                break;
            }
        }

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addStoreFilter($storeId);
        $collection->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());
        $collection->getSelect()->orderRand('entity_id');
        $collection->getSelect()->limit('1');

        $url = '';

        if ($collection->count() > 0) {
            $product = $collection->getFirstItem();
            /** @var $product Mage_Catalog_Model_Product */

            $url = $product->getUrlModel()->getUrl($product, array('_query' => 'ptto_env=PREVIEW'));
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $url;
    }

}
