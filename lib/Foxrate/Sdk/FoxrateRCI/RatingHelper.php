<?php
class Foxrate_Sdk_FoxrateRCI_RatingHelper
{
    protected $review;

    function __construct(Foxrate_Sdk_FoxrateRCI_ReviewInterface $review, $reviewTotals)
    {
        $this->review = $review;
        $this->reviewTotals = $reviewTotals;
    }

    public function getRatingStars($productId)
    {
        $productPage = $this->review->getReviewTotalDataById($productId);
        if (isset($productPage['error']))
        {
            return $productPage['error'];
        }
        return '<div class="rating" style="width:' . $this->formatCalcPercent($productPage['average'], 5) . '%"></div>';
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
    }
}