<?php
namespace Foxrate\Sdk\Tests\Unit\FoxrateRCI;


class ProcessReviewsTest extends \PHPUnit_Framework_TestCase
{
    private $processReviews;

    public function setup()
    {
        $feedbackInfo = new \stdClass();
        $productReviewEntity = new \Foxrate_Sdk_Entities_ProductReview();
        $productReviewEntity->id = 1228;
        $feedbackInfo->reviews = array($productReviewEntity);

        $reviewModel = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_Review')
            ->disableOriginalConstructor()
            ->getMock();

        $dataManagergMock = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_DataManager')
            ->setMethods(array('loadCachedProductReviews'))
            ->disableOriginalConstructor()
            ->getMock();

        $dataManagergMock
            ->expects($this->any())
            ->method('loadCachedProductReviews')
            ->will($this->returnValue($feedbackInfo));

        $this->processReviews = new \Foxrate_Sdk_FoxrateRCI_ProcessReviews(
            $dataManagergMock,
            $reviewModel,
            null
        );
    }

    public function testReturnsReviewsAsEntities()
    {
        $feedbackCollection = $this->processReviews->getRawProductReviews(1);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_ProductReview', $feedbackCollection->reviews[0]);
        $this->assertEquals(1228, $feedbackCollection->reviews[0]->id);
    }

    /**
     * @expectedException    \Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException
     */
    public function testReturnsError()
    {
        $reviewModel = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_Review')
            ->disableOriginalConstructor()
            ->getMock();

        $dataManagerMock = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_DataManager')
            ->setMethods(array('loadCachedProductReviews'))
            ->disableOriginalConstructor()
            ->getMock();

        $dataManagerMock
            ->expects($this->any())
            ->method('loadCachedProductReviews')
            ->will($this->throwException(new \Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException('Reviews not found')));

        $processReviews = new \Foxrate_Sdk_FoxrateRCI_ProcessReviews(
            $dataManagerMock,
            $reviewModel,
            null
        );

        $processReviews->getRawProductReviews(1);
    }
}
