<?php

class Foxrate_Sdk_Entities_Feedback
{
    /**
     * @readonly
     * @var int
     */
    public $id;

    /**
     * @var DateTime
     */
    public $created;

    /**
     * @var Foxrate_Sdk_Entities_Feedback_Writer
     */
    public $writer;

    /**
     * @var string[]
     */
    public $texts;

    /**
     * @var int[]
     */
    public $ratings;

    public function __construct()
    {
        $this->writer = new Foxrate_Sdk_Entities_Feedback_Writer();
        $this->texts = new stdClass();
        $this->ratings = new stdClass();
    }


}