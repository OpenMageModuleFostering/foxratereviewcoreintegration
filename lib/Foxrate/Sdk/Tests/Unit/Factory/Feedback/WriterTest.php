<?php
namespace Foxrate\Sdk\Tests\Unit\Factory\Feedback;


class WriterTest extends \PHPUnit_Framework_TestCase
{
    private $feedbackWriterFactory;

    private $stdObject;

    public function setup()
    {
        $this->feedbackWriterFactory = new \Foxrate_Sdk_Factory_Feedback_Writer();
        $this->stdObject = new \stdClass();
    }

    public function testHasWriterEntity()
    {
        $feedbackWriterEntity = $this->feedbackWriterFactory->fromStdObject($this->stdObject);
        $this->assertInstanceOf('Foxrate_Sdk_Entities_Feedback_Writer', $feedbackWriterEntity);
    }

    public function testConvertsNameField()
    {
        $this->stdObject->name = 'Laura';
        $feedbackWriterEntity = $this->feedbackWriterFactory->fromStdObject($this->stdObject);
        $this->assertEquals('Laura', $feedbackWriterEntity->name);
    }

    public function testConvertsIsAnonymousField()
    {
        $this->stdObject->anonymous = true;
        $feedbackWriterEntity = $this->feedbackWriterFactory->fromStdObject($this->stdObject);
        $this->assertEquals(true, $feedbackWriterEntity->isAnonymous);
    }
}
