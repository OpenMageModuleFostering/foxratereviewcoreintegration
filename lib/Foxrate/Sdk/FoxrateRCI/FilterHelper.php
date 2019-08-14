<?php

class Foxrate_Sdk_FoxrateRci_FilterHelper extends Foxrate_Sdk_FoxrateRCI_ReviewAbstract
{

    protected $reviewModel;
    protected $dataManager;
    protected $foxrateGeneralData;
    protected $request;
    protected $processedReviews;

    function __construct($config, $dataManager, $reviewModel, $request)
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
        $page = $this->request->post("page");
        $productId = $this->request->post("product");
        $filter['star_filter'] = $this->request->post("star_filter");
        $filter['sort'] = $this->request->post("sort");
        $filter['search'] = $this->request->post("frsearch");

        try
        {
            $objData = $this->getFilteredProductRevs($productId, $page, $filter);
            $pageRevInfo =  $this->reviewModel->convertObjectToArray($objData);
        }
        catch (Exception $e)
        {
            $pageRevInfo = array( "error" => $e->getMessage());
        }
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
                $allRevs[$this->sAPIResRev] = $this->applyFilterForRevs($allRevs[$this->sAPIResRev], $key, $condition);
                $allRevs[$this->sAPIResRevCount] = count($allRevs[$this->sAPIResRev]);
                $allRevs[$this->sAPIResPageCnt] = ceil($allRevs[$this->sAPIResRevCount]/$revsPerPage);
            }
            if($allRevs[$this->sAPIResPageCnt] > 1){
                $allRevs = $this->applyFilterForRevs($allRevs, 'page', $page);
            }else{
                $allRevs[$this->sAPIResPageCnt] = 1;
                $allRevs[$this->sAPIResCurPage] = 1;
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
                if(empty($finalRevs)){
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
                if(empty($finalRevs)){
                    throw new Exception('Could not find any product with given keyword');
                }
                break;
            case "page":
                $currPageIndex = $filterVal;
                $filterVal--;
                $revsPerPage = $this->sFoxrateSettings['foxratePR_RevsPerPage'];
                $finalRevs[$this->sAPIResRev] = array_slice($revs[$this->sAPIResRev], $filterVal*$revsPerPage, $revsPerPage);
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

    public function isError()
    {
        return isset($this->processedReviews['error']);
    }

    public function getReviewList()
    {
        $data = $this->getProcessedReviews();
        return $data['reviews'];
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

}