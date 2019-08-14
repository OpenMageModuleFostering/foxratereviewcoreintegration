<?php


class Foxrate_Sdk_Entities_Order
{
    /**
     * @readonly
     * @var int
     */
    public $id;

    /**
     * @var Foxrate_Sdk_Entities_Customer
     */
    public $customer;

    /**
     * @var DateTime
     */
    public $created;

    /**
     * @var Foxrate_Sdk_Entities_Channel
     */
    public $channel;

    public function __construct()
    {
        $this->channel = new Foxrate_Sdk_Entities_Channel();
        $this->customer = new Foxrate_Sdk_Entities_Customer();
    }

}
