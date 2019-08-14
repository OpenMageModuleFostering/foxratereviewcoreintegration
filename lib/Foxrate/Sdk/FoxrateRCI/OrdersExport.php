<?php

class Foxrate_Sdk_FoxrateRci_OrdersExport
{
    /**
     * Foxrate API Url
     */
    protected $_sFoxrateAPIUrl = 'http://fb.foxrate.de/';

    protected $_FoxrateAPI2Url = 'https://api.foxrate.com';

    /**
     * Foxrate plugin version
     */
    protected $_sFoxratePluginVersion = '3.3.3';

    /**
     * Foxrate secret key
     */
    protected $_sFoxrateSecret = 'Foxrate';

    public function __construct()
    {
        if ($this->isDevelopementEnviroment())
        {
           $this->enableDevelopmentMode();
        }
    }

	/**
	 * The GET parameter check will have the value of a secret key which MUST 
	 * be checked against the FOXRATE user data stored by the plugin
	 *
     * @param string $sHash
	 * @param string $sUser
	 * @param string $sPassword
	 * @return boolean
	 */
	public function check($sHash, $sUser, $sPassword)
	{
		$sSalt = md5(md5($sUser . $sPassword) . $this->_sFoxrateSecret);

		if($sHash == $sSalt) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Get orders for last given days
	 * 
	 * @param integer $iDays
	 * @return array
	 */
	public function getOrders($iDays = 30)
	{
		$sShopId = oxConfig::getInstance()->getShopID();
		$aResponse = array();
        $sNow = date('Y-m-d H:i:s');
		
		$sSelect = "SELECT 
						oxid,
						oxlang 
					FROM 
						oxorder 
					WHERE 
						oxshopid = '$sShopId' AND 
						oxorderdate BETWEEN DATE_SUB('".$sNow."', INTERVAL ".$iDays." day) AND '".$sNow."'";
//		$aOrders = oxDb::getDb(true)->GetAll($sSelect);
        $aOrders = oxDb::getDb( oxDb::FETCH_MODE_ASSOC)->GetAll($sSelect);

		// no orders
		if(empty($aOrders)) {
			$aResponse['foxrate_auth_id'] = 1;
			$aResponse['error'] = 'no_data_order';
		} else {
			foreach($aOrders as $aOrder) {

                // set base language the one from order
                oxLang::getInstance()->setBaseLanguage($aOrder['oxlang']);

				$aData = array();
				
				// we will need to get all info in the language that order was made for
				$sOrderLangTag = oxLang::getInstance()->getLanguageTag($aOrder['oxlang']);
				
				// get customer
				$aCustomerData = $this->_getCustomer($aOrder['oxid'], $sOrderLangTag);
				if(!empty($aCustomerData)) {
					$aData['customer'] = $aCustomerData;
				}
				
				// get order
				$aOrderData = $this->_getOrder($aOrder['oxid']);
				if(!empty($aOrderData)) {
					$aData['order'] = $aOrderData;
				}
				
				// get products
				$aProducts = $this->_getProducts($aOrder['oxid'], $aOrder['oxlang']);
				if(!empty($aProducts)) {
					$aData['products'] = $aProducts;
				}
				
				$aResponse[] = $aData;
			}
		}
		
		return $aResponse;
	}
	
	
	/**
	 * Get customer info by given order id
	 * 
	 * @param string $sOrderId
	 * @param string $sLangTag
	 * @return mixed
	 */
	protected function _getCustomer($sOrderId, $sLangTag = '')
	{
		$sSelect = "SELECT 
						u.oxid as customers_id,
						o.oxbillcity as customers_city,
						c.oxtitle$sLangTag as customers_country,
						o.oxbillemail as customers_email_address,
						o.oxbillsal as customers_gender,
						o.oxbillfname as customers_firstname,
						o.oxbilllname as customers_lastname 
					FROM 
						oxorder o 
					LEFT JOIN 
						oxuser u ON u.oxid = o.oxuserid 
					LEFT JOIN 
						oxstates s ON s.oxid = o.oxbillstateid 
					LEFT JOIN 
						oxcountry c ON c.oxid = o.oxbillcountryid 
					WHERE 
						o.oxid = '$sOrderId'";
		$aCustomer = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->GetRow($sSelect);

		if(!empty($aCustomer)) {
			
			// change salutation
			if($aCustomer['customers_gender'] == 'MRS') {
				$aCustomer['customers_gender'] = 'w'; // female
			} else {
				$aCustomer['customers_gender'] = 'm'; // male
			}
			
			return $aCustomer;
		} else {
			return false;
		}
	}
	
	/**
	 * Get order info by given order id
	 * 
	 * @param string $sOrderId
	 * @return mixed
	 */
	protected function _getOrder($sOrderId)
	{
		$sSelect = "SELECT 
						oxordernr as orders_id,
						oxorderdate as order_date,
						oxcurrency as order_currency,
						oxlang as order_language  
					FROM 
						oxorder 
					WHERE 
						oxid = '$sOrderId'";
		$aOrder = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->GetRow($sSelect);
		
		if(!empty($aOrder)) {
			
			// change language
			if(!empty($aOrder['order_language'])) {
				$aOrder['order_language'] = oxLang::getInstance()->getLanguageAbbr($aOrder['order_language']);
			}

            // convert date format
            if(!empty($aOrder['order_date'])) {
                $aOrder['order_date'] = strtotime($aOrder['order_date']);
            }
			
			return $aOrder;
		} else {
			return false;
		}
	}
	
	/**
	 * Get order products by given order id
	 * 
	 * @param string $sOrderId
	 * @param integer $iLang
	 * @return mixed
	 */
	protected function _getProducts($sOrderId, $iLang = 0)
	{
		$sLangTag = oxLang::getInstance()->getLanguageTag($iLang);
		
		$sSelect = "SELECT 
						oa.oxartid as products_id,
						oa.oxartnum as products_model,
						oa.oxtitle as products_name,
						oa.oxprice as final_price,
						o.oxcurrency as products_currency,
						a.oxean as products_ean,
						1 as category_name,
						1 as products_image,
						1 as products_url
					FROM 
						oxorderarticles oa 
					LEFT JOIN 
						oxorder o ON o.oxid = '$sOrderId'
					LEFT JOIN 
						oxarticles a ON a.oxid = oa.oxartid 
					LEFT JOIN 
						oxvendor v ON v.oxid = a.oxvendorid
					WHERE 
						oa.oxorderid = '$sOrderId'";
		$aProducts = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->GetAll($sSelect);

		if(!empty($aProducts)) {
			foreach($aProducts as &$aProduct) {
				
				$oProduct = oxNew('oxarticle');
				$oProduct->load($aProduct['products_id']);
				
				// get category_name
				$oMainCategory = $oProduct->getCategory();
				if($oMainCategory->oxcategories__oxparentid->value == 'oxrootid') {
					
					// it's a root category - just put it to category_name
					//$sTitleField = 'oxcategories__oxtitle' . $sLangTag;
					$aProduct['category_name'] = $oMainCategory->oxcategories__oxtitle->value;
				} else {
					
					$sCategoryView = getViewName('oxcategories', $iLang);
					
					// get category tree
					$sSelectCategoryTree = "SELECT 
												oxtitle
											FROM 
												$sCategoryView 
											WHERE 
												oxrootid = '".$oMainCategory->oxcategories__oxrootid->value."' AND 
												oxleft <= '".$oMainCategory->oxcategories__oxleft->value."' AND 
												oxright >= '".$oMainCategory->oxcategories__oxright->value."'";
					$aCategoryTree = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->GetCol($sSelectCategoryTree);
					$aCategoryTree = array_reverse($aCategoryTree);
					
					$aProduct['category_name'] = implode('|', $aCategoryTree);
					$aProduct['category_name'] = rtrim($aProduct['category_name'], '|');
				}
			
				// get products_image
				$aProduct['products_image'] = $oProduct->getMasterZoomPictureUrl(1);
				
				// get products_url
				$aProduct['products_url'] = $oProduct->getLink();
			}
		}
		
		return $aProducts;
	}
	
	/**
	 * Check if user exists in Foxrate API
	 * 
	 * @param string $sUser
	 * @param string $sPass
	 * @param string $sDataType
	 * 
	 * @return boolean
	 */
	public function isUserExist($sUser, $sPass)
	{
		$sUrl = $this->_sFoxrateAPIUrl . 'feedback_api/is_user_exist.php';

        $aHeaders = $this->_getHeaders($sUser, $sPass);
		
		$sResponse = $this->_makeRequest($sUrl, $aHeaders);
		$oResponse = json_decode($sResponse);

		if($oResponse->error != 'false') {
			return false;
		}

		if($oResponse->user_exist == 'true') {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Make a request via CURL with given headers and params to specific URL
	 * 
	 * @param string $sUrl
	 * @param array $aHeaders
	 * @param array $aParams
	 * 
	 * @return mixed
	 */
	protected function _makeRequest($sUrl, $aHeaders = array(), $aParams = null)
	{
		$ch = curl_init();

		$opts[CURLOPT_CONNECTTIMEOUT] = 10;
		$opts[CURLOPT_RETURNTRANSFER] = true;
		$opts[CURLOPT_TIMEOUT] = 60;
		$opts[CURLOPT_HTTPHEADER] = $aHeaders;
		$opts[CURLOPT_URL] = $sUrl;
		if(!is_null($aParams)){
			$opts[CURLOPT_POSTFIELDS] = $aParams;
		}

		curl_setopt_array($ch, $opts);
		$result = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($result === false || $code != 200) {

			$en = curl_errno($ch);
			$e = curl_error($ch);
			curl_close($ch);
			return 'HTTP Code: '.$code.', cURL errno: '.$en.', cURL error: '.$e;
		}
		curl_close($ch);

		return $result;
	}

    /**
     * Inform Foxrate of the Foxrate export script
     *
     * @param string $sUser
     * @param string $sPass
     * @param string $sAPIDataType
     * @return mixed
     */
    public function setShopModuleUrl($sUser, $sPass)
    {
        $sShopModuleUrl = $this->_sFoxrateAPIUrl . 'feedback_api/set_shopmodule_url.php';
        $aHeaders = $this->_getHeaders($sUser, $sPass, 'POST', 'JSON');
        $blResult = true;

        $aParams = array();

        $oShopList = oxNew('oxshoplist');
        $oShopList->selectString("select * from ".getViewName('oxshops')."");

        if(!empty($oShopList)) {
            foreach($oShopList as $oShop) {

                // get shop url
                $sShopUrl = oxConfig::getInstance()->getShopConfVar('sMallShopURL', $oShop->oxshops__oxid->value);
                if(empty($sShopUrl)) {
                    $sShopUrl = oxConfig::getInstance()->getConfigParam( 'sShopURL' );
                }
                $sShopUrl = oxUtils::getInstance()->checkUrlEndingSlash($sShopUrl);

                $aParams['shop_module_url'] = $sShopUrl . 'index.php?cl=foxrate_orders';
                $aParams['shop_system'] = 'OXID';
                $aParams['shop_system_version'] = $oShop->oxshops__oxedition->value . ' ' . $oShop->oxshops__oxversion->value;
                $aParams['plugin_version'] = $this->_sFoxratePluginVersion;

                $sResult = $this->_makeRequest($sShopModuleUrl, $aHeaders, $aParams);
                $oResult = json_decode($sResult);

                if($oResult->error == 'true') {
                    $blResult = false;
                }
            }
        }

        return $blResult;
    }

    /**
     * Create headers by given data
     *
     * @param string $sUser
     * @param string $sPass
     * @param string $sDataTypeRequest
     * @param string $sDataTypeResponse
     * @return array
     */
    protected function _getHeaders($sUser, $sPass, $sDataTypeRequest = 'JSON', $sDataTypeResponse = 'JSON')
    {
        $aHeaders[] = "FoxrateAuthLogin: ".$sUser;
        $aHeaders[] = "FoxrateAuthPassword: ".$sPass;
        $aHeaders[] = "FoxrateResponseType: ".$sDataTypeResponse;
        $aHeaders[] = "FoxrateRequestType: ".$sDataTypeRequest;

        return $aHeaders;
    }
    /**
     * Uploads order data to remote server
     * @param $sJson
     * @param $apiUsername
     * @param $apiPassword
     * @return string
     */
    public function uploadOrders($sJson, $apiUsername, $apiPassword){
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
        $apiUrl = $this->_FoxrateAPI2Url . $apiUrlPartial;
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

    private function isDevelopementEnviroment()
    {
        return isset($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] == 'development';
    }

    private function enableDevelopmentMode()
    {
        $this->_sFoxrateAPIUrl = 'http://foxrate.vm/';
        $this->_FoxrateAPI2Url = 'http://api.foxrate.vm/app_dev.php';
    }

}