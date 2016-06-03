<?php

/**
 * Class Perpetto_Perpetto_Helper_Catalog
 */
class Perpetto_Perpetto_Helper_Catalog extends Mage_Core_Helper_Abstract
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'perpetto';

    /**
     * Get category name from cache by ID
     *
     * @param $categoryId
     * @return string
     */
    public function getCategoryNameById($categoryId)
    {
        $categoryId = (int)$categoryId;
        $storeId = Mage::app()->getStore()->getId();

        $cache = Mage::app()->getCache();
        $categoryNameCacheKey = sprintf('perpetto_category_name_%d_%d', $storeId, $categoryId);
        $categoryName = $cache->load($categoryNameCacheKey);

        if (!$categoryName) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if ($category->getId()) {
                $categoryName = $category->getName();
                $tags = array(self::CACHE_TAG, Mage_Catalog_Model_Category::CACHE_TAG);
                $cache->save($categoryNameCacheKey, $categoryName, $tags);
            }
        }

        return $categoryName;
    }

    /**
     * Get category path
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int $breakId
     * @return string
     */
    public function getCategoryPath(Mage_Catalog_Model_Category $category, $breakId = null)
    {
        $categoryNames = array();
        $pathIds = $category->getPathIds();

        rsort($pathIds);

        foreach ($pathIds as $categoryId) {
            if ($categoryId == Mage::app()->getStore()->getRootCategoryId() || $breakId == $categoryId) {
                break;
            }

            $categoryNames[] = $this->getCategoryNameById($categoryId);
        }

        $categoryNames = array_reverse($categoryNames);

        $path = implode('/', $categoryNames);

        return $path;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return array
     * @throws Exception
     */
    public function getProductCategoryPaths(Mage_Catalog_Model_Product $product)
    {
        $storeId = Mage::app()->getStore()->getId();
        $productId = $product->getId();

        $pathsCacheKey = sprintf('perpetto_product_paths_%d_%d', $storeId, $productId);

        $cache = Mage::app()->getCache();
        $pathsData = $cache->load($pathsCacheKey);

        if (!$pathsData) {
            $paths = array();

            $categories = $product->getCategoryCollection();
            foreach ($categories as $category) {
                /** @var Mage_Catalog_Model_Category $category */
                $paths[] = $this->getCategoryPath($category);

                $pathsData = serialize($paths);
                $tags = array(self::CACHE_TAG, Mage_Catalog_Model_Product::CACHE_TAG);
                $category->save($pathsCacheKey, $pathsData, $tags);
            }
        } else {
            $paths = (array)unserialize($pathsData);
        }

        return $paths;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return number
     */
    public function getProductPrice($product)
    {
        $price = (string)$product->getPrice();

        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE
            && $product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
        ) {
            $priceModel = $product->getPriceModel();
            /** @var $priceModel Mage_Bundle_Model_Product_Price */

            $tax = Mage::helper('tax');

            if ($tax->displayPriceIncludingTax()) {
                $minPrice = $priceModel->getTotalPrices($product, 'min', true, false);
            } else {
                $minPrice = $priceModel->getTotalPrices($product, 'min', null, false);
            }

            $price = $minPrice;
        }

        return $price;
    }

}
