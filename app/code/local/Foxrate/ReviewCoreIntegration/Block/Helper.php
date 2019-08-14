<?php
class Foxrate_ReviewCoreIntegration_Block_Helper extends Mage_Review_Block_Helper
{
    protected $_availableTemplates = array(
        'default'   => 'foxrate/rating/detailed.phtml',
        'short'     => 'foxrate/review/helper/summary_short.phtml',
    );

    protected $entityId;

    protected $foxrateReviewModel;

    protected $reviewTotalsModel;

    protected $reviewTotalsData;

    private static $kernel;

    protected function _construct()
    {
        parent::_construct();
    }

    protected function _toHtml()
    {
        $kernel = $this->getKernel();

        //force new Review Total model
        /** @var Foxrate_ReviewCoreIntegration_Model_Reviewtotals reviewTotalsModel */
//        $this->reviewTotalsModel = Mage::getModel('reviewcoreintegration/reviewtotals');
//        $this->reviewTotals = $this->reviewTotalsModel->getReviewTotalData($this->getEntityId());

        $reviewTotals = $kernel->get('rci.review_totals');
        $reviewTotals->setProductId($this->getEntityId());

        $this->assign('reviewLink', $this->getKernel()->get('rci.review')->getWriteReviewLink($this->getEntityId()));
        $this->assign('reviewTotalsData', $reviewTotals->getReviewTotalData($this->getEntityId()));
        $this->assign('reviewTotals', $reviewTotals);
        $this->assign('ratingHelper', $this->getKernel()->get('rci.rating_helper'));
        $this->assign('processedReviews', $this->getKernel()->get('rci.rating_helper'));
        $this->assign('entityId', $this->getEntityId());

        if (0 == $reviewTotals->getTotalReviews())
        {
            $this->setTemplate('foxrate/rating/empty.phtml');
            return parent::_toHtml();
        }

        return parent::_toHtml();
    }

    /**
     * Helper method to generate view and change behaviour on product page
     *
     * @param $product
     * @param $templateType
     * @param $displayIfNoReviews
     * @return string
     */
    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        $this->assign('addReviewsLink', true);

        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }

        $this->setTemplate($this->_availableTemplates[$templateType]);
        $this->setProduct($product);
        $this->setEntityId($product->getId());

        return $this->_toHtml();
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        if (!isset($this->entityId))
        {
            $this->entityId = Mage::app()->getRequest()->getParam('id');
        }

        return $this->entityId;
    }

    /**
     * @param mixed $entityId
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
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


    public function getKernel()
    {
        if (self::$kernel !== null)
        {
            return self::$kernel;
        }

        $kernel = new Foxrate_Kernel('dev', false);
        $kernel->boot();

        return self::$kernel = $kernel;
    }
}
