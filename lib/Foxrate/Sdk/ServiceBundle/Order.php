<?php



class Foxrate_Sdk_ServiceBundle_Order
{
    protected $dataSource;

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function save($accountId, $order)
    {
        throw new Exception('Pending implementation');

    }
}
