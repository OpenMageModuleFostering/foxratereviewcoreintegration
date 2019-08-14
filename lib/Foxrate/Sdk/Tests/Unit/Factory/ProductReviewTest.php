<?php
namespace Foxrate\Sdk\Tests\Unit\Factory;


class ProductReviewTest extends \PHPUnit_Framework_TestCase
{
    private $productReviewFactory;

    private $stdObject;

    public function setup()
    {
        $this->productReviewFactory = new \Foxrate_Sdk_Factory_ProductReview();
        $this->stdObject = new \stdClass();
        $this->stdObject->this_is_useful = new \stdClass();
    }

    public function testConvertsStdToProductReviewEntity()
    {
        $productReviewEntity = $this->productReviewFactory->fromStdObject($this->stdObject);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_ProductReview', $productReviewEntity);
    }

    public function testConvertsRecommendsField()
    {
        $this->stdObject->recommends_for_others = "1";
        $productReviewEntity = $this->productReviewFactory->fromStdObject($this->stdObject);
        $this->assertEquals(1, $productReviewEntity->recommends);

    }

    public function testConvertsVotePositiveField()
    {
        $this->stdObject->this_is_useful->yes = "1";
        $productReviewEntity = $this->productReviewFactory->fromStdObject($this->stdObject);
        $this->assertEquals(1, $productReviewEntity->votes->positive);
    }


    public function testConvertsVoteNegativeField()
    {
        $this->stdObject->this_is_useful->no = "1";
        $productReviewEntity = $this->productReviewFactory->fromStdObject($this->stdObject);
        $this->assertEquals(1, $productReviewEntity->votes->negative);
    }

    public function testConvertsVoteTotalField()
    {
        $this->stdObject->this_is_useful->total = "1";
        $productReviewEntity = $this->productReviewFactory->fromStdObject($this->stdObject);
        $this->assertEquals(1, $productReviewEntity->votes->total);
    }

    public function testParentEntityField()
    {
        $this->stdObject->id = '1234';
        $feedbackEntity = $this->productReviewFactory->fromStdObject($this->stdObject);
        $this->assertEquals('1234', $feedbackEntity->id);
    }

}
