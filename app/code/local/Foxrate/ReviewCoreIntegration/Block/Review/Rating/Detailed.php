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
        //check empty reviews
        try {
            $this->reviewTotalsModel = $this->getKernel()->get('rci.review_totals');
            $this->reviewTotalsModel->setProductId($this->getEntityId());
            $this->reviewTotalsData = $this->reviewTotalsModel->getReviewTotalData($this->getEntityId());

            $this->assign('reviewTotals', $this->reviewTotalsModel);
            $this->assign('reviewLink', $this->getKernel()->get('rci.review')->getWriteReviewLink($this->getEntityId()));
    //        $this->assign('reviewLink', $this->getWriteReviewLink($this->getEntityId()));
            $this->assign('entityId', $this->getEntityId());
            $this->assign('ratingHelper', $this->getKernel()->get('rci.rating_helper'));

            if (0 == $this->reviewTotalsModel()->getTotalReviews())
            {
                $this->setTemplate('rating/empty.phtml');
                return parent::_toHtml();
            }
        } catch (Foxrate_Sdk_ApiBundle_Exception_Communicate $e) {

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
        return $this->getKernel()->get('rci.review')->getWriteReviewLink($prodId);
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function reviewTotalsModel()
    {
        if (null == $this->reviewTotalsModel)
        {
            $this->reviewTotalsModel = $this->getKernel()->get('rci.review_totals');
        }

        return $this->reviewTotalsModel;
    }

    public function getEntityId()
    {
        return Mage::app()->getRequest()->getParam('id');
    }

    public function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }
}
