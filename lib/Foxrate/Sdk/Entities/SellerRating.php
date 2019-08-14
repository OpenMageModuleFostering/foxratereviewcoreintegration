<?php

class Foxrate_Sdk_Entities_SellerRating extends Foxrate_Sdk_Entities_Feedback
{
    public $recommends;

    /**
     * @readonly
     * @var stdClass
     */
    public $votes;

    public function __construct()
    {
        parent::__construct();
        $this->votes = new stdClass();
    }
}
