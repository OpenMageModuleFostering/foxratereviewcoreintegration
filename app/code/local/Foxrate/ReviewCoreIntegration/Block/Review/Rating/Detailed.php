<?php

class Foxrate_ReviewCoreIntegration_Block_Review_Rating_Detailed extends Mage_Core_Block_Template
{

    protected $productPage = array();

    protected $reviewTotalsData;

    protected $reviewTotalsModel;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('foxrate/rating/detailed.phtml');

    }

    protected function _toHtml()
    {
        $entityId = $this->getEntityId();

        //check empty reviews
        $this->reviewTotalsData = $this->reviewTotalsModel()->getReviewTotalData($entityId);

        $this->assign('reviewTotals', $this->reviewTotalsData);
        $this->assign('reviewLink', $this->getWriteReviewLink($entityId));
        $this->assign('entityId', $entityId);

        if (0 == $this->reviewTotalsModel()->getTotalReviews())
        {
            $this->setTemplate('rating/empty.phtml');
            return parent::_toHtml();
        }
        return parent::_toHtml();
    }


    /**
     * Gets link to write user review
     *
     * @param $prodId
     * @return mixed
     * @deprecated This does not add any value. It should used directly.
     */
    public function getWriteReviewLink($prodId)
    {
        return Mage::getModel('reviewcoreintegration/review')->getWriteReviewLink($prodId);
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function reviewTotalsModel()
    {
        if (null == $this->reviewTotalsModel)
        {
            $this->reviewTotalsModel = Mage::getModel('reviewcoreintegration/reviewtotals');
        }

        return $this->reviewTotalsModel;
    }


    /**
     * Get Config Model
     * @return false|Mage_Core_Model_Abstract
     */
    public function getConfig()
    {
        return Mage::getModel('reviewcoreintegration/config');
    }

    public function getEntityId()
    {
        return Mage::app()->getRequest()->getParam('id');
    }


}
