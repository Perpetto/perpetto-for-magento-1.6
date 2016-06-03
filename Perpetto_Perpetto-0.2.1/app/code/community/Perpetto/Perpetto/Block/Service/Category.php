<?php

/**
 * Class Perpetto_Perpetto_Block_Service_Category
 */
class Perpetto_Perpetto_Block_Service_Category extends Mage_Core_Block_Template
{
    /**
     * Init
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('perpetto/service/category-page.phtml');
    }

    /**
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
}
