<?php

class Foxrate_Magento_ShopOrders {

    public $orders;

    public function getOrders($iDays)
    {
        $sNow = date('Y-m-d H:i:s');

        $sSelect = "SELECT
						o.id_order as orders_id,
						o.date_add as order_date,
						c.iso_code as order_currency,
						l.iso_code as order_language,
						o.id_customer as customers_id,
						a.city as customers_city,
						a.postcode as customers_postcode,
						";
        if(version_compare(_PS_VERSION_, '1.5.0.0') >= 0) {
            $sSelect .= "s.name as customers_state,";
        }
        $sSelect .= "country.name as customers_country,
						a.phone as customers_telephone,
						cust.email as customers_email_address,
						cust.id_gender as customers_gender,
						cust.firstname as customers_firstname,
						cust.lastname as customers_lastname,
						o.id_lang as order_language_id
					FROM
						"._DB_PREFIX_."orders o
				    LEFT JOIN
				        "._DB_PREFIX_."currency c ON c.id_currency = o.id_currency
				    LEFT JOIN
				        "._DB_PREFIX_."lang l ON l.id_lang = o.id_lang
				    LEFT JOIN
				        "._DB_PREFIX_."customer cust ON cust.id_customer = o.id_customer
				    LEFT JOIN
				        "._DB_PREFIX_."address a ON a.id_address = o.id_address_invoice
				        ";
        if(version_compare(_PS_VERSION_, '1.5.0.0') >= 0) {
            $sSelect .= " LEFT JOIN
				        "._DB_PREFIX_."state s ON s.id_state = a.id_state ";
        }
        $sSelect .= "LEFT JOIN
				        "._DB_PREFIX_."country_lang country ON (country.id_country = a.id_country AND country.id_lang = o.id_lang)
					WHERE
						o.date_add BETWEEN DATE_SUB('".$sNow."', INTERVAL ".$iDays." day) AND '".$sNow."'";

        $db = Db::getInstance();
        $aOrders = $db->ExecuteS($sSelect);

        // show error to screen
        $err = $db->getMsgError($sSelect);
        if(!empty($err)) {
            die($err);
        }

        // no orders
        if(empty($aOrders)) {
            $aResponse['foxrate_auth_id'] = 1;
            $aResponse['error'] = 'no_data_order';
        } else {
            foreach($aOrders as $aOrder) {
                // get customer
                $customer = $this->_getCustomer($aOrder);

                // get order
                $order = $this->_getOrder($aOrder);

                // get products
                $products = $this->_getProducts($aOrder);

                $aData = array();
                $aData['customer'] = $customer;
                $aData['order'] = $order;
                $aData['products'] = $products;

                $aResponse[] = $aData;
            }
        }

        return $aResponse;
    }

    protected function _getProducts($aOrder)
    {
        $aProducts = array();

        $sSelect = "SELECT
                        d.product_id as products_id,
                        d.product_name as products_name,
                        d.product_price as final_price,
                        ";
        if(version_compare(_PS_VERSION_, '1.5.0.0') >= 0) {
            $sSelect .= "d.product_ean13 as products_ean,";
        }
        $sSelect .= "   pl.link_rewrite
                    FROM
                        "._DB_PREFIX_."order_detail d
                    LEFT JOIN
                        "._DB_PREFIX_."product_lang pl ON (pl.id_product = d.product_id AND pl.id_lang = '".$aOrder['order_language_id']."')
                    WHERE
                        d.id_order = '".$aOrder['orders_id']."'";

        $aData = Db::getInstance()->ExecuteS($sSelect);

        $oLink = new Link();

        if(!empty($aData)) {
            foreach($aData as $aProduct) {
                $aProduct['category_name'] = '';

                $oProduct = new Product($aProduct['products_id']);

                $sCategoryName = $this->_getCategoryName($oProduct, $aProduct['products_id'], $aOrder['order_language_id']);
                if(!empty($sCategoryName)) {
                    $aProduct['category_name'] = $sCategoryName;
                }

                // image
                $sImage = '';
                $aImages = $oProduct->getImages($aOrder['order_language_id']);
                if(!empty($aImages[0])) {
                    $aProduct['products_image'] = $oLink->getImageLink($aProduct['link_rewrite'], $aImages[0]['id_image']);
                }

                // model
                $aProduct['products_model'] = ''; // there's no such in presta

                // url
                $aProduct['products_url'] = $oLink->getProductLink($aProduct['products_id']);

                unset($aProduct['link_rewrite']);

                $aProducts[] = $aProduct;
            }
        }

        return $aProducts;
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

    protected function _getCustomer($aOrder)
    {
        $customer = array();

        $sGender = '';
        if($aOrder['customers_gender'] == 2) {
            $sGender = 'f';
        } else {
            $sGender = 'm';
        }

        $customer['customers_id'] = $aOrder['customers_id'];
        $customer['customers_city'] = $aOrder['customers_city'];
        $customer['customers_country'] = $aOrder['customers_country'];
        $customer['customers_email_address'] = $aOrder['customers_email_address'];
        $customer['customers_gender'] = $sGender;
        $customer['customers_firstname'] = $aOrder['customers_firstname'];
        $customer['customers_lastname'] = $aOrder['customers_lastname'];

        return $customer;
    }

    protected function _getOrder($aOrder)
    {
        $order = array();

        $sGender = '';
        if($aOrder['customers_gender'] == 2) {
            $sGender = 'f';
        } elseif($aOrder['customers_gender'] == 1) {
            $sGender = 'm';
        }

        $order['orders_id'] = $aOrder['orders_id'];
        $order['order_date'] = strtotime($aOrder['order_date']);
        $order['order_currency'] = $aOrder['order_currency'];
        $order['order_language'] = $aOrder['order_language'];

        return $order;
    }
}