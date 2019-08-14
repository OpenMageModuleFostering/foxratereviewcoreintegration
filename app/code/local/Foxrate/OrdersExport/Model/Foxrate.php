<?php

class Foxrate_OrdersExport_Model_Foxrate
{
    /**
     * Foxrate API url
     */
    protected $foxrateUrl = 'http://foxrate.de';

    /**
     * Foxrate symfony2 api
     * @var string
     */
    protected $foxrateApiUrl  = 'https://api.foxrate.com';

    /**
     * Foxrate secret key
     */
    protected $_foxrateSecret = 'Foxrate';

    /**
     * Foxrate plugin version
     */
    protected $_foxratePluginVersion = '3.3.0';

    public function __construct()
    {
        $this->enableDevMode();
    }

    public function check($hash, $user, $pass)
    {
        $salt = md5(md5($user . $pass) . $this->_foxrateSecret);

        if($hash == $salt) {
            return true;
        }

        return false;
    }

    public function isUserExist($user, $pass)
    {
        $url = $this->getFoxrateUrl() . '/feedback_api/is_user_exist.php';

        $headers = $this->_getHeaders($user, $pass);

        $response = $this->_makeRequest($url, $headers);
        $responseObject = json_decode($response);

        if($responseObject->error != 'false') {
            if (Mage::getIsDeveloperMode() == true)
            {
                throw new Exception($response);
            }
            return false;
        }

        if($responseObject->user_exist == 'true') {
            return true;
        } else {
            return false;
        }
    }

    public function getFoxrateUrl()
    {
        return $this->foxrateUrl;
    }

    protected function _makeRequest($url, $headers = array(), $params = null)
    {
        $ch = curl_init();

        //$opts[CURLOPT_COOKIE] = 'XDEBUG_SESSION=phpstorm';
        $opts[CURLOPT_CONNECTTIMEOUT] = 10;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_TIMEOUT] = 60;
        $opts[CURLOPT_HTTPHEADER] = $headers;
        $opts[CURLOPT_URL] = $url;
        if(!is_null($params)){
            $opts[CURLOPT_POSTFIELDS] = $params;
        }

        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($result === false || $code != 200) {

            $en = curl_errno($ch);
            $e = curl_error($ch);
            curl_close($ch);
            $res = 'HTTP Code: '.$code.', cURL errno: '.$en.', cURL error: '.$e;
            return $res;
        }
        curl_close($ch);

        return $result;
    }

    protected function _getHeaders($user, $pass, $dataTypeRequest = 'JSON', $dataTypeResponse = 'JSON')
    {
        $headers[] = "FoxrateAuthLogin: ".$user;
        $headers[] = "FoxrateAuthPassword: ".$pass;
        $headers[] = "FoxrateResponseType: ".$dataTypeResponse;
        $headers[] = "FoxrateRequestType: ".$dataTypeRequest;

        return $headers;
    }

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
                    $productData['products_model'] = $product->getSku();
                    $productData['products_name'] = trim($product->getName());
                    $productData['products_image'] = $image;
                    $productData['products_url'] = $url;
                    $productData['final_price'] = $productPrice;
                    $productData['products_currency'] = $order->getOrderCurrencyCode();
                    $productData['categorie_name'] = trim($categories);

                    $data[] = $productData;
                }
            }
        }

        return $data;
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

    public function setShopModuleUrl($user, $pass)
    {
        $this->_checkExtensions();

        $shopModuleUrl = $this->getFoxrateUrl() . '/feedback_api/set_shopmodule_url.php';

        $headers = $this->_getHeaders($user, $pass, 'POST', 'JSON');
        $errors = array();

        $version = Mage::getVersion();
        if(method_exists('Mage', 'getEdition')) {
            $version .= ' ' . Mage::getEdition();
        }

        $params = array();
        $params['shop_system'] = 'Magento';
        $params['shop_system_version'] = $version;
        $params['plugin_version'] = $this->_foxratePluginVersion;

        // get stores with different urls
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $params['shop_module_url'] = Mage::getUrl('ordersexport', array('_store' => $store->getId()));
                    $sResult = $this->_makeRequest($shopModuleUrl, $headers, $params);
                    $oResult = json_decode($sResult);

                    if($oResult->error == 'true') {
                        $errors[] = $oResult->error_msg;
                    }
                }
            }
        }

        if(count($errors) > 0) {
            throw new Exception(implode('. ', $errors));
        }

        return $oResult;
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

    protected function _checkExtensions()
    {
        if(!function_exists('curl_version')) {
            $sMessage = $this->__('Required CURL extension is not installed');
            die($sMessage);
        }

        return true;
    }

    /**
     * Uploads order data to remote server
     * @param $sJson
     * @param $apiUsername
     * @param $apiPassword
     * @return string
     *
     * @todo Refactor isDevEnviroment part.
     */
    public function uploadOrders($sJson, $apiUsername, $apiPassword){

        if ($this->isDevEnviroment()) {
            return  array(
                "error" => 'false',
                "upload_id" => 1
            );;
        }

        $basicAPILogins = array();
        $basicAPILogins['BasicUser'] = $apiUsername;
        $basicAPILogins['BasicPass'] = $apiPassword;

        try
        {
            $uploadData= $this->queryFoxrateApi($basicAPILogins, "/v1/uploadUrl/generate.json?type=orders", "");
            $this->uploadDataHttpPut($uploadData->upload_url, $sJson);
            $this->queryFoxrateApi($basicAPILogins, "/v1/uploadUrl/{$uploadData->upload_id}.json", "");
        }
        catch(Exception $ex)
        {
            $status = array(
                "error" => 'true',
                "error_msg" => "{$ex->getMessage()}"
            );
            return json_encode($status);
        }

        $status = array(
            "error" => 'false',
            "upload_id" => $uploadData->upload_id
        );
        return json_encode($status);
    }

    /**
     * Queries the Foxrate API
     * @param $basicAPILogins
     * @param $apiUrlPartial
     * @param $postFields
     * @return mixed
     * @throws Exception
     */
    public function queryFoxrateApi($basicAPILogins, $apiUrlPartial, $postFields = NULL){
        $apiUrl = $this->getFoxrateAPIUrl() . $apiUrlPartial;
        $handle = curl_init();
        $opts = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $basicAPILogins['BasicUser'] .":". $basicAPILogins['BasicPass'],
            CURLOPT_URL => $apiUrl,
        );
        curl_setopt_array($handle, $opts);
        if(isset($postFields)){
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, $postFields);
        }

        $apiResponseRaw = curl_exec($handle);
        $curlError = curl_error($handle);
        $ApiResponse = json_decode($apiResponseRaw);

        if(!empty($curlError)){
            throw new Exception($curlError);
        }

        if($ApiResponse->status == 'error'){
            throw new Exception($apiResponseRaw);
        }

        return $ApiResponse;
    }

    /**
     * Uploads the string to specified url using CURL's HTTP PUT method
     * @param $uploadUrl
     * @param $sData
     * @throws Exception
     */
    private function uploadDataHttpPut($uploadUrl, $sData){
        $temp = tmpfile();
        fwrite($temp, $sData);
        rewind($temp);
        $handle = curl_init();

        $options = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_URL => $uploadUrl,
            CURLOPT_PUT => TRUE,
            CURLOPT_INFILE => $temp,
            CURLOPT_INFILESIZE => strlen($sData),
            CURLOPT_BINARYTRANSFER => TRUE,
            CURLOPT_RETURNTRANSFER => true
        );

        curl_setopt_array($handle, $options);
        $serverError = curl_exec($handle);
        $curlError = curl_error($handle);
        fclose($temp);

        if(!empty($curlError)){
            throw new Exception($curlError);
        }

        if(!empty($serverError)){
            throw new Exception($serverError);
        }
    }

    public function getFoxrateAPIUrl()
    {
        return $this->foxrateApiUrl;
    }

    /**
     * Enables Dev mode, if it is dev enviroment
     * @todo needs to refactored to correct location
     */
    public function enableDevMode()
    {
        if ($this->isDevEnviroment())
        {
            $this->foxrateApiUrl =  'https://api.foxrate.vm';

            $this->foxrateUrl = 'http://foxrate.vm/';
        }
    }

    /**
     * Check if it is Foxrate developer enviroment.
     * @return bool
     */
    public function isDevEnviroment()
    {
        return Mage::getIsDeveloperMode() == true && strpos($_SERVER['SERVER_NAME'], '.vm');
    }

}