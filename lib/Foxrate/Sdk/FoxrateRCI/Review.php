<?php
//ini_set('error_reporting', 0);
set_time_limit(0);

class Foxrate_Sdk_FoxrateRCI_Review extends Foxrate_Sdk_FoxrateRCI_ReviewAbstract implements Foxrate_Sdk_FoxrateRCI_ReviewInterface
{

    public function __construct($config, $connector, Foxrate_Sdk_FoxrateRci_DataManager $dataManager)
    {
        $this->config = $config;
        $this->foxrateConnector = $connector;
        $this->dataManager = $dataManager;

        $this->setSettings();

        $this->enableDevMode();
    }

    public function useDbCompatibleMode()
    {
        return false;
    }

    public function getRemoteAddr()
    {
        return $this->_getData('_remote_addr');
    }


    /**
     * Get general review info about a single product
     *
     * @param $sProductId
     * @return array
     */
    public function getReviewTotalDataById($sProductId)
    {
        try {
            $objData = $this->dataManager->loadProductsRevsGeneral_Cache($sProductId);
            $this->checkReviewValid($objData);
            $generalRevInfo = $this->convertObjectToArray($objData);
            krsort($generalRevInfo['counts']);
        } catch (Exception $e) {
            $generalRevInfo = array("error" => $e->getMessage());
        }
        return $generalRevInfo;
    }

    /**
     * Checks if given review is valid
     * @param $review
     * @throws Exception
     */
    protected function checkReviewValid($review)
    {
        if ($review->count == 0) {
            throw new Exception('Product review is not valid.');
        }
    }

    /**
     * Performs product review import from Foxrate using API
     * Loads product reviews from Foxrate and saves them to temporary directory, when all reviews are loaded, they are moved
     * to permanent directory, and temp is cleared. Failsafe strategy is used: If downloading the files fails, the old cache is used (instead of nothing)
     *
     * @return string
     */
    public function importProductReviews()
    {
        $cacheExpired = $this->hasCacheExpired();

        if ($cacheExpired) {
            try {
                $this->checkUserExist();
                $productIds = $this->getProductIds();
                $this->getSaveProductsReviews($productIds, true);
            } catch (Exception $e) {
                return $this->config->writeToLog("Error: " . $e->getMessage());
            }
            $this->updateCacheImportDate();
            return $this->config->writeToLog("Status: Imported Reviews Successfully");
        }
        return $this->config->writeToLog("Status: Import is not needed, cached reviews have not expired");
    }

    /**
     * Performs reviews import for single product, by checking if reviews are expired by file time modified,
     * locks the update and starts import. Locks so other users wont starts the import again when it is started.
     * Lock has a timeout, if something breaks, the import can be started again when the locks expires.
     */
    public function cacheOnDemand_SingleProdRev($productId)
    {
        $isExpired = $this->hasCacheExpired_CacheDemand($productId);
        $isLocked = $this->isCacheLocked_CacheDemand($productId);
        if (!$isExpired) {
            return "Cache On demand for this product has not expired yet. Product ID: " . $productId;
        }
        if ($isLocked) {
            return "Cache import is already in progress for product: " . $productId;
        }
        try {
            $this->lockOnDemandCache_Product($productId);
            $prodId = array(array($productId));
            $this->getSaveProductsReviews($prodId, false);
            $this->dataManager->unlockOnDemandCache_Product($productId);
        } catch (Exception $ex) {
            $this->dataManager->unlockOnDemandCache_Product($productId);
            return $this->config->writeToLog("Error: " . $ex->getMessage());
        }
        return "Cache On Demand Import was successfull for product: " . $productId;
    }


    /**
     * Creates product lock for reviews, so no other import instances would be launched, for the same product
     * @param $prodId
     */
    protected function lockOnDemandCache_Product($prodId)
    {
        $data = json_encode(array('lock_time' => strtotime('now')));
        $name = $prodId . ".lock";
        $this->storeToMainCache($name, $data, 'json');
    }


    /**
     * Updates date when last import finished successfully
     */
    protected function updateCacheImportDate()
    {
        $this->config->saveShopConfVar($this->sFoxrateConfNameImportDate, strtotime("now"), "string");
    }

    /**
     * Gets product ID's, on failure throws Exception
     * @return Object
     * @throws Exception
     */
    protected function getProductIds()
    {
        $productIds = $this->loadProductIds();
        if (!$productIds) {
            throw new Exception("No products were found in database");
        }
        return $productIds;
    }

    /**
     * Downloads product reviews from Foxrate and saves them in cache's temp directory
     * @param $productIds
     * @throws Exception
     */
    protected function getSaveProductsReviews($productIds, $fullImport)
    {
        $storeTempCache = false;
        try {
            $this->createDir($this->config->getCachedReviewsPathTemp());
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . '. Please make shure your cache dir is writable.');
        }

        if ($fullImport) {
            $this->deleteDirContents($this->config->getCachedReviewsPathTemp());
            $storeTempCache = true;
        }
        $this->loadSellerId_Cache();
        $this->loadShopId_Cache();
        foreach ($productIds as $productId) {
            $productId = is_array($productId) ? $productId[0] : $productId;
            try {
                $page = 0;
                do {
                    $page++;
                    $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array("foxratePR_Page" => $page));
                    $productRev = $this->requestSingleProductReview_Foxrate($productId);
                    $status = isset($productRev->status) ? $productRev->status != "error" : true;

                    if ($status && $productRev->reviews_count != 0) {
                        $this->storeToCache(
                            $productId . ".page" . $productRev->current_page,
                            json_encode($productRev),
                            $storeTempCache
                        );
                    }

                } while (isset($productRev->reviews_count) && ($productRev->pages_count != $productRev->current_page) && ($productRev->reviews_count != 0));

                $productRevGen = $this->requestSingleProductReviewGeneral_Foxrate($productId);

                $genStatus = isset($productRevGen->status) ? $productRevGen->status != "error" : true;
                if (!$genStatus || $productRevGen->count == 0) {
                    $productRevGen = array('count' => '0');
                }
                $this->storeToCache($productId . ".gener", json_encode($productRevGen), $storeTempCache);
            } catch (Exception $e) {
                $this->config->writeToLog(
                    "Product with id " . $productId . " reviews not imported. " . $e->getMessage()
                );
            }
        }

        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array("foxratePR_Page" => 1));
        if ($fullImport) {
            $this->moveCacheTempToPermanent();
        }
    }


    /**
     * Move contents from temporary cache to permanent
     * @return null
     */
    protected function moveCacheTempToPermanent()
    {
        $this->deleteDirContents($this->config->getCachedReviewsPath());
        $this->copyDirContents($this->config->getCachedReviewsPathTemp(), $this->config->getCachedReviewsPath());
        $this->deleteDirContents($this->config->getCachedReviewsPathTemp());
    }

    /**
     * Copies contents from source to destination directory
     * @param string $source
     * @param string $destination
     * @return null
     */
    protected function copyDirContents($source, $destination)
    {
        $success = true;
        if (!is_dir($source)) {
            throw new Exception("Directory copying failed, source directory does not exist: " . $source);
        }
        $this->createDir($destination);
        $fileList = scandir($source);
        foreach ($fileList as $singleFile) {

            $sourcePath = $source . "/" . $singleFile;
            $destinPath = $destination . "/" . $singleFile;

            if (!is_dir($sourcePath)) {
                $result = copy($sourcePath, $destinPath);
                if (!$result && $singleFile != "." && $singleFile != "..") {
                    $this->config->writeToLog(
                        "Warning: Failed to copy file from : '" . $sourcePath . "' to '" . $destinPath . "'"
                    );
                    $success = false;
                }
            }
        }
        return $success;
    }


    /**
     * Deletes contents (files only) from given directory
     * @param string $dir
     * @return null
     */
    protected function deleteDirContents($dir)
    {
        $fileList = scandir($dir);
        $success = true;
        foreach ($fileList as $singleFile) {
            $path = $dir . "/" . $singleFile;

            if (!is_dir($path)) {
                $result = @unlink($path);
                if (!$result && $singleFile != "." && $singleFile != ".." && $singleFile != $this->config->getCachedReviewsPath(
                    )
                ) {
                    $this->config->writeToLog("Warning: Failed to remove file: " . $path);
                    $success = false;
                }
            };

        }
        return $success;
    }


    /**
     * Creates directory if does not exist
     * @param $dir
     */
    protected function createDir($dir)
    {
        $res = true;
        if (!is_dir($dir)) {
            $res = $this->recursive_mkdir($dir, 0777, true);
        }
        if (!$res) {
            throw new Exception("Failed to create directory structure: " . $dir);
        }
    }

    function recursive_mkdir($path, $mode = 0777)
    {
        $dirs = explode(DIRECTORY_SEPARATOR, $path);
        $count = count($dirs);
        $path = '';
        for ($i = 0; $i < $count; ++$i) {
            if (!$dirs[$i]) {
                continue;
            }
            $path .= DIRECTORY_SEPARATOR . $dirs[$i];
            if (!is_dir($path) && !@mkdir($path, $mode)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Stores given data to temp or main cache
     * @param $name
     * @param $data
     * @param $isMainCacheStorage
     * @param string $format
     */
    protected function storeToCache($name, $data, $isTempCacheStorage, $format = "json")
    {
        if ($isTempCacheStorage) {
            $baseCachePath = $this->config->getCachedReviewsPathTemp();
        } else {
            $baseCachePath = $this->config->getCachedReviewsPath();
        }

        $pathName = $baseCachePath . "/" . $name . "." . $format;
        $saveResponse = file_put_contents($pathName, $data);
        if (!$saveResponse) {
            $this->config->writeToLog("Warning: Couldn't save data to temp cache directory: " . $pathName);
        }
    }

    /**
     * Stores given data to main cache
     * @param $name
     * @param $data
     * @param string $format
     * @throws Exception
     */
    protected function storeToMainCache($name, $data, $format = "json")
    {
        $pathName = $this->config->getCachedReviewsPath() . "/" . $name . "." . $format;
        $saveResponse = file_put_contents($pathName, $data);
        if (!$saveResponse) {
            $this->config->writeToLog("Warning: Couldn't save data to main cache directory: " . $pathName);
        }
    }

    /**
     * Gets single product review, builds api call, then uses it on curl with basic auth.
     * @param $productId
     * @return mixed
     */
    protected function requestSingleProductReview_Foxrate($productId)
    {
        $params = array("productId" => $productId);
        $apiCall = $this->apiCallBuilder("productReviews", "json", $params);
        $result = $this->makeRequestBasicAuth($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);

        if ($result === null) {
            throw new Exception('No result returned for product id: ' . $productId);
        }

        return $result;
    }

    /**
     * Gets single product general review information, builds api call, then uses it on curl with basic auth.
     * @param $productId
     * @return mixed
     */
    protected function requestSingleProductReviewGeneral_Foxrate($productId)
    {
        $params = array("productId" => $productId);
        $apiCall = $this->apiCallBuilder("productGeneral", "json", $params);
        return $this->makeRequestBasicAuth($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);

    }


    /**
     * Load Seller id, which is needed for multiple api calls
     *
     * @throws Exception
     */
    protected function loadSellerId_Foxrate()
    {
        $myConfig = $this->config;
        $apiCall = $this->apiCallBuilder("currentSellerId", "json");


        $resultObject = $this->makeRequestBasicAuth($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);

        if (empty($resultObject->id)) {
            throw new Foxrate_Sdk_Api_Exception_Communicate("Couldn't get current seller Id from Foxrate. Url: " . $apiCall);
        }
        $myConfig->saveShopConfVar('foxrateSellerId', $resultObject->id, 'string');
        $this->sFoxrateAPI2sellerId = $resultObject->id;
    }

    /**
     * Load Seller id from cache, which is needed for multiple api calls
     * @return mixed
     */
    protected function loadSellerId_Cache()
    {
        $sellerId = $this->config->getConfigParam('foxrateSellerId');

        if (is_null($sellerId)) {
            $this->loadSellerId_Foxrate();
        } else {
            $this->sFoxrateAPI2sellerId = $sellerId;
        }
    }

    /**
     * Load Seller id from cache, which is needed for multiple api calls
     *
     * @return mixed
     */
    protected function loadShopId_Cache()
    {
        $myConfig = $this->config;
        $shopId = $myConfig->getConfigParam('foxrateShopId');

        if (is_null($shopId)) {
            $this->loadShopId_Foxrate();
        } else {
            $this->sFoxrateAPIShopId = $shopId;
        }

    }

    /**
     * Load shop and channel id from Foxrate's api. List of channels is returned and one is selected which matches with domain
     * the import is being ran on
     *
     * @return int
     * @throws Exception
     */
    protected function loadShopId_Foxrate()
    {

        $myConfig = $this->config;
        $shopIdOverride = $myConfig->getConfigParam('foxrateOverrideShopId');
        if (isset($shopIdOverride)) {
            $this->sFoxrateAPIShopId = $shopIdOverride;
            return 0;
        }

        $apiCall = $this->apiCallBuilder("currentSellerChannelsId", "json");
        $ShopIdAndUrl = $this->makeRequestBasicAuth($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);
        if (!$ShopIdAndUrl[0]->id) {
            throw new Exception("Couldn't get shop Id from Foxrate. Url: " . $apiCall);
        }
        $ShopId = $this->findShopIdByUrlMatch($ShopIdAndUrl);
        if (!$ShopId) {
            throw new Exception("The url received from Foxrate's api does not match this domain. This url '" . $this->config->getShopUrl(
            ) . "'");
        }
        $myConfig->saveShopConfVar('foxrateShopId', $ShopId, 'string');
        $this->sFoxrateAPIShopId = $ShopId;
    }

    /**
     * Find shop id from given object, that matches it's url and this domain
     */
    protected function findShopIdByUrlMatch($ShopIdAndUrl)
    {
        $domainRaw = $this->config->getHomeUrl();
        $domain = preg_replace("/(http:\/\/|https:\/\/|www.)/", "", $domainRaw);

        foreach ($ShopIdAndUrl as $singleBlock) {
            $cleanUrl = preg_replace("/(http:\/\/|https:\/\/|www.)/", "", $singleBlock->name);
            $matchResult = preg_match("/" . $cleanUrl . ".*|.*hotdigital.*/", $domain);
            if ($matchResult == 1 || $matchResult == true) {
                return $singleBlock->id;
            }
        }
        return false;
    }

    /**
     * Build api call by given scenario
     *
     * @param $method
     * @param string $format
     * @param bool $params
     * @return string
     * @throws Exception
     */
    protected function apiCallBuilder($method, $format = "json", $params = false)
    {
        $call = "";
        $extraParams = "";


        $callBase = $this->getFoxrateApiUrl() . "/" . $this->sFoxrateAPI2version;
        switch ($method) {
            case "currentSellerId":
                $call = $callBase . "/" . $this->sFoxrateAPI2sellers . "/id";
                break;
            case "productGeneral":
                if (!is_array($params)) {
                    throw new Exception('Params not set!');
                }
                $call = $callBase . "/" . $this->sFoxrateAPI2sellers . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->sFoxrateAPI2products . "/" . $params["productId"] . "/" . $this->sFoxrateAPI2ratings;
                $extraParams = $this->apiCallOptionalParamsBuilder();
                break;
            case "currentSellerChannelsId":
                $call = $callBase . "/" . $this->sFoxrateAPI2sellers . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->sFoxrateAPI2channels;
                break;
            case 'voteProductReview':
                if (!is_array($params)) {
                    throw new Exception('Params not set!');
                }
                $call = $callBase . "/" . $this->sFoxrateAPI2sellers . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->sFoxrateAPI2products;
                $call .= "/" . $this->sFoxrateAPI2reviews . "/" . $params["reviewId"] . "/" . $this->sFoxrateAPI2vote;
                break;
            case 'abuseReview':
                if (!is_array($params)) {
                    throw new Exception('Params not set!');
                }
                $call = $callBase . "/" . $this->sFoxrateAPI2sellers . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->sFoxrateAPI2products;
                $call .= "/" . $this->sFoxrateAPI2reviews . "/" . $params["reviewId"] . "/" . $this->sFoxrateAPI2abuse;
                break;
            case "productReviews":
                if (!is_array($params)) {
                    throw new Exception('Params not set!');
                }
                $call = $callBase . "/" . $this->sFoxrateAPI2sellers . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->sFoxrateAPI2products . "/" . $params["productId"];
                $call .= "/" . $this->sFoxrateAPI2reviews;
                $extraParams = $this->apiCallOptionalParamsBuilder();
                break;
        }
        $call .= "." . $format . $extraParams;
        return $call;
    }


    /**
     * If optional/additional parameters are set, they are added to api call.
     * @return string
     */
    protected function apiCallOptionalParamsBuilder()
    {
        $urlParams = "filter[channel]=" . $this->getFoxrateShopId() . "&";
        $paramName = "";
        foreach ($this->sFoxrateSettings as $settingKey => $settingValue) {
            if (($settingKey != "") && ($settingValue != "")) {
                switch ($settingKey) {
                    case 'foxratePR_SortBy':
                        $paramName = "sort_by";
                        $urlChanges = true;
                        break;
                    case 'foxratePR_SortOrder':
                        $paramName = "sort_order";
                        $urlChanges = true;
                        break;
                    case 'foxratePR_Page':
                        $paramName = "page";
                        $urlChanges = true;
                        break;
                    case 'foxratePR_RevsPerPage':
                        $paramName = "limit";
                        $urlChanges = true;
                        break;
                    default:
                        $urlChanges = false;
                        break;
                }
                if ($urlChanges) {
                    $urlParams .= $paramName . "=" . $settingValue . "&";
                }
            }
        }

        if ($urlParams != "") {
            $urlParams = "?" . $urlParams;
        }
        return $urlParams;
    }


    /**
     * Throws exception if user does not exist
     * @throws Exception
     */
    protected function checkUserExist()
    {
        if (!$this->sFoxrateAPIUsername) {
            throw new Exception("Foxrate Api Username is not set");
        }
        if (!$this->sFoxrateAPIPassword) {
            throw new Exception("Foxrate Api Password is not set");
        }
        if (!$this->foxrateConnector->isUserExist($this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword)) {
            throw new Exception("User is not found in Foxrate: Username - '" . $this->sFoxrateAPIUsername . "' password - '" . $this->sFoxrateAPIPassword . "'");
        }
    }

    /**
     * Make a request via CURL with given headers and params to specific URL, using Masic Auth
     *
     * @param $sUrl
     * @param $username
     * @param $password
     * @param array $aHeaders
     * @param null $aParams
     * @return bool|mixed
     *
     */
    protected function makeRequestBasicAuth($sUrl, $username, $password, $aHeaders = array(), $aParams = null)
    {
        $ch = curl_init();

        $opts[CURLOPT_CONNECTTIMEOUT] = 10;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_TIMEOUT] = 60;
        $opts[CURLOPT_HTTPHEADER] = $aHeaders;
        $opts[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $opts[CURLOPT_USERPWD] = trim($username) . ":" . trim($password);
        $opts[CURLOPT_URL] = $sUrl;
        if (!is_null($aParams)) {
            $opts[CURLOPT_POSTFIELDS] = $aParams;
        }

        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($result === false || $code != 200) {

            $en = curl_errno($ch);
            $e = curl_error($ch);
            curl_close($ch);
            $this->config->writeToLog("Warning: cUrl Error: " . $en . " - " . $e);
            $this->config->writeToLog("Warning: cUrl Error url: " . $sUrl);
        }

        if (gettype($ch) == 'resource') {
            curl_close($ch);
        }

        return json_decode($result);
    }

    public function makeMageRequest($url, $username, $password, $headers = null, $params = null)
    {
        $client = new Varien_Http_Client();

        $client->setAuth($username, $password, Zend_Http_Client::AUTH_BASIC);

        $client->setUri($url)
            ->setMethod('GET')
            ->setConfig(
                array(
                    'maxredirects' => 0,
                    'timeout' => 60,
                )
            );

        if (isset($headers)) {
            $client->setHeaders($headers);
        }

        if (isset($params)) {
            $client->setRawData($params);
        }

        $result = $client->request();

        try {
            return $this->handleRequest($result);
        } catch (Zend_Http_Client_Exception $e) {
            $error_message = 'There was error with API. ' . $e->getMessage();
            $this->config->writeToLog($error_message);
            throw new Exception ($error_message);
        }

    }

    protected function handleRequest(Zend_Http_Response $result)
    {
        if ($result->isError()) {
            $bodyObject = json_decode($result->getRawBody());
            $error_message = "Api returned error with message: " . $bodyObject->status_text;
            $this->config->writeToLog($error_message);
            throw new Exception($error_message);
        }

        return json_decode($result->getRawBody());
    }

    /**
     * Checks if the cache on demand for single file has already expired
     * @param $prodId
     * @param string $format
     *
     * @return bool
     */
    protected function hasCacheExpired_CacheDemand($prodId, $format = 'json')
    {
        $path = $this->config->getCachedReviewsPath() . "/" . $prodId . ".gener." . $format;
        if (!file_exists($path)) {
            return true;
        }
        $changedTime = filemtime($path);
        $expireDate = strtotime("- {$this->sReviewsExpirePeriodCacheDemand} hours");
        if ($changedTime <= $expireDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if product review cache has expired
     * @return bool
     */
    protected function hasCacheExpired()
    {
        //fixme
        return true;

        $lastImportDate = $this->config->getConfigParam($this->sFoxrateConfNameImportDate);
        $expireDate = strtotime("- {$this->sReviewsExpirePeriod} hours");
        if (!$lastImportDate) {
            return true;
        }

        if ($lastImportDate <= $expireDate) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Foxrate product review logger
     * @param $eventMessage
     * @return mixed
     */
    protected function eventLogger($eventMessage)
    {
        $this->cleanLog();
        $time = date("Y.m.d H:i:s");
        $logMessage = $time . " " . $eventMessage . "\n";
        oxUtils::getInstance()->writeToLog($logMessage, $this->sFoxrateLoggerFileName);
        return $eventMessage;
    }

    /**
     * Log cleaning
     */
    protected function cleanLog()
    {
        $logsDir = $this->config->getLogsDir();
        $foxLog = $logsDir . $this->sFoxrateLoggerFileName;
        if (file_exists($foxLog)) {
            $size = filesize($foxLog);
            $mSize = ($size / 1024) / 1024;
            if ($mSize >= 20) {
                unlink($foxLog);
            }
        }
    }

    /**
     * Downloads the log
     */
    public function downloadLog()
    {
        $logsDir = $this->config->getLogsDir();
        $foxLog = $logsDir . $this->sFoxrateLoggerFileName;
        if (file_exists($foxLog)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $this->sFoxrateLoggerFileName);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($foxLog));
            ob_clean();
            flush();
            readfile($foxLog);
            flush();
        }
    }


    /**
     * Converts object to array
     * @param $data
     * @return array
     */
    public function convertObjectToArray($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->convertObjectToArray($value);
            }
            return $result;
        }
        return $data;
    }

    /**
     * Loads all the pages of a review from cache (needed when all reviews have to be displayed)
     * @param $prodId
     * @return array
     */
    public function loadProductsAllRevs_Cache($prodId)
    {
        $pages = 0;
        $reviewArr = array();
        $reviewArr[$this->sAPIResRev] = array();
        $reviewMain = array();

        do {
            $pages++;
            $reviewsPage = $this->dataManager->loadCachedProductReviews($prodId, $pages);
            if ($reviewsPage) {
                $reviewArr[$this->sAPIResRev] = array_merge(
                    $reviewArr[$this->sAPIResRev],
                    $this->convertObjectToArray($reviewsPage->reviews)
                );
                $reviewMain = $this->convertObjectToArray($reviewsPage);
            }
        } while ($pages < $reviewMain[$this->sAPIResPageCnt]);

        $reviewArr = array_merge($reviewMain, $reviewArr);

        return $reviewArr;
    }

    /**
     * Calculates date
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date)
    {
        $matches = array();
        $result = preg_match($this->regExpDateFromApi, $date, $matches);
        if ($result) {
            return $matches[1];
        }
    }

    /**
     * Loads page navigation, gets neighbouring values calculated from current page number, filtering negative ones,
     * and those which are bigger than the page limit
     * @param $totalPages
     * @param $currentPage
     * @return array
     */
    public function getPageNav($totalPages, $currentPage)
    {
        $pageCounts = array(-2, -1, 0, 1, 2);
        $pageNav = array();
        foreach ($pageCounts as $pageCount) {
            $result = $currentPage + $pageCount;
            if (($result > 0) && ($result <= $totalPages)) {
                if ($result == $currentPage) {
                    $pageNav = array_merge($pageNav, array('current' => $result));
                } else {
                    $pageNav = array_merge($pageNav, array('other' . $result => $result));
                }
            }
        }
        return $pageNav;
    }

    /**
     * Generates sorting criteria for user reviews
     */
    public function getSortingCriteria()
    {
        $sortingCriteria = array(
            '' => '',
            'date_asc' => 'Date ↑',
            'date_desc' => 'Date ↓',
            'rate_asc' => 'Rating ↑',
            'rate_desc' => 'Rating ↓'
        );
        return $sortingCriteria;
    }

    /**
     * Send voting to foxrate api
     */
    public function voteReview($revId, $useful)
    {
        if ($useful == 'true') {
            $useful = true;
        } else {
            $useful = false;
        }
        $this->loadSellerId_Cache();
        $params = array("reviewId" => $revId);
        $url = $this->apiCallBuilder('voteProductReview', 'json', $params);
        $postParams = json_encode(array('useful' => $useful));
        $resultRaw = $this->makeRequestBasicAuth(
            $url,
            $this->sFoxrateAPIUsername,
            $this->sFoxrateAPIPassword,
            array("Content-type: application/json"),
            $postParams
        );
        return $resultRaw;
    }


    /**
     * Report abuse review to foxrate api
     */
    public function abuseReview($revId, $abuse)
    {
        if ($abuse == 'true') {
            $abuse = true;
        } else {
            $abuse = false;
        }
        $this->loadSellerId_Cache();
        $params = array("reviewId" => $revId);
        $url = $this->apiCallBuilder('abuseReview', 'json', $params);
        $postParams = json_encode(array('abuse' => $abuse));
        $resultRaw = $this->makeRequestBasicAuth(
            $url,
            $this->sFoxrateAPIUsername,
            $this->sFoxrateAPIPassword,
            array("Content-type: application/json"),
            $postParams
        );
        return $resultRaw;
    }

    /**
     * Gets link to write reviews
     */
    public function getWriteReviewLink($prodId)
    {

        $isAllowed = $this->config->getConfigParam('foxratePR_WriteReview');
        if ($isAllowed == 'off' || is_null($isAllowed)) {
            return null;
        }

        $lang = $this->config->getLanguageAbbr();
        $this->loadSellerId_Cache();
        $this->loadShopId_Cache();

        //http://foxrate.vm/product_rate/en/2820/shop_58/
        //http://foxrate.de/product_rate/de/15170/shop_1/315a1773d274183955625d030225fcc9


        $link = $this->getFoxrateUrl() . $this->sFoxrateProdProfLink . "/" . $lang . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->getFoxrateShopId() . "/";
        $link .= $prodId;
        return $link;
    }

    /**
     * Load product Ids
     *
     * @return mixed
     */
    public function loadProductIds()
    {
        return FoxrateReviews::getKernel()->get('shop.product')->getProductsIds();
    }

    public function getTotalReviews($generalReview)
    {
        if (!isset($generalReview)) {
            throw new Exception('General product review info not given!');
        }
        return $generalReview['count'];
    }

    public function getFoxrateApiUrl()
    {
        return $this->foxrateApiUrl;
    }

    public function getFoxrateUrl()
    {
        return $this->foxrateUrl;
    }

    /**
     * Enables Dev mode, if it is dev enviroment
     * @todo needs to refactored to correct location and unify
     */
    public function enableDevMode()
    {
        if ($this->isDevEnviroment()) {
            $this->foxrateApiUrl = 'http://api.foxrate.vm';

            $this->foxrateUrl = 'http://foxrate.vm/';
        }
    }

    /**
     * Check if it is Foxrate developer enviroment.
     * @return bool
     */
    public function isDevEnviroment()
    {
        return isset($_SERVER['FOXRATE__IS_DEVELOPER_MODE']) && $_SERVER['FOXRATE__IS_DEVELOPER_MODE'] == true && strpos(
            $_SERVER['SERVER_NAME'],
            '.vm'
        );
    }

    /**
     * Controller return true or false if richsnippet options is enabled or disabled
     * @return bool
     */
    public function richSnippetIsActive()
    {

        $isActive = $this->config->getConfigParam('foxratePR_OrderRichSnippet');
        if ($isActive == 'off' || is_null($isActive)) {
            return false;
        } else {
            return true;
        }
    }

    public function getFoxrateShopId()
    {
        if (null === $this->sFoxrateAPIShopId) {
            throw new InvalidArgumentException('Foxrate shop id is not set.');
        }
        return $this->sFoxrateAPIShopId;
    }

}

