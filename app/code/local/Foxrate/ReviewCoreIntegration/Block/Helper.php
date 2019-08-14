<?php
class Foxrate_ReviewCoreIntegration_Block_Helper extends Mage_Review_Block_Helper
{
    protected $foxrateAvailableTemplates = array(
        'default'   => 'foxrate/rating/detailed.phtml',
        'short'     => 'foxrate/review/helper/summary_short.phtml',
        //'short'     => 'foxrate/rating/detailed.phtml'
    );

    protected $entityId;

    protected $foxrateReviewModel;

    protected $reviewTotalsModel;

    protected $reviewTotalsData;

    protected $writeReviewLink;

    protected $useMagentoDefaultPage = false;

    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Displays rating box in product page (not reviews)
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->useMagentoDefaultPage === true) {
            parent::_toHtml();
        }

        try {

            $reviewTotals = $this->createReviewTotalsModel($this->getEntityId());

            $this->assign('reviewLink', $this->writeReviewLink);
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

        } catch (Foxrate_Sdk_ApiBundle_Exception_Communicate $e) {
            $this->setTemplate('review/helper/summary.phtml');
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
        try {
           $this->writeReviewLink = $this->getKernel()->get('rci.review')->getWriteReviewLink($this->getEntityId());
        } catch (Foxrate_Sdk_ApiBundle_Exception_Communicate $e) {
            $this->useMagentoDefaultPage = true;
            return parent::getSummaryHtml($product, $templateType, $displayIfNoReviews);
        }

        $this->assign('addReviewsLink', true);

        $this->_availableTemplates = $this->foxrateAvailableTemplates;
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
     *
     *
     * @param $productId
     * @return Foxrate_Sdk_FoxrateRCI_ReviewTotals
     */
    public function createReviewTotalsModel($productId)
    {
        $this->reviewTotalsModel = new Foxrate_Sdk_FoxrateRCI_ReviewTotals(
            $this->getKernel()->get("rci.review")
        );

        $this->reviewTotalsModel->setProductId($productId);

        return $this->reviewTotalsModel;
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

    /**
     * @return Foxrate_Kernel
     */
    private function getKernel()
    {
        return Mage::getModel('reviewcoreintegration/kernelloader')->getKernel();
    }
}
