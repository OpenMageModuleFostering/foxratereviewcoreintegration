<?php
namespace Foxrate\Sdk\Tests\Unit\Factory;


class FeedbackTest extends \PHPUnit_Framework_TestCase
{
    private $feedbackFactory;

    private $stdObject;

    public function setup()
    {
        $this->feedbackFactory = new \Foxrate_Sdk_Factory_Feedback();
        $this->stdObject = new \stdClass();
    }

    public function testConvertsStdToFeedbackEntity()
    {
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_Feedback', $feedbackEntity);
    }

    public function testConvertsIdField()
    {
        $this->stdObject->id = '1234';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('1234', $feedbackEntity->id);
    }

    public function testConvertsDateTypeField()
    {
        $this->stdObject->date = '2012-06-25T10:22:01+0200';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertInstanceOf('DateTime', $feedbackEntity->created);
    }

    public function testConvertsDateField()
    {
        $this->stdObject->date = '2012-06-25T10:22:01+0200';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('2012-06-25 10:22:01', $feedbackEntity->created->format('Y-m-d H:i:s'));
    }

    public function testHasWriterEntity()
    {
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_Feedback_Writer', $feedbackEntity->writer);
    }

    public function testConvertsWritersField()
    {
        $this->stdObject->name = 'Laura';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('Laura', $feedbackEntity->writer->name);
    }

    public function testConvertsOverallRatingField()
    {
        $this->stdObject->stars = 3;
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals(3, $feedbackEntity->ratings->overall);
    }

    public function testConvertsCommentsPros()
    {
        $this->stdObject->comment_pros = 'Comment for test';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('Comment for test', $feedbackEntity->texts->pros);
    }

    public function testConvertsCommentsCons()
    {
        $this->stdObject->comment_cons = 'Comment for test';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('Comment for test', $feedbackEntity->texts->cons);
    }

    public function testConvertsCommentsConclusion()
    {
        $this->stdObject->comment_conclusion = 'Comment for test';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('Comment for test', $feedbackEntity->texts->conclusion);
    }

    public function testConvertsCommentsText()
    {
        $this->stdObject->comment = 'Comment for test';
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals('Comment for test', $feedbackEntity->texts->comment);
    }

    public function testConvertsRatingQuality()
    {
        $this->stdObject->rating_question_second = 2;
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals(2, $feedbackEntity->ratings->performance);
    }

    public function testConvertsRatingValueForMoney()
    {
        $this->stdObject->rating_question_third = 3;
        $feedbackEntity = $this->feedbackFactory->fromStdObject($this->stdObject);
        $this->assertEquals(3, $feedbackEntity->ratings->valueForMoney);
    }
}