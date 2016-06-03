<?php

/**
 * Class Perpetto_Perpetto_Helper_Widget
 */
class Perpetto_Perpetto_Helper_Widget extends Mage_Core_Helper_Abstract
{

    /**
     * @var array
     */
    protected $_packageThemes = array();

    /**
     * Create slot widget
     *
     * @param Perpetto_Perpetto_Model_Slot $slot
     * @param string $packageTheme
     * @param int $sortOrder
     * @return $this
     */
    public function createSlotWidget(Perpetto_Perpetto_Model_Slot $slot, $packageTheme = null, $sortOrder = 10)
    {
        $widgetInstance = Mage::getModel('widget/widget_instance');

        $title = sprintf('Perpetto - %s - %s', $slot->getName(), $slot->getTitle());
        $widgetInstance->setTitle($title);

        $widgetInstance->setType('perpetto/recommendations');
        $widgetInstance->setPackageTheme($packageTheme);
        $widgetInstance->setStoreIds(array(0));
        $widgetInstance->setSortOrder($sortOrder);

        $slotId = (string)$slot->getPerpettoId();
        $params = array(
            'slot_id' => $slotId,
            'attr_class' => '',
        );
        $params = serialize($params);
        $widgetInstance->setWidgetParameters($params);

        $groups = $this->_getSlotPageGroups($slot);
        $widgetInstance->setPageGroups($groups);

        $widgetInstance->save();

        return $this;
    }

    /**
     * @param Perpetto_Perpetto_Model_Slot $slot
     * @param int $sortOrder
     * @return $this
     */
    public function createSlotWidgets(Perpetto_Perpetto_Model_Slot $slot, $sortOrder = 10)
    {
        $packageThemes = $this->_getActivePackageThemes();

        foreach ($packageThemes as $packageTheme) {
            if (!$this->slotWidgetExists($slot, $packageTheme)) {
                $this->createSlotWidget($slot, $packageTheme, $sortOrder);
            }
        }

        return $this;
    }

    /**
     * @param Perpetto_Perpetto_Model_Slot $slot
     * @return $this
     * @throws Exception
     */
    public function deleteSlotWidgets(Perpetto_Perpetto_Model_Slot $slot)
    {
        $perpettoId = $slot->getPerpettoId();

        $like = sprintf('%%"slot_id";s:%d:"%d"%%', strlen($perpettoId), $perpettoId);

        $collection = Mage::getModel('widget/widget_instance')->getCollection()
            ->addFieldToFilter('instance_type', 'perpetto/recommendations')
            ->addFieldToFilter('widget_parameters', array('like' => $like));

        foreach ($collection as $widget) {
            /** @var $widget Mage_Widget_Model_Widget_Instance */
            $widget->delete();
        }

        return $this;
    }

    /**
     * @param Perpetto_Perpetto_Model_Slot $slot
     * @param string $packageTheme
     * @return bool
     */
    public function slotWidgetExists(Perpetto_Perpetto_Model_Slot $slot, $packageTheme)
    {
        $perpettoId = $slot->getPerpettoId();

        $like = sprintf('%%"slot_id";s:%d:"%d"%%', strlen($perpettoId), $perpettoId);

        $collection = Mage::getModel('widget/widget_instance')->getCollection()
            ->addFieldToFilter('instance_type', 'perpetto/recommendations')
            ->addFieldToFilter('package_theme', $packageTheme)
            ->addFieldToFilter('widget_parameters', array('like' => $like));

        $result = $collection->count() > 0;

        return $result;
    }

    /**
     * Get all active themes
     *
     * @return array
     */
    protected function _getActivePackageThemes()
    {
        if (empty($this->_packageThemes)) {
            foreach (Mage::app()->getStores() as $store) {
                $design = Mage::getModel('core/design_package');
                $design->setStore($store);

                $package = $design->getPackageName();
                $theme = $design->getTheme('frontend');

                $packageTheme = sprintf('%s/%s', $package, $theme);

                $this->_packageThemes[$packageTheme] = $packageTheme;
            }
        }

        return $this->_packageThemes;
    }

    /**
     * Get page groups
     *
     * @param Perpetto_Perpetto_Model_Slot $slot
     * @return array
     */
    protected function _getSlotPageGroups(Perpetto_Perpetto_Model_Slot $slot)
    {
        $groups = array();

        switch ($slot->getPage()) {
            case 'product_page':
                $groups[] = array(
                    'page_group' => 'all_products',
                    'all_products' => array(
                        'page_id' => 0,
                        'layout_handle' => 'default,catalog_product_view',
                        'for' => 'all',
                        'block' => 'content',
                        'is_anchor_only' => null,
                        'product_type_id' => null,
                        'entities' => null
                    )
                );
                break;

            case 'home_page':
                $groups[] = array(
                    'page_group' => 'pages',
                    'pages' => array(
                        'page_id' => 0,
                        'layout_handle' => 'cms_index_index',
                        'for' => 'all',
                        'block' => 'content'
                    )
                );
                break;

            case 'category_page':
                $groups[] = array(
                    'page_group' => 'anchor_categories',
                    'anchor_categories' => array(
                        'page_id' => 0,
                        'layout_handle' => 'default,catalog_category_layered',
                        'for' => 'all',
                        'block' => 'content',
                        'is_anchor_only' => 1,
                        'product_type_id' => null,
                        'entities' => null
                    )
                );
                $groups[] = array(
                    'page_group' => 'notanchor_categories',
                    'notanchor_categories' => array(
                        'page_id' => 0,
                        'layout_handle' => 'default,catalog_category_layered',
                        'for' => 'all',
                        'block' => 'content',
                        'is_anchor_only' => 0,
                        'product_type_id' => null,
                        'entities' => null
                    )
                );
                break;

            case 'cart_page':
                $groups[] = array(
                    'page_group' => 'pages',
                    'pages' => array(
                        'page_id' => 0,
                        'layout_handle' => 'checkout_cart_index',
                        'for' => 'all',
                        'block' => 'content'
                    )
                );
                break;
        }

        return $groups;
    }

}
