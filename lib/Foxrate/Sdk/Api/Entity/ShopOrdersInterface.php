<?php
/**
 * class FoxrateApiTomatoCart_ShopOrdersInterface
 *
 * Interface to return orders of shop
 */
interface Foxrate_Sdk_Api_Entity_ShopOrdersInterface
{
    /**
     * Get orders from shop and return as foreachable array
     * @param $days
     * @return array
     */
    public function getOrders($days);
}