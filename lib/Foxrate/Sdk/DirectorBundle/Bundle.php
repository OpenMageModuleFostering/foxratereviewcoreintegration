<?php


class Foxrate_Sdk_DirectorBundle_Bundle extends Foxrate_Sdk_FrameworkBundle_BaseBundle
{
    public function boot()
    {
        $this->container->set('director.filter_seller_rating', array($this, 'getFoxrate_Director_Filter_SellerRating'));
    }

    public function getFoxrate_Director_Filter_SellerRating()
    {
        return new Foxrate_Sdk_DirectorBundle_Filter_SellerRating(
            $this->container->getParameter('sellerRatingMinDate'),
            $this->container->getParameter('sellerRatingMaxDate'),
            $this->container->get('data_source')
        );
    }
}
