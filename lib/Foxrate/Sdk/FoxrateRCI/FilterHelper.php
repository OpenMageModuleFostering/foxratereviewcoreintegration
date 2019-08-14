<?php

class Foxrate_Sdk_FoxrateRCI_FilterHelper extends Foxrate_Sdk_FoxrateRCI_Settings
{

    protected $reviewModel;
    protected $dataManager;
    protected $foxrateGeneralData;
    protected $request;
    protected $processedReviews;

    function __construct($config, $dataManager, $reviewModel, Foxrate_Sdk_FoxrateRCI_RequestInterface $request)
    {
        $this->config = $config;
        $this->dataManager = $dataManager;
        $this->reviewModel = $reviewModel;
        $this->request = $request;

        $this->setSettings();
    }

    /**
     * Processed reviews from variety of users
     *
     * @return array
     */
    public function processProductReviews()
    {
        $filter = array();
        $page = $this->request->takeParameter("page");
        $productId = $this->request->takeParameter("product");
        $filter['star_filter'] = $this->request->takeParameter("star_filter");
        $filter['sort'] = $this->request->takeParameter("sort");
        $filter['search'] = $this->request->takeParameter("frsearch");

        $pageRevInfo = $this->getFilteredProductRevs($productId, $page, $filter);

        $this->processedReviews = $pageRevInfo;
        return $this->processedReviews;
    }

    /**
     * Returns filtered reviews by search keyword, star ratings, sorting criteria
     *
     * @param $prodId
     * @param int $page
     * @param $filter
     * @return array|bool|string
     */
    private function getFilteredProductRevs($prodId, $page=1, $filter)
    {
        $activeFilter = false;
        $innerFilter = array();
        foreach($filter as $key => $condition)
        {
            if((isset($key)) && (isset($condition)) && $condition!=""){
                $activeFilter = true;
                $innerFilter[$key] = $condition;
            }
        }

        if($activeFilter){
            $allRevs = $this->reviewModel->loadProductsAllRevs_Cache($prodId);
            $revsPerPage = $this->sFoxrateSettings['foxratePR_RevsPerPage'];

            foreach($innerFilter as $key => $condition){
                $allRevs->reviews = $this->applyFilterForRevs($allRevs->reviews, $key, $condition);
                $allRevs->reviews_count = count($allRevs->reviews);
                $allRevs->pages_count = ceil($allRevs->reviews_count/$revsPerPage);
            }

            if($allRevs->pages_count > 1){
                $allRevs = $this->applyFilterForRevs($allRevs, 'page', $page);
            }else{
                $allRevs->current_page = 1;
                $allRevs->pages_count = 1;
            }
        }else{

            $allRevs = $this->dataManager->loadCachedProductReviews($prodId, $page);
        }
        return $allRevs;
    }

    /**
     * Applies filtering rules on given reviews
     */
    private function applyFilterForRevs($revs, $filterRule, $filterVal)
    {

        $foxrateFiltering = new Foxrate_Sdk_FoxrateRCI_Filter();
        $finalRevs ="";

        switch($filterRule)
        {
            case "star_filter":
                $finalRevs = $foxrateFiltering->filter($filterVal, $revs, 'filterRevs_Ratings');
                if (empty($finalRevs)) {
                    throw new Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException('No products found with selected star count');
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
                    throw new Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException('Could not find any product with given keyword');
                }
                break;
            case "page":
                $currPageIndex = $filterVal;
                $filterVal--;
                $revsPerPage = $this->sFoxrateSettings['foxratePR_RevsPerPage'];
                $finalRevs->reviews = array_slice($revs->reviews, $filterVal*$revsPerPage, $revsPerPage);
                $finalRevs->reviews_count = $revs->reviews_count;
                $finalRevs->pages_count = $revs->pages_count;
                $finalRevs->current_page = $currPageIndex;
                break;
            default:
                $finalRevs = $revs;
                break;
        }
        return $finalRevs;
    }

    public function isError()
    {
        return isset($this->processedReviews->error);
    }

    public function getReviewList()
    {
        $data = $this->getProcessedReviews();
        return $data->reviews;
    }

    public function isNotEmptyReviewList()
    {
        return count($this->getReviewList()) > 0;
    }

    /**
     * @param mixed $processedReviews
     */
    public function setProcessedReviews($processedReviews)
    {
        $this->processedReviews = $processedReviews;
    }

    /**
     * @return mixed
     */
    public function getProcessedReviews()
    {
        return $this->processedReviews;
    }

    /**
     * @return mixed
     */
    public function getReviewModel()
    {
        return $this->reviewModel;
    }

    /**
     * Extracts date from specific format
     * @param $date
     * @return mixed
     */
    public function calcReviewDate($date)
    {
        return $this->getReviewModel()->calcReviewDate($date);
    }

    public function getProductReviewList() {
        return $this->processedReviews->reviews;
    }

}