<?php

/**
 * Class Perpetto_Perpetto_OrdersController
 */
class Perpetto_Perpetto_OrdersController extends Perpetto_Perpetto_Controller_Action
{
    /**
     * Slots action
     */
    public function infoAction()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();

        $core = Mage::helper('core');

        $headers = array();

        try {
            $this->_validateRequest();

            $startTime = $request->getParam('startTime');
            $endTime = $request->getParam('endTime');
            $count = 0;
            $firstOrderId = 0;
            $lastOrderId = 0;

            /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
            $collection = Mage::getModel('sales/order')->getCollection();
            if (!empty($startTime)) {
                $startTime = gmdate('Y-m-d H:i:s', $startTime);
            }
            if (!empty($endTime)) {
                $endTime = gmdate('Y-m-d H:i:s', $endTime);
            }

            if (!empty($startTime)) {
                $collection->addFieldToFilter('created_at', array('gteq' => $startTime));
            }

            if (!empty($endTime)) {
                $collection->addFieldToFilter('created_at', array('lteq' => $endTime));
            }

            if ($collection->count() > 0) {
                /** @var Mage_Sales_Model_Order $firstOrder */
                $firstOrder = $collection->getFirstItem();
                $firstOrderId = $firstOrder->getId();
                if (empty($startTime)) {
                    $startTime = $firstOrder->getCreatedAt();
                }

                /** @var Mage_Sales_Model_Order $lastOrder */
                $lastOrder = $collection->getLastItem();
                $lastOrderId = $lastOrder->getId();
                if (empty($endTime)) {
                    $endTime = $lastOrder->getCreatedAt();
                }

                $count = $collection->count();
            }

            $data = array(
                'info' => array(
                    'start_time' => strtotime($startTime . ' GMT'),
                    'end_time' => strtotime($endTime . ' GMT'),
                    'orders_count' => $count,
                    'first_order_id' => $firstOrderId,
                    'last_order_id' => $lastOrderId
                )
            );

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

    /**
     * Slots action
     */
    public function listAction()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();

        $core = Mage::helper('core');

        $headers = array();

        try {
            $this->_validateRequest();

            $pageSize = (int) $request->getParam('pageSize');
            if ($pageSize == 0) {
                throw new Perpetto_Perpetto_Exception('Invalid request, missing parameter pageSize.');
            }

            $firstOrderId = (int) $request->getParam('firstOrderId');
            $lastOrderId = (int) $request->getParam('lastOrderId');

            /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
            $collection = Mage::getModel('sales/order')->getCollection();
            if ($firstOrderId > 0) {
                $collection->addFieldToFilter('entity_id', array('gteq' => $firstOrderId));
            } else if ($lastOrderId > 0) {
                $collection->addFieldToFilter('entity_id', array('gt' => $lastOrderId));
            }

            $collection->setPageSize($pageSize);
            $data = array(
                'orders' => array(),
            );

            $orderIds = $collection->getAllIds();
            $orderItems = Mage::getModel('sales/order_item')->getCollection();
            $orderItems->addFieldToFilter('order_id', array('in' => $orderIds));
            $itemData = array();
            foreach ($orderItems as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                /** @var Mage_Catalog_Model_Product $product */
                $product = Mage::getModel('catalog/product')
                    ->setStoreId($item->getOrder()->getStoreId())
                    ->load($item->getProduct()->getId());

                $url = Mage::helper('catalog/image')->init($product, 'image')->__toString();
                $price = Mage::helper('perpetto/catalog')->getProductPrice($product);
                $finalPrice = $product->getFinalPrice();

                $paths = Mage::helper('perpetto/catalog')->getProductCategoryPaths($product);
                $categories = implode(',', $paths);

                $productDetails = array(
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'image' => $url,
                    'url' => $product->getUrlInStore(),
                    'listprice' => $price,
                    'currency' => Mage::app()->getStore($item->getOrder()->getStoreId())->getCurrentCurrencyCode(),
                    'availability' => (int)$product->getIsSalable(),
                    'categories' => $categories,
                    'category' => '',
                    'brand' => $product->getBrand(),
                    'tags' => '',
                    'summary' => $product->getDescription(),
                );

                if ($price != $finalPrice) {
                    $productDetails['price'] = $finalPrice;
                }

                /** @var Mage_Sales_Model_Order_Item $item */
                $itemData[$item->getOrderId()][] = array(
                    'id' => $item->getId(),
                    'quantity' => $item->getQtyOrdered(),
                    'price' => $item->getPrice(),
                    'itemTotal' => $item->getRowTotal(),
                    'product' => $productDetails
                );
            }

            foreach($collection as $order) {
                /** @var Mage_Sales_Model_Order $order */
                $data['orders'][] = array(
                    'cartid' => $order->getId(),
                    'total' => $order->getGrandTotal(),
                    'currency' => $order->getOrderCurrencyCode(),
                    'created_at' => strtotime($order->getCreatedAt() . ' GMT'),
                    'paid' => ($order->getBaseTotalDue() > 0),
                    'profile' => array(
                        'email' => $order->getCustomerEmail(),
                        'firstname' => $order->getCustomerFirstname(),
                        'lastname' => $order->getCustomerLastname(),
                    ),
                    'items' => $itemData[$order->getId()],
                );
            }


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
