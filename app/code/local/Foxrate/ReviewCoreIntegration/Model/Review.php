<?php
//ini_set('error_reporting', 0);
set_time_limit(0);

class Foxrate_ReviewCoreIntegration_Model_Review extends Mage_Core_Model_Abstract
{


    /**
     * Foxrate's api url
     * @var string
     */
    protected $foxrateApiUrl = 'http://api.foxrate.com';

    /**
     * Foxrate application link
     * @var string
     */
    protected $foxrateUrl = 'http://foxrate.de';

    /**
     * Foxrate's api version
     * @var string
     */
    protected $sFoxrateAPI2version = 'v1';

    /**
     * Foxrate's api product
     * @var string
     */
    protected $sFoxrateAPI2products = 'products';

    /**
     * Foxrate's api product
     * @var string
     */
    protected $sFoxrateAPI2reviews = 'reviews';

    /**
     * Foxrate's api vote
     * @var string
     */
    protected $sFoxrateAPI2vote = 'vote';

    /**
     * Foxrate's api abuse
     * @var string
     */
    protected $sFoxrateAPI2abuse = 'abuse';

    /**
     * Foxrate's api sellers
     * @var string
     */
    protected $sFoxrateAPI2sellers = 'sellers';

    /**
     * Foxrate's api ratings
     * @var string
     */
    protected $sFoxrateAPI2ratings = 'ratings';


    /** Foxrate's api channels string
     * @var string
     */
    protected $sFoxrateAPI2channels = 'channels';

    /** Foxrate's config name for last import name in db
     * @var string
     */
    protected $sFoxrateConfNameImportDate = "foxrate_lastProductReviewImport";

    /**
     * Foxrate's api seller id
     * @var string
     */
    protected $sFoxrateAPI2sellerId = '';

    /**
     * Foxrate's locally stored review expiration period (hours)
     * This timeout is for cron update, which will import all reviews
     * This expire time should be equal or less than Cache on Demand timeout
     * @var string
     */
    protected $sReviewsExpirePeriod = '6';

    /**
     * Foxrate's locally single stored review expiration period (hours)
     * This timeout is checked by cache on demand, for review file's timestamp
     * This expiration should be equal or greater than Expire time for Cron import
     * @var string
     */
    protected $sReviewsExpirePeriodCacheDemand = '1';

    /**
     * Lock expire time. (minutes). If this time period has passed since lock creation, it is assumed something broke
     * and lock is treated as expired.
     * @var string
     */
    protected $sReviewsExpireLockCacheDemand = '30';


    /**
     * Full Path to cached product reviews directory
     * @var string
     */
    protected $sCachedProdRevsDir = "";

    /**
     * Directory name for main cached files of Foxrate
     * @var string
     */
    protected $sFoxrateCachedFilesDir = "FoxrateCache";

    /**
     * Subdirectory for Foxrate's cached files of product reviews
     * @var string
     */
    protected $sFoxrateCachedProductsSubDir = "ProductReviews";

    /**
     * Subdirectory for Foxrate's cached files temporary directory
     * Fail-safe way to download files first, and then move them to permanent directory
     * If download fails, permanent directory (old) files will be used instead
     * @var string
     */
    protected $sFoxrateCachedProductsTemp = "tmp";

    /**
     * Foxrate's username for API
     * @var string
     */
    protected $sFoxrateAPIUsername = "";
    /**
     * Foxrate's password for API
     * @var string
     */
    protected $sFoxrateAPIPassword = "";

    /**
     * Foxrate's shop id
     * @var string
     */
    protected $sFoxrateAPIShopId = "";

    /**
     * Foxrate's product review import logger path
     * @var string
     */
    protected $sFoxrateLoggerFileName = 'foxrateProductReviewLog.txt';

    /**
     * Import settings for product reviews
     * @var mixed|string
     */
    protected $sFoxrateSettings = array();

    /**
     * RegExp for date extraction from api
     * @var string
     */
    protected $regExpDateFromApi = "/(\d+-\d+-\d+)T\d+:\d+:\d+\+\d+/";

    /**
     * Review variable from api
     * @var string
     */
    protected $sAPIResRev = 'reviews';

    /**
     * Review count variable from api
     * @var string
     */
    protected $sAPIResRevCount = 'reviews_count';

    /**
     * Page count variable from api
     * @var string
     */
    protected $sAPIResPageCnt = 'pages_count';

    /**
     * Current page from api
     * @var string
     */
    protected $sAPIResCurPage = 'current_page';


    /**
     * Foxrate application link
     * @var string
     */
    protected $sFoxrateProdProfLink = 'product_rate';

    /**
     * Default number of reviews if user has not set it
     * @var int
     */
    protected $sFoxrateDefaultRevsPerPage = 20;

    /**
     * Registry object
     * @var
     *
     */
    public $registry;


    /**
     * Config object
     * @var
     */
    public $config;

    public function __construct()
    {
        $this->registry = Mage::getModel('reviewcoreintegration/registry');
        $this->config = Mage::getModel('reviewcoreintegration/config');
        $this->foxrateConnector = Mage::getModel('ordersexport/foxrate');
        //$this->databaseAccess = Mage::getModel('reviewcoreintegration/databaseAccess');

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

    public function setSettings()
    {

        $sTempFileDirectory = $this->config->getConfigParam("sCompileDir");
        $this->sCachedProdRevsDir = $sTempFileDirectory . $this->sFoxrateCachedFilesDir . "/" . $this->sFoxrateCachedProductsSubDir;
        $this->sCachedProdRevsDirTemp = $sTempFileDirectory . $this->sFoxrateCachedFilesDir . "/" . $this->sFoxrateCachedProductsSubDir . "/" . $this->sFoxrateCachedProductsTemp;
        $this->sFoxrateAPIUsername = $this->config->getConfigParam("foxrateUsername");
        $this->sFoxrateAPIPassword = $this->config->getConfigParam("foxratePassword");

        $revsPerPage = $this->config->getConfigParam('foxratePR_RevsPerPage');
        if (!$revsPerPage) {
            $revsPerPage = $this->sFoxrateDefaultRevsPerPage;
        }
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array('foxratePR_RevsPerPage' => $revsPerPage));
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_SortBy' => $this->config->getConfigParam('foxratePR_SortBy'))
        );
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_SortOrder' => $this->config->getConfigParam('foxratePR_SortOrder'))
        );
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_Summary' => $this->config->getConfigParam('foxratePR_Summary'))
        );
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_OrderRichSnippet' => $this->config->getConfigParam('foxratePR_OrderRichSnippet'))
        );
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_CatalogDisplay' => $this->config->getConfigParam('foxratePR_CatalogDisplay'))
        );
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_CatalogTooltip' => $this->config->getConfigParam('foxratePR_CatalogTooltip'))
        );
        $this->sFoxrateSettings = array_merge(
            $this->sFoxrateSettings,
            array('foxratePR_WriteReview' => $this->config->getConfigParam('foxratePR_WriteReview'))
        );
        $this->sFoxrateSettings = array_merge($this->sFoxrateSettings, array("foxratePR_Page" => 1));
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
            $objData = $this->loadProductsRevsGeneral_Cache($sProductId);
            $generalRevInfo = $this->convertObjectToArray($objData);
            krsort($generalRevInfo['counts']);
        } catch (Exception $e) {
            $generalRevInfo = array("error" => $e->getMessage());
        }
        return $generalRevInfo;
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
            } catch (Exception $ex) {
                return $this->config->writeToLog("Error: " . $ex->getMessage());
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
            $this->unlockOnDemandCache_Product($productId);
        } catch (Exception $ex) {
            $this->unlockOnDemandCache_Product($productId);
            return $this->config->writeToLog("Error: " . $ex->getMessage());
        }
        return "Cache On Demand Import was successfull for product: " . $productId;
    }

    /**
     * Checks if cache on demand is locked by another instance, which might be in import progress.
     * If lock was not removed some time ago, it is assumed something broke and lock was not removed properly,
     * so the lock is treated as invalid/expired
     * @param $productId
     * @return bool
     */
    protected function isCacheLocked_CacheDemand($productId, $format = 'json')
    {
        $lockPath = $this->sCachedProdRevsDir . "/" . $productId . ".lock." . $format;
        if (!file_exists($lockPath)) {
            return false;
        }
        $lockTimeRaw = file_get_contents($lockPath);
        $lockTimeArr = json_decode($lockTimeRaw);
        $lockTime = $lockTimeArr->lock_time;
        $currentTime = strtotime("- {$this->sReviewsExpireLockCacheDemand} minutes");
        if ($lockTime <= $currentTime) {
            $this->unlockOnDemandCache_Product($productId);
            return false;
        } else {
            return true;
        }
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
     * Removes product lock for reviews. Can only be called if at least one of these conditions apply:
     * 1. Import was successfull
     * 2. Import encountered an error
     * 3. Import lock time ended.
     *
     * @param $prodId
     * @throws Exception
     */
    protected function unlockOnDemandCache_Product($prodId)
    {
        $pathName = $this->sCachedProdRevsDir . "/" . $prodId . ".lock.json";
        $result = unlink($pathName);
        if (!$result) {
            throw new Exception("Lock removal failed for path: " . $pathName);
        }
    }

    /**
     * Updates date when last import finished successfully
     */
    protected function updateCacheImportDate()
    {
        $this->config->saveShopConfVar("string", $this->sFoxrateConfNameImportDate, strtotime("now"));
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
            $this->createDir($this->sCachedProdRevsDirTemp);
        } catch (Exception $e) {
            throw new Exception($e->getMessage() . '. Please make shure your cache dir is writable.');
        }

        if ($fullImport) {
            $this->deleteDirContents($this->sCachedProdRevsDirTemp);
            $storeTempCache = true;
        }
        $this->loadSellerId_Foxrate();
        $this->loadShopId_Foxrate();
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
                } while (($productRev->pages_count != $productRev->current_page) && ($productRev->reviews_count != 0));

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
        $this->deleteDirContents($this->sCachedProdRevsDir);
        $this->copyDirContents($this->sCachedProdRevsDirTemp, $this->sCachedProdRevsDir);
        $this->deleteDirContents($this->sCachedProdRevsDirTemp);
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
                if (!$result && $singleFile != "." && $singleFile != ".." && $singleFile != $this->sFoxrateCachedProductsTemp) {
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
            $baseCachePath = $this->sCachedProdRevsDirTemp;
        } else {
            $baseCachePath = $this->sCachedProdRevsDir;
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
        $pathName = $this->sCachedProdRevsDir . "/" . $name . "." . $format;
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
        return $this->makeMageRequest($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);
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
        return $this->makeMageRequest($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);;
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


        $resultObject = $this->makeMageRequest($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);

        if (empty($resultObject->id)) {
            throw new Exception("Couldn't get current seller Id from Foxrate. Url: " . $apiCall);
        }
        $myConfig->saveShopConfVar('string', 'foxrateSellerId', $resultObject->id);
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
        $ShopIdAndUrl = $this->makeMageRequest($apiCall, $this->sFoxrateAPIUsername, $this->sFoxrateAPIPassword);
        if (!$ShopIdAndUrl[0]->id) {
            throw new Exception("Couldn't get shop Id from Foxrate. Url: " . $apiCall);
        }
        $ShopId = $this->findShopIdByUrlMatch($ShopIdAndUrl);
        if (!$ShopId) {
            throw new Exception("The url received from Foxrate's api does not match this domain. This url '" . $this->config->getShopUrl(
            ) . "'");
        }
        $myConfig->saveShopConfVar('string', 'foxrateShopId', $ShopId);
        $this->sFoxrateAPIShopId = $ShopId;
    }

    /**
     * Find shop id from given object, that matches it's url and this domain
     */
    protected function findShopIdByUrlMatch($ShopIdAndUrl)
    {
        $domainRaw = Mage::helper('core/url')->getHomeUrl();
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
        $urlParams = "filter[channel]=" . $this->sFoxrateAPIShopId . "&";
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
     * @deprecated use makeMageRequest
     *
     * @todo use Varien_Http_Client
     */
    protected function makeRequestBasicAuth($sUrl, $username, $password, $aHeaders = array(), $aParams = null)
    {
        $ch = curl_init();

        $opts[CURLOPT_CONNECTTIMEOUT] = 10;
        $opts[CURLOPT_RETURNTRANSFER] = true;
        $opts[CURLOPT_TIMEOUT] = 60;
        $opts[CURLOPT_HTTPHEADER] = $aHeaders;
        $opts[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
        $opts[CURLOPT_USERPWD] = $username . ":" . $password;
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
        curl_close($ch);

        return $result;
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
     * @return bool
     */
    protected function hasCacheExpired_CacheDemand($prodId, $format = 'json')
    {
        $path = $this->sCachedProdRevsDir . "/" . $prodId . ".gener." . $format;
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
     * Loads single review page from cache
     * @param $prodId
     * @param int $page
     * @return bool
     */
    public function loadCachedProductReviews($prodId, $page = 1)
    {
        $revPage = false;
        $path = $this->prodRevFilenameBuilder($prodId, "page", $page, "json");

        try {
            $rawContent = $this->ReadFileContents($path);
            $arrContent = json_decode($rawContent);
            $revPage = $arrContent;
        } catch (Exception $e) {
            $this->config->writeToLog($e->getMessage() . " product: " . $prodId);
            throw new Exception("Product review info not found.");
        }
        return $revPage;
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
     * Loads review's general info from cache
     * @param $prodId
     * @param int $page
     * @return bool
     */
    public function loadProductsRevsGeneral_Cache($prodId)
    {
        $revGen = false;
        $path = $this->prodRevFilenameBuilder($prodId, "general");
        try {
            $rawContent = $this->ReadFileContents($path);
            $revGen = json_decode($rawContent);
            $this->checkReviewValid($revGen);
        } catch (Exception $e) {
            $this->config->writeToLog($e->getMessage() . " product: " . $prodId);
            throw new Exception("Reviews for this product are not found");
        }
        return $revGen;
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
     * Builds review file path from parameters
     * @param $prodId
     * @param int $page
     * @param string $format
     * @return string
     */
    protected function prodRevFilenameBuilder($prodId, $type = "page", $page = 1, $format = "json")
    {
        switch ($type) {
            case "page":
                $typeStr = "page" . $page;
                break;
            case "general":
                $typeStr = "gener";
                break;
            default:
                $typeStr = "page" . $page;
                break;
        }
        return $this->sCachedProdRevsDir . "/" . $prodId . "." . $typeStr . "." . $format;
    }

    /**
     * Read Single file if it exists
     * @param string $dir
     * @return null
     */
    protected function ReadFileContents($path)
    {
        if (is_file($path)) {
            $content = file_get_contents($path);
            if (!$content) {
                throw new Exception("Error: Failed to load data from file: " . $path);
            }
        } else {
            throw new Exception("Error: File not found: " . $path);
        }
        return $content;
    }

    /**
     * Loads all the pages of a review from cache (needed when all reviews have to be displayed)
     * @param $prodId
     * @return array
     */
    protected function loadProductsAllRevs_Cache($prodId)
    {
        $pages = 0;
        $reviewArr = array();
        $reviewArr[$this->sAPIResRev] = array();
        $reviewMain = array();

        do {
            $pages++;
            $reviewsPage = $this->loadCachedProductReviews($prodId, $pages);
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
     * Returns filtered reviews by search keyword, star ratings, sorting criteria
     *
     * @param $prodId
     * @param int $page
     * @param $filter
     * @return array|bool|string
     */
    public function getFilteredProductRevs($prodId, $page = 1, $filter)
    {
        $activeFilter = false;
        $innerFilter = array();
        foreach ($filter as $key => $condition) {
            if ((isset($key)) && (isset($condition)) && $condition != "") {
                $activeFilter = true;
                $innerFilter[$key] = $condition;
            }
        }

        if ($activeFilter) {
            $allRevs = $this->loadProductsAllRevs_Cache($prodId);
            $revsPerPage = $this->sFoxrateSettings['foxratePR_RevsPerPage'];

            foreach ($innerFilter as $key => $condition) {
                $allRevs[$this->sAPIResRev] = $this->applyFilterForRevs($allRevs[$this->sAPIResRev], $key, $condition);
                $allRevs[$this->sAPIResRevCount] = count($allRevs[$this->sAPIResRev]);
                $allRevs[$this->sAPIResPageCnt] = ceil($allRevs[$this->sAPIResRevCount] / $revsPerPage);
            }
            if ($allRevs[$this->sAPIResPageCnt] > 1) {
                $allRevs = $this->applyFilterForRevs($allRevs, 'page', $page);
            } else {
                $allRevs[$this->sAPIResPageCnt] = 1;
                $allRevs[$this->sAPIResCurPage] = 1;
            }
        } else {
            $allRevs = $this->loadCachedProductReviews($prodId, $page);
        }
        return $allRevs;
    }

    /**
     * Applies filtering rules on given reviews
     */
    public function applyFilterForRevs($revs, $filterRule, $filterVal)
    {

        $foxrateFiltering = Mage::helper('reviewcoreintegration/filter');
        $finalRevs = "";
        switch ($filterRule) {
            case "star_filter":
                $finalRevs = $foxrateFiltering->filter($filterVal, $revs, 'filterRevs_Ratings');
                if (empty($finalRevs)) {
                    throw new Exception('No products found with selected star count');
                }
                break;
            case "sort":
                $finalRevs = $foxrateFiltering->sort($filterVal, $revs, 'filterRevs_Sorting');
                //uasort($revs, array($foxrateFiltering->setValue($filterVal), 'filterRevs_Sorting'));
//                $finalRevs = $revs;
                break;
            case "search";
                $finalRevs = $foxrateFiltering->filter($filterVal, $revs, 'filterRevs_Search');
                if (empty($finalRevs)) {
                    throw new Exception('Could not find any product with given keyword');
                }
                break;
            case "page":
                $currPageIndex = $filterVal;
                $filterVal--;
                $revsPerPage = $this->sFoxrateSettings['foxratePR_RevsPerPage'];
                $finalRevs[$this->sAPIResRev] = array_slice(
                    $revs[$this->sAPIResRev],
                    $filterVal * $revsPerPage,
                    $revsPerPage
                );
                $finalRevs[$this->sAPIResRevCount] = $revs[$this->sAPIResRevCount];
                $finalRevs[$this->sAPIResPageCnt] = $revs[$this->sAPIResPageCnt];
                $finalRevs[$this->sAPIResCurPage] = $currPageIndex;
                break;
            default:
                $finalRevs = $revs;
                break;
        }
        return $finalRevs;
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


        return $this->getFoxrateUrl(
        ) . "/" . $this->sFoxrateProdProfLink . "/" . $lang . "/" . $this->sFoxrateAPI2sellerId . "/" . $this->sFoxrateAPIShopId . "/" . $prodId;
    }

    /**
     * Load product Ids
     *
     * @return mixed
     */
    public function loadProductIds()
    {
        $model = Mage::getModel('catalog/product');;
        $collection = $model->getCollection();;
        foreach ($collection as $product) {
            $productIds[$product->getId()] = $product->getId();
        }
        array_unique($productIds);
        return $productIds;
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
            $this->foxrateApiUrl = 'https://api.foxrate.vm';

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

