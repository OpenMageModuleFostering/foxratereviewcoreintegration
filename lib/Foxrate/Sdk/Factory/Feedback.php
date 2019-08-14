<?php

class Foxrate_Sdk_Factory_Feedback
{
    private $feedbackWriterFactory;

    public function __construct()
    {
        $this->feedbackWriterFactory = new Foxrate_Sdk_Factory_Feedback_Writer();
    }

    public function fromStdObject($stdObject)
    {
        $feedbackEntity = $this->getEntity();

        if (isset($stdObject->id)) {
            $feedbackEntity->id = $stdObject->id;
        }

        if (isset($stdObject->date)) {
            $feedbackEntity->created = new DateTime($stdObject->date);
        }

        if (isset($stdObject->stars)) {
            $feedbackEntity->ratings->overall = intval($stdObject->stars);
        }

        if (isset($stdObject->comment_pros)) {
            $feedbackEntity->texts->pros = $stdObject->comment_pros;
        }

        if (isset($stdObject->comment_cons)) {
            $feedbackEntity->texts->cons = $stdObject->comment_cons;
        }

        if (isset($stdObject->comment_conclusion)) {
            $feedbackEntity->texts->conclusion = $stdObject->comment_conclusion;
        }

        if (isset($stdObject->comment)) {
            $feedbackEntity->texts->comment = $stdObject->comment;
        }

        if (isset($stdObject->rating_question_second)) {
            $feedbackEntity->ratings->performance = intval($stdObject->rating_question_second);
        }

        if (isset($stdObject->rating_question_third)) {
            $feedbackEntity->ratings->valueForMoney = intval($stdObject->rating_question_third);
        }

        $feedbackEntity->writer = $this->feedbackWriterFactory->fromStdObject($stdObject);

        return $feedbackEntity;
    }

    protected function getEntity()
    {
        return new Foxrate_Sdk_Entities_Feedback();
    }
}