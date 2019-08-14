<?php

class Foxrate_Sdk_FoxrateRCI_Filter
{

    private $value;

    public function __construct()
    {

        $this->regExpDateTimeFromApi = "/(\d+-\d+-\d+)T(\d+:\d+:\d+)\+\d+/";
    }

    public function filter($needle, $array, $callback)
    {
        $this->setValue($needle);
        return array_filter($array, array($this, $callback));
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function sort($needle, $array, $callback)
    {
        $this->setValue($needle);
        uasort($array, array($this, $callback));
        return $array;
    }

    /*
     * Filters reviews leaving only the selected star ratings
     */
    public function filterRevs_Ratings($review)
    {
        $star = $review->ratings->overall;
        $applyFilter = false;

        if ($star == $this->value) {
            $applyFilter = true;
        }

        return $applyFilter;
    }

    /** Filters all reviews leaving only one matching search criteria
     * @param $review
     * @return bool
     */

    public function filterRevs_Search($review)
    {

        $reviewText = array();
        $reviewText['comment_pros'] = $review->texts->pros;
        $reviewText['comment_cons'] = $review->texts->cons;
        $reviewText['comment_conclusion'] = isset($review->texts->conclusion) ? $review->texts->conclusion : '';
        $reviewText['comment'] = isset($review->texts->comment) ? $review->texts->comment : '';
        $applyFilter = false;

        foreach ($reviewText as $singleText) {
            $temp = strstr($singleText, $this->value);
            if (strstr($singleText, $this->value)) {
                $applyFilter = true;
            }

        }

        return $applyFilter;
    }

    /**
     * Get field value from given array
     *
     * @param $array
     * @param $name
     * @return string
     */
    protected function getExistingFieldValue($array, $name)
    {

        return isset($array[$name]) ? $array[$name] : '';
    }

    /**
     * Sorting by variuos criteria
     * @param $review1
     * @param $review2
     * @return int
     */
    public function filterRevs_Sorting($review1, $review2)
    {
        switch ($this->value) {
            case "rate_asc":
                if ($review1->ratings->overall == $review2->ratings->overall) {
                    return 0;
                }
                if ($review1->ratings->overall < $review2->ratings->overall) {
                    return -1;
                } else {
                    return 1;
                }
                break;
            case "rate_desc":
                if ($review1->ratings->overall == $review2->ratings->overall) {
                    return 0;
                }
                if ($review1->ratings->overall > $review2->ratings->overall) {
                    return -1;
                } else {
                    return 1;
                }
                break;
            case "date_asc":
                $date1 = $review1->created->format('U');
                $date2 = $review2->created->format('U');

                if ($date1 == $date2) {
                    return 0;
                }
                if ($date1 < $date2) {
                    return -1;
                } else {
                    return 1;
                }
                break;
            case "date_desc":
                $date1 = $review1->created->format('U');
                $date2 = $review2->created->format('U');

                if ($date1 == $date2) {
                    return 0;
                }
                if ($date1 > $date2) {
                    return -1;
                } else {
                    return 1;
                }
                break;
            default:
                throw new Foxrate_Sdk_ApiBundle_Exception_ModuleException('Could not sort product by this criteria');
                break;

        }
    }
}