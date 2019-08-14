<?php

class Foxrate_Sdk_Factory_ProductReview extends Foxrate_Sdk_Factory_Feedback
{
    public function fromStdObject($stdObject)
    {
        $feedbackEntity = parent::fromStdObject($stdObject);

        if (isset($stdObject->recommends_for_others)) {
            $feedbackEntity->recommends = intval($stdObject->recommends_for_others);
        }

        if (isset($stdObject->this_is_useful->yes)) {
            $feedbackEntity->votes->positive = intval($stdObject->this_is_useful->yes);
        }

        if (isset($stdObject->this_is_useful->no)) {
            $feedbackEntity->votes->negative = intval($stdObject->this_is_useful->no);
        }

        if (isset($stdObject->this_is_useful->total)) {
            $feedbackEntity->votes->total = intval($stdObject->this_is_useful->total);
        }

        return $feedbackEntity;
    }

    protected function getEntity()
    {
        return new Foxrate_Sdk_Entities_ProductReview();
    }
}