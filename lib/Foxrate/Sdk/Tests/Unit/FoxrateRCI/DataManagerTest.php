<?php
namespace Foxrate\Sdk\Tests\Unit\FoxrateRCI;


class DataManagerTest extends \PHPUnit_Framework_TestCase
{
    private $dataManager;

    public function setup()
    {
        $configMock = $this->getMock('Foxrate_Sdk_FoxrateRCI_ConfigInterface');
        $configMock
            ->expects($this->any())
            ->method('getCachedReviewsPath')
            ->will($this->returnValue(__DIR__ .'/../'));

        $this->dataManager = new \Foxrate_Sdk_FoxrateRCI_DataManager(
            $configMock,
            new \Foxrate_Sdk_ApiBundle_Service_ProductReviewsFactory()
        );
    }

    public function testReviewsInCollection()
    {
        $feedbackCollection = $this->dataManager->loadCachedProductReviews(1);
        $this->assertEquals(true, is_array($feedbackCollection->reviews));
    }

    public function testReviewsAreEntities()
    {
        $feedbackCollection = $this->dataManager->loadCachedProductReviews(1);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_ProductReview', $feedbackCollection->reviews[0]);
    }

    public function testIsReviewFromCache()
    {
        $feedbackCollection = $this->dataManager->loadCachedProductReviews(1);
        $this->assertEquals(1228, $feedbackCollection->reviews[0]->id);
    }

    public function testHasAllReviews()
    {
        $feedbackCollection = $this->dataManager->loadCachedProductReviews(1);
        $this->assertEquals(3, count($feedbackCollection->reviews));
    }
}
