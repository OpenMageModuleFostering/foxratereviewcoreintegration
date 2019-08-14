<?php

class Foxrate_Magento_ShopOrders {

    public $orders;

    public function getOrders($days)
    {
        $dateFrom = date('Y-m-d', strtotime("-$days day"));
        $data = array();

        // get stores from which we will take orders
        $storesId = $this->_getStoreIds();

        // get orders
        $orders = Mage::getModel('sales/order')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('store_id', array('in' => $storesId))
            ->addFieldToFilter('status', array('in' => array('complete')))
            ->addFieldToFilter('created_at', array('date' => true, 'from' => $dateFrom))
            ->load();

        // no orders
        if(empty($orders)) {
            $response['foxrate_auth_id'] = 1;
            $response['error'] = 'no_data_order';
        } else {
            foreach($orders as $order) {

                // get order data
                $orderData = $this->_getOrder($order);
                if(!empty($orderData)) {
                    $oneOrderData['order'] = $orderData;
                }

                // get customer data
                $customerData = $this->_getCustomer($order);
                if(!empty($customerData)) {
                    $oneOrderData['customer'] = $customerData;
                }

                // get products data
                $productsData = $this->_getProducts($order);

                if(!empty($productsData)) {
                    $oneOrderData['products'] = $productsData;
                }

                $data[] = $oneOrderData;

            }
        }

        return $data;
    }

    protected function _getStoreIds()
    {
        $currentStoreUrl = Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $stores = Mage::getModel('core/store')
            ->getCollection()
            ->load();

        // get stores with the same URL as the current store
        foreach($stores as $store) {
            $storeUrl = $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            if($storeUrl == $currentStoreUrl) {
                $storeIds[] = $store->getId();
            }
        }

        return $storeIds;
    }

    protected function _getProducts($order)
    {
        $data = array();

        $orderProducts = $order->getAllItems();

        $productIds = array();
        $productPrices = array();
        foreach($orderProducts as $orderProduct) {
            $productIds[] = $orderProduct->getProductId();
            $productPrices[$orderProduct->getProductId()] = $orderProduct->getRowTotal();
        }

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
            ->load();

        if(count($products) > 0) {
            foreach($products as $product) {

                $productPrice = $productPrices[$product->getId()];
                if($productPrice > 0) {

                    $categories = $this->_getProductCategories($product);

                    $image = $product->getImageUrl();
                    $url = $product->getProductUrl();

                    $productData['products_id'] = $product->getId();
                    $productData['product_sku'] = $product->getSku();
                    $productData['products_name'] = trim($product->getName());
                    $productData['products_image'] = $image;
                    $productData['products_url'] = $url;
                    $productData['final_price'] = $productPrice;
                    $productData['products_currency'] = $order->getOrderCurrencyCode();
                    $productData['categorie_name'] = trim($categories);

                    $parentIds = $this->getProductParentIds($product);
                    if (!empty($parentIds)) {
                        $productData['variant_ids'] = $this->getVariantIdsByParentIds($parentIds);
                    }

                    $data[] = $productData;
                }
            }
        }

        return $data;
    }

    protected function getStoreId() {

        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
        {
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        }
        elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
        {
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        }
        else // default level
        {
            $store_id = 0;
        }

        return $store_id;
    }

    protected function _getProductCategories($product)
    {
        $catCollection = $product->getCategoryCollection();

        if(count($catCollection) > 0) {
            $catIds = array();

            foreach($catCollection as $catColl) {
                $catIds[] = $catColl->getEntityId();
            }

            $cats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToFilter('entity_id', array('in' => $catIds))
                ->load();

            if(count($cats) > 0) {
                $catStr = '';

                foreach($cats as $cat){
                    $catStr .= $cat->getName() . '|';
                }

                $catStr = rtrim($catStr, '|');

                return $catStr;
            }
        }

        return false;
    }

    /**
     * Get category name for given product
     *
     * @param $oProduct
     * @param $sProductId
     * @param $sLang
     * @return string|false
     */
    protected function _getCategoryName($oProduct, $sProductId, $sLang)
    {
        if(empty($oProduct) || empty($sProductId) || empty($sLang)) {
            return false;
        }

        $sName = '';
        if(version_compare(_PS_VERSION_, '1.5.0.0') >= 0) {
            // for higher or equal presta versions than 1.5.0.0
            $aCategories = $oProduct->getProductCategoriesFull($sProductId, $sLang);
            if(!empty($aCategories)) {
                $aLastCat = end($aCategories);
                $sName = $aLastCat['name'];
            }
        } else {
            // for lower presta versions than 1.5.0.0
            $aCategoriesIds = $oProduct->getIndexedCategories($sProductId);
            if(!empty($aCategoriesIds[0])) {
                $sSelect = "SELECT name FROM `"._DB_PREFIX_."category_lang`
                            WHERE `id_category` = '".$aCategoriesIds[0]['id_category']."' AND id_lang = '".$sLang."'";
                // get category name
                $db = Db::getInstance();
                $aName = $db->getRow($sSelect);
                if(!empty($aName['name'])) {
                    $sName = $aName['name'];
                }
            }
        }
        return $sName;
    }

    protected function _getCustomer($order)
    {
        $data = array();

        $address = $order->getBillingAddress();

        // gender
        $genderValue = $address->getCustomerGender();
        $gender = '';
        if($genderValue == '123') {
            $gender = 'm'; // male
        } elseif($genderValue == '124') {
            $gender = 'w'; // female
        }

        $data['customers_id'] = $order->getCustomerId();
        $data['customers_city'] = $address->getCity();
        $data['customers_country'] = $address->getCountryId();
        $data['customers_email_address'] = $order->getCustomerEmail();
        $data['customers_gender'] = $gender;
        $data['customers_firstname'] = $order->getCustomerFirstname();
        $data['customers_lastname'] = $order->getCustomerLastname();

        return $data;
    }

    protected function _getOrder($order)
    {
        $data = array();

        $data['orders_id'] = $order->getId();
        $data['order_date'] = strtotime($order->getCreatedAt());
        $data['order_currency'] = $order->getOrderCurrencyCode();
        $data['order_language'] = strtolower(substr(Mage::getStoreConfig('general/locale/code', $order->getStoreId()), 0, 2));

        return $data;
    }

    protected function getProductParentIds($product)
    {
        $parentIds = Mage::getModel('catalog/product_type_configurable')
            ->getParentIdsByChild($product->getId());

        if ($product->isConfigurable()) {
            $parentIds[] = $product->getId();
        }
        return $parentIds;
    }

    protected function getVariantIdsByParentIds($parentIds)
    {
        $parents = Mage::getModel('catalog/product')
            ->getCollection()->addAttributeToFilter('entity_id', array('in' => $parentIds))
            ->load();

        $variantIds = array();
        foreach ($parents as $parent) {
            if ($parent->isConfigurable()) {
                $variantIds = array_merge($variantIds, $parent->getTypeInstance()->getUsedProductIds());
            }
        }
        return array_unique($variantIds);
    }

    /**
     * Check if it is Foxrate developer enviroment.
     * @return bool
     */
    public function isDevEnviroment()
    {
        return Mage::getIsDeveloperMode() == true && strpos($_SERVER['SERVER_NAME'], '.vm');
    }

    protected function pluginVersion() {
        return Mage::getConfig()->getNode()->modules->Foxrate_ReviewCoreIntegration->version;
    }

}