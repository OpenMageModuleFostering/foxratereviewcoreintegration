<?php


class Foxrate_Sdk_ServiceBundle_Bundle extends Foxrate_Sdk_FrameworkBundle_BaseBundle
{
    public function boot()
    {
        $this->initParameters();

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            throw new Exception("PHP Version 5.3.0 or higher is needed for ServiceBundle on SdkKernel");
        }
        $this->container->set('seller_rating', array($this, 'getFoxrate_Seller_Rating'));
        $this->container->set('product_review', array($this, 'getFoxrate_Product_Review'));
        $this->container->set('cumulative_channel', array($this, 'getFoxrate_Cumulative_Channel'));
        $this->container->set('order', array($this, 'getFoxrate_Order'));
        $this->container->set('channel', array($this, 'getFoxrate_Channel'));
        $this->container->set('account', array($this, 'getFoxrate_Account'));
        $this->container->set('filter_builder_factory', array($this, 'getFoxrate_FilterBuilder'));
        $this->container->set('count_strategy_resolver', array($this, 'getFoxrate_StrategyResolver'));
        $this->container->set('overall_from_feedback', array($this, 'getFoxrate_OverallFromFeedback'));
        $this->container->set('overall_from_channel_averages', array($this, 'getFoxrate_OverallFromChannelAverages'));

    }

    public function getFoxrate_Seller_Rating()
    {
        return new Foxrate_Sdk_ServiceBundle_SellerRating(
            $this->container->get('list.seller_rating'),
            $this->container->get('data_source'),
            $this->container->get('cumulative_channel'),
            $this->container->get('channel'),
            $this->container->get('count_strategy_resolver'),
            $this->container->get('filter_builder_factory')
        );
    }

    public function getFoxrate_Product_Review()
    {
        return new Foxrate_Sdk_ServiceBundle_ProductReview(
            $this->container->get('list.product_review')
        );
    }

    public function getFoxrate_Order()
    {
        return new Foxrate_Sdk_ServiceBundle_Order(
            $this->container->get('data_source')
        );
    }

    public function getFoxrate_Channel()
    {
        return new Foxrate_Sdk_ServiceBundle_Channel(
            $this->container->get('list.channel'),
            $this->container->get('cumulative_channel')
        );
    }

    public function getFoxrate_Account()
    {
        return new Foxrate_Sdk_ServiceBundle_Account(
            $this->container->get('list.account')
        );
    }

    public function getFoxrate_Cumulative_Channel()
    {
        return new Foxrate_Sdk_ServiceBundle_CumulativeChannel(
            $this->container->get('list.cumulative_channel')
        );
    }

    public function getFoxrate_FilterBuilder()
    {
        return new Foxrate_Sdk_DirectorBundle_Filter_SellerRating(
            $this->container->getParameter('sellerRatingMinDate'),
            $this->container->getParameter('sellerRatingMaxDate'),
            $this->container->get('data_source')
        );
    }

    public function getFoxrate_StrategyResolver()
    {
        return new Foxrate_Sdk_Strategy_OverallStrategyResolver(
            $this->container->get('data_source'),
            $this->container->get('overall_from_feedback'),
            $this->container->get('overall_from_channel_averages')
        );
    }

    public function getFoxrate_OverallFromFeedback()
    {
        return new Foxrate_Sdk_Strategy_OverallFromFeedbacks(
            $this->container->get('cumulative_channel'),
            $this->container->get('channel'),
            $this->container->get('filter_builder_factory'),
            $this->container->get('list.seller_rating')
        );
    }

    public function getFoxrate_OverallFromChannelAverages()
    {
        return new Foxrate_Sdk_Strategy_OverallFromChannelAverages(
            $this->container->get('cumulative_channel'),
            $this->container->get('channel'),
            $this->container->get('filter_builder_factory'),
            $this->container->get('list.seller_rating')
        );
    }

    private function initParameters()
    {
        $this->container->setParameter('sellerRatingMinDate', '-12 months');
        $this->container->setParameter('sellerRatingMaxDate', 'now');
    }
}
