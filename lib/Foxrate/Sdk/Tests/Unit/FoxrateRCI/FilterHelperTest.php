<?php
namespace Foxrate\Sdk\Tests\Unit\FoxrateRCI;


class FilterHelperTest extends \PHPUnit_Framework_TestCase
{
    private $configMock;
    private $dataManagerMock;
    private $requestMock;

    public function setup()
    {
        $feedbackInfo = new \stdClass();
        $productReviewEntity = new \Foxrate_Sdk_Entities_ProductReview();
        $productReviewEntity->id = 1228;
        $feedbackInfo->reviews = array($productReviewEntity);

        $this->requestMock = $this->getMock('Foxrate_Sdk_FoxrateRCI_RequestInterface');
        $this->requestMock
            ->expects($this->any())
            ->method('takeParameter')
            ->will($this->returnValue(''));

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
        $filterHelper = new \Foxrate_Sdk_FoxrateRCI_FilterHelper(
            $this->configMock,
            $this->dataManagerMock,
            null,
            $this->requestMock
        );

        $feedbackCollection = $filterHelper->processProductReviews(1);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_ProductReview', $feedbackCollection->reviews[0]);
        $this->assertEquals(1228, $feedbackCollection->reviews[0]->id);
    }

    /**
     * @expectedException \Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException
     */
    public function testReturnsError()
    {
        $dataManagergMock = $this
            ->getMockBuilder('Foxrate_Sdk_FoxrateRCI_DataManager')
            ->disableOriginalConstructor()
            ->getMock();
        $dataManagergMock
            ->expects($this->any())
            ->method('loadCachedProductReviews')
            ->will($this->throwException(new \Foxrate_Sdk_ApiBundle_Exception_ReviewsNotFoundException('Reviews not found')));

        $filterHelper = new \Foxrate_Sdk_FoxrateRCI_FilterHelper(
            $this->configMock,
            $dataManagergMock,
            null,
            $this->requestMock
        );

        $filterHelper->processProductReviews(1);
    }
}
