<?php


class Foxrate_Sdk_FoxrateRCI_DataManager
{


    protected $config;

    protected $productReviewsFactory;

    /**
     * Lock expire time. (minutes). If this time period has passed since lock creation, it is assumed something broke
     * and lock is treated as expired.
     * @var string
     */
    protected $sReviewsExpireLockCacheDemand = '30';

    public function __construct(
        Foxrate_Sdk_FoxrateRCI_ConfigInterface $config,
        Foxrate_Sdk_ApiBundle_Service_ProductReviewsFactory $productReviewFactory
    ) {
        $this->config = $config;
        $this->productReviewsFactory = $productReviewFactory;
    }

    /**
     * Creates product lock for reviews, so no other import instances would be launched, for the same product
     * @param $prodId
     */
    public function lockOnDemandCacheProduct($prodId)
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
     * @throws Foxrate_Sdk_ApiBundle_Exception_ModuleException
     */
    public function unlockOnDemandCacheProduct($prodId)
    {
        $pathName = $this->config->getCachedReviewsPath()."/".$prodId.".lock.json";
        $result = unlink($pathName);
        if (!$result) {
            throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException("Lock removal failed for path: ".$pathName);
        }
    }

    /**
     * Read Single file if it exists
     *
     * @param $path
     * @return string
     * @throws Foxrate_Sdk_ApiBundle_Exception_ModuleException
     */
    protected function ReadFileContents($path)
    {
        if (is_file($path)) {
            $content = file_get_contents($path);
            if (!$content) {
                throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException("Error: Failed to load data from file: " . $path);
            }
        } else {
            throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException("Error: File not found: " . $path);
        }
        return $content;
    }

    /**
     * Loads review's general info from cache
     *
     * @param $prodId
     * @return bool|mixed
     * @throws Foxrate_Sdk_ApiBundle_Exception_ModuleException
     */
    public function loadProductsRevsGeneral_Cache($prodId)
    {
        $path = $this->prodRevFilenameBuilder($prodId, "general");
        try {
            $content = json_decode($this->ReadFileContents($path));

        } catch (Foxrate_Sdk_ApiBundle_Exception_ModuleException $e) {
            $this->config->writeToLog($e->getMessage() ." product: ".$prodId);
            throw new Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException("Reviews for this product are not found");
        }
        return $content;
    }


    /**
     * Loads single review page from cache
     * @param $prodId
     * @param int $page
     *
     * @return mixed
     * @throws Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException
     */
    public function loadCachedProductReviews($prodId, $page = 1)
    {
        $path = $this->prodRevFilenameBuilder($prodId, "page", $page, "json");
        $reviewCollection = array();
        try {
            $arrContent = json_decode($this->ReadFileContents($path));
            foreach ($arrContent->reviews as $review) {
                $reviewCollection[] = $this->productReviewsFactory->fromStdObject($review);
            }
            $arrContent->reviews = $reviewCollection;
        } catch (Foxrate_Sdk_ApiBundle_Exception_ModuleException $e) {
            $this->config->writeToLog($e->getMessage() ." product: ".$prodId);
            throw new Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException("Product review info not found.");
        }
        return $arrContent;
    }

    /**
     * Builds review file path from parameters
     *
     * @param $prodId
     * @param string $type
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
        return $this->config->getCachedReviewsPath() . "/" . $prodId . "." . $typeStr . "." . $format;
    }

    /**
     * Checks if cache on demand is locked by another instance, which might be in import progress.
     * If lock was not removed some time ago, it is assumed something broke and lock was not removed properly,
     * so the lock is treated as invalid/expired
     *
     * @param $productId
     * @param string $format
     * @return bool
     */
    public function isCacheLocked_CacheDemand($productId, $format = 'json')
    {
        $lockPath = $this->config->getCachedReviewsPath()."/".$productId.".lock.".$format;
        if (!file_exists($lockPath)) {
            return false;
        }
        $lockTimeRaw = file_get_contents($lockPath);
        $lockTimeArr = json_decode($lockTimeRaw);
        $lockTime = $lockTimeArr->lock_time;
        $currentTime = strtotime("- {$this->sReviewsExpireLockCacheDemand} minutes");
        if ($lockTime <= $currentTime) {
            $this->unlockOnDemandCacheProduct($productId);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Stores given data to main cache
     * @param $name
     * @param $data
     * @param string $format
     * @throws Foxrate_Sdk_ApiBundle_Exception_ModuleException
     */
    protected function storeToMainCache($name, $data, $format = "json")
    {
        $pathName = $this->config->getCachedReviewsPath() . "/" . $name . "." . $format;
        $saveResponse = file_put_contents($pathName, $data);
        if (!$saveResponse) {
            $this->config->writeToLog("Warning: Couldn't save data to main cache directory: " . $pathName);
        }
    }

} 