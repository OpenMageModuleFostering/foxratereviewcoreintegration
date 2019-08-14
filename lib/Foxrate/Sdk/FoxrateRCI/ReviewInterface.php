<?php

interface Foxrate_Sdk_FoxrateRCI_ReviewInterface
{
    public function getRemoteAddr();

    /**
     * Check if it is Foxrate developer enviroment.
     * @return bool
     */
    public function isDevEnviroment();

    /**
     * Performs product review import from Foxrate using API
     * Loads product reviews from Foxrate and saves them to temporary directory, when all reviews are loaded, they are moved
     * to permanent directory, and temp is cleared. Failsafe strategy is used: If downloading the files fails, the old cache is used (instead of nothing)
     *
     * @return string
     */
    public function importProductReviews();

    public function useDbCompatibleMode();

    /**
     * Gets link to write reviews
     */
    public function getWriteReviewLink($prodId);

    /**
     * Converts object to array
     * @param $data
     * @return array
     */
    public function convertObjectToArray($data);

    public function getFoxrateUrl();

    public function makeMageRequest($url, $username, $password, $headers = null, $params = null);

    public function getFoxrateApiUrl();

    /**
     * Generates sorting criteria for user reviews
     */
    public function getSortingCriteria();

    /**
     * Send voting to foxrate api
     */
    public function voteReview($revId, $useful);

    public function setSettings();

    /**
     * Get general review info about a single product
     *
     * @param $sProductId
     * @return array
     */
    public function getReviewTotalDataById($sProductId);

    function recursive_mkdir($path, $mode = 0777);

    /**
     * Downloads the log
     */
    public function downloadLog();

    /**
     * Calculates date
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date);

    /**
     * Report abuse review to foxrate api
     */
    public function abuseReview($revId, $abuse);

    /**
     * Enables Dev mode, if it is dev enviroment
     * @todo needs to refactored to correct location and unify
     */
    public function enableDevMode();

    /**
     * Loads page navigation, gets neighbouring values calculated from current page number, filtering negative ones,
     * and those which are bigger than the page limit
     * @param $totalPages
     * @param $currentPage
     * @return array
     */
    public function getPageNav($totalPages, $currentPage);

    public function getTotalReviews($generalReview);

    /**
     * Performs reviews import for single product, by checking if reviews are expired by file time modified,
     * locks the update and starts import. Locks so other users wont starts the import again when it is started.
     * Lock has a timeout, if something breaks, the import can be started again when the locks expires.
     */
    public function cacheOnDemand_SingleProdRev($productId);

    /**
     * Load product Ids
     *
     * @return mixed
     */
    public function loadProductIds();

}