<?php


class Foxrate_Sdk_ListBundle_Bundle extends Foxrate_Sdk_FrameworkBundle_BaseBundle
{
    public function boot()
    {
        $this->container->set('list.seller_rating', array($this, 'getFoxrate_List_Seller_Rating'));
        $this->container->set('list.product_review', array($this, 'getFoxrate_List_Product_Review'));
        $this->container->set('list.cumulative_channel', array($this, 'getFoxrate_List_Cumulative_Channel'));
        $this->container->set('list.channel', array($this, 'getFoxrate_List_Channel'));
        $this->container->set('list.account', array($this, 'getFoxrate_List_Account'));
    }

    public function getFoxrate_List_Seller_Rating()
    {
        return new Foxrate_Sdk_ListBundle_SellerRating(
            $this->container->get('data_source')
        );
    }

    public function getFoxrate_List_Product_Review()
    {
        return new Foxrate_Sdk_ListBundle_ProductReview(
            $this->container->get('data_source')
        );
    }

    public function getFoxrate_List_Cumulative_Channel()
    {
        return new Foxrate_Sdk_ListBundle_CumulativeChannel(
            $this->container->get('data_source')
        );
    }

    public function getFoxrate_List_Account()
    {
        return new Foxrate_Sdk_ListBundle_Account(
            $this->container->get('data_source')
        );
    }

    public function getFoxrate_List_Channel()
    {
        return new Foxrate_Sdk_ListBundle_Channel(
            $this->container->get('data_source')
        );
    }
}
