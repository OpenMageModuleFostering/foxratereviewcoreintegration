<?php


class Foxrate_Sdk_FoxrateRci_DataManager
{


    protected $config;

    function __construct($config)
    {
        $this->config = $config;

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
    public function unlockOnDemandCache_Product($prodId)
    {
        $pathName = $this->config->getCachedReviewsPath()."/".$prodId.".lock.json";
        $result = unlink($pathName);
        if(!$result)
        {
            throw new Exception("Lock removal failed for path: ".$pathName);
        }
    }

    /**
     * Read Single file if it exists
     *
     * @param $path
     * @return string
     * @throws Exception
     */
    protected function ReadFileContents($path)
    {
        if (is_file($path)) {
            $content = file_get_contents($path);
            if(!$content)
            {
                throw new Exception("Error: Failed to load data from file: " . $path);
            }
        } else {
            throw new Exception("Error: File not found: " . $path);
        }
        return $content;
    }

    /**
     * Loads review's general info from cache
     *
     * @param $prodId
     * @return bool|mixed
     * @throws Exception
     */
    public function loadProductsRevsGeneral_Cache($prodId)
    {
        $revGen = false;
        $path = $this->prodRevFilenameBuilder($prodId, "general");
        try
        {
            $rawContent = $this->ReadFileContents($path);
            $revGen = json_decode($rawContent);
        }
        catch(Exception $e)
        {
            $this->config->writeToLog($e->getMessage() ." product: ".$prodId);
            throw new Exception("Reviews for this product are not found");
        }
        return $revGen;
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

        try
        {
            $rawContent = $this->ReadFileContents($path);
            $arrContent = json_decode($rawContent);
            $revPage = $arrContent;
        }
        catch(Exception $e)
        {
            $this->config->writeToLog($e->getMessage() ." product: ".$prodId);
            throw new Exception("Product review info not found.");
        }
        return $revPage;
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
    protected function isCacheLocked_CacheDemand($productId, $format = 'json')
    {
        $lockPath = $this->config->getCachedReviewsPath()."/".$productId.".lock.".$format;
        if(!file_exists($lockPath)){
            return false;
        }
        $lockTimeRaw = file_get_contents($lockPath);
        $lockTimeArr = json_decode($lockTimeRaw);
        $lockTime = $lockTimeArr->lock_time;
        $currentTime = strtotime("- {$this->sReviewsExpireLockCacheDemand} minutes");
        if($lockTime <= $currentTime){
            $this->unlockOnDemandCache_Product($productId);
            return false;
        }else{
            return true;
        }
    }

} 