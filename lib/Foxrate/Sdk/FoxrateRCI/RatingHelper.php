<?php
class Foxrate_Sdk_FoxrateRCI_RatingHelper
{
    protected $review;

    protected $reviewTotals;

    protected $error;

    function __construct(Foxrate_Sdk_FoxrateRCI_Review $review, $reviewTotals)
    {
        $this->review = $review;
        $this->reviewTotals = $reviewTotals;
    }

    /**
     * General review info about a single product
     * @param productId
     * @return mixed
     */
    public function getFoxrateRatingData($productId)
    {
        try {
            if ($this->review->isSummaryDisbaled()) {
                return $this->review->disabledSummaryError();
            }
            return $this->review->getReviewTotalDataById($productId);

        } catch (Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException $e) {
            $this->error = new Foxrate_Sdk_FoxrateRCI_Error($e->getMessage());
        }
    }

    public function getRatingStars($productId)
    {
        try {
            return '<div class="fi-rating-stars" data-rating="' . $this->getStarPercents($productId) . '">
                        <div class="rating fi-indicator" style="width:' .  $this->getStarPercents($productId) . '%"></div>
                    </div>';
        } catch (Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException $e) {
            return '<!-- Reviews not found -->';
        }
    }

    public function getListRatingStars($productId)
    {
        try {
            return '<div class="fi-rating-stars" data-rating="' . $this->getStarPercents($productId) . '">
                        <div class="rating fi-indicator" style="width:' .  $this->getStarPercents($productId) . '%"></div>
                    </div>';
        } catch (Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException $e) {
            return '<!-- Reviews not found -->';
        }
    }

    public function getStarPercents($productId)
    {
        $productPage = $this->review->getReviewTotalDataById($productId);
        if (isset($productPage['error']))
        {
            return $productPage['error'];
        }
        return $this->formatCalcPercent($productPage['average'], 5);
    }

    /**
     * Calculates percent
     * @param $current
     * @param $total
     * @return mixed
     */
    public function formatCalcPercent($current, $total)
    {
        $percent = $this->reviewTotals->calcPercent($current, $total);
        return number_format($percent, 2, ".", "");
    }

    public function getReviewsUrl()
    {
        return $this->review->getReviewsUrl();
    }

    public function showFoxrateCategoryRatings()
    {
        return $this->review->showFoxrateCategoryRatings();
    }

    /**
     * Gets link to write user review
     * @param $prodId
     * @return mixed
     */
    public function getWriteReviewLink($prodId)
    {
        try {
            return $this->review->getWriteReviewLink($prodId);
        } catch (Foxrate_Sdk_ApiBundle_Exception_Communicate $e) {
            //todo We should output: "Setup auth process is not finished" error message ?

        } catch (Foxrate_Sdk_ApiBundle_Exception_ModuleException $e) {

        }
    }

    /**
     * Get Foxrate general error
     * @return mixed
     */
    public function getFoxrateFiError(){
        return $this->error;
    }
}