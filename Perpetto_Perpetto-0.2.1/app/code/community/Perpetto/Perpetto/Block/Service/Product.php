<?php

/**
 * Class Perpetto_Perpetto_Block_Service_Product
 */
class Perpetto_Perpetto_Block_Service_Product extends Mage_Core_Block_Template
{

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_product;

    /**
     * Init
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/service/product-page.phtml');
    }

    /**
     * Get current product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (is_null($this->_product)) {
            $this->_product = Mage::registry('current_product');
        }

        return $this->_product;
    }

    /**
     * Get current product image URL
     *
     * @return string
     */
    public function getProductImageUrl()
    {
        $url = Mage::helper('catalog/image')->init($this->getProduct(), 'image')->__toString();

        return $url;
    }

    /**
     * Get current categories path
     *
     * @return string
     */
    public function getCurrentCategoryPath()
    {
        $path = '';

        $category = Mage::registry('current_category');
        if ($category instanceof Mage_Catalog_Model_Category) {
            $helper = Mage::helper('perpetto/catalog');
            $path = $helper->getCategoryPath($category);
        }

        return $path;
    }

    /**
     * Get product categories paths
     *
     * @return string
     */
    public function getProductCategoriesPaths()
    {
        $result = '';

        $product = $this->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            $helper = Mage::helper('perpetto/catalog');
            $paths = $helper->getProductCategoryPaths($product);
            $result = implode(',', $paths);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getProductPrice()
    {
        $product = $this->getProduct();
        return Mage::helper('perpetto/catalog')->getProductPrice($product);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $product = $this->getProduct();
        if (!$product instanceof Mage_Catalog_Model_Product) {
            return '';
        }

        return parent::_toHtml();
    }

}
