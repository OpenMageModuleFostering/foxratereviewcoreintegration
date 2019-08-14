<?php
namespace Foxrate\Sdk\Tests\Unit\FoxrateRCI;


class ReviewTest extends \PHPUnit_Framework_TestCase
{
    private $configMock;
    private $dataManagerMock;

    public function setup()
    {
        $feedbackInfo = new \stdClass();
        $productReviewEntity = new \Foxrate_Sdk_Entities_ProductReview();
        $productReviewEntity->id = 1228;
        $feedbackInfo->reviews = array($productReviewEntity);
        $feedbackInfo->pages_count = 1;

        $this->configMock = $this->getMock('Foxrate_Sdk_FoxrateRCI_ConfigInterface');
        $this->configMock
            ->expects($this->any())
            ->method('getConfigParam')
            ->will($this->returnValue(''));

        $this->dataManagerMock = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_DataManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataManagerMock
            ->expects($this->any())
            ->method('loadCachedProductReviews')
            ->will($this->returnValue($feedbackInfo));
    }

    public function testReturnsReviewsAsEntities()
    {
        $reviewModel = new \Foxrate_Sdk_FoxrateRCI_Review(
            $this->configMock,
            $this->getMockBuilder('Foxrate_Sdk_ApiBundle_Controllers_Authenticator')
                ->disableOriginalConstructor()
                ->getMock(),
            $this->dataManagerMock,
            $this->getMock('Foxrate_Sdk_FoxrateRCI_ProductInterface'),
            $this->getMock('Foxrate_Sdk_ApiBundle_Resources_ApiEnvironmentInterface')
        );

        $feedbackCollection = $reviewModel->loadProductsAllRevs_Cache(1);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_ProductReview', $feedbackCollection->reviews[0]);
        $this->assertEquals(1228, $feedbackCollection->reviews[0]->id);
    }
}
