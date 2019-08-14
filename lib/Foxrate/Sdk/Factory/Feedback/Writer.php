<?php

class Foxrate_Sdk_Factory_Feedback_Writer
{

    public function fromStdObject($stdObject)
    {
        $feedbackWriterEntity = $this->getEntity();

        if (isset($stdObject->name)) {
            $feedbackWriterEntity->name = $stdObject->name;
        }

        if (isset($stdObject->email)) {
            $feedbackWriterEntity->email = $stdObject->email;
        }

        if (isset($stdObject->anonymous)) {
            $feedbackWriterEntity->isAnonymous = $stdObject->anonymous;
        }

        return $feedbackWriterEntity;
    }

    protected function getEntity()
    {
        return new Foxrate_Sdk_Entities_Feedback_Writer();
    }
}