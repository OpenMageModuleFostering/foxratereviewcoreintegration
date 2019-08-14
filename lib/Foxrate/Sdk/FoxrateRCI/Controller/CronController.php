<?php

class Foxrate_Sdk_FoxrateRCI_Controller_CronController extends Foxrate_Sdk_FrameworkBundle_Controller
{
    public function indexAction() {
        @set_time_limit(0);
        $this->get('rci.review')->importProductReviews();
        return new Foxrate_Sdk_FrameworkBundle_Response('');
    }
}
