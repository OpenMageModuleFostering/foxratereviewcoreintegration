<?php


class Foxrate_ReviewCoreIntegration_Helper_Filter extends Mage_Core_Helper_Abstract
{

    private $value;

    public function __construct()
    {

        $this->regExpDateTimeFromApi = "/(\d+-\d+-\d+)T(\d+:\d+:\d+)\+\d+/";
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function filter($needle, $array, $callback)
    {
        $this->setValue($needle);
        return array_filter($array, array($this, $callback));
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
        $star = $review['stars'];
        $applyFilter = false;

        if($star == $this->value)
        {
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
        $reviewText['comment_pros'] = $review['comment_pros'];
        $reviewText['comment_cons'] = $review['comment_cons'];
        $reviewText['comment_conclusion'] = $this->getExistingFieldValue($review, 'comment_conclusion');
        $reviewText['comment'] = $this->getExistingFieldValue($review, 'comment');
        $applyFilter = false;

        foreach($reviewText as $singleText){
            $temp = strstr($singleText, $this->value);
            if(strstr($singleText, $this->value)){
                $applyFilter = true;
            }

        }

        return $applyFilter;
    }


    /**
     * Sorting by variuos criteria
     * @param $review1
     * @param $review2
     * @return int
     */
    public function filterRevs_Sorting($review1, $review2)
    {
        switch($this->value)
        {
            case "rate_asc":
                if($review1['stars'] == $review2['stars'])
                {
                    return 0;
                }
                if($review1['stars'] < $review2['stars'])
                {
                    return -1;
                }else{
                    return 1;
                }
                break;
            case "rate_desc":
                if($review1['stars'] == $review2['stars'])
                {
                    return 0;
                }
                if($review1['stars'] > $review2['stars'])
                {
                    return -1;
                }else{
                    return 1;
                }
                break;
            case "date_asc":
                $date1 = $review1['date'];
                $date2 = $review2['date'];
                $matches1 = preg_match($this->regExpDateTimeFromApi, $date1, $resultDate1);
                $matches2 = preg_match($this->regExpDateTimeFromApi, $date2, $resultDate2);


                if($matches1 && $matches2)
                {
                    $unixTime1 = strtotime($resultDate1[1]." ".$resultDate1[2]);
                    $unixTime2 = strtotime($resultDate2[1]." ".$resultDate2[2]);
                }

                if($unixTime1 == $unixTime2)
                {
                    return 0;
                }
                if($unixTime1 < $unixTime2)
                {
                    return -1;
                }else{
                    return 1;
                }
                break;
            case "date_desc":
                $date1 = $review1['date'];
                $date2 = $review2['date'];
                $matches1 = preg_match($this->regExpDateTimeFromApi, $date1, $resultDate1);
                $matches2 = preg_match($this->regExpDateTimeFromApi, $date2, $resultDate2);


                if($matches1 && $matches2)
                {
                    $unixTime1 = strtotime($resultDate1[1]." ".$resultDate1[2]);
                    $unixTime2 = strtotime($resultDate2[1]." ".$resultDate2[2]);
                }

                if($unixTime1 == $unixTime2)
                {
                    return 0;
                }
                if($unixTime1 > $unixTime2)
                {
                    return -1;
                }else{
                    return 1;
                }
                break;
            default:
                throw new Exception('Could not sort product by this criteria');
                break;

        }
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
}