<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetPostCustomField/WPGetPostCustomFieldStage.php";

class WPGetPostCustomFieldStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPGetPostCustomFieldStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Gets a custom field value for a specified post and assigns it to a context parameter.";
        $setupParameters = array(
            "postId" => "The ID of the post to retrieve the custom field from.",
            "customFieldName" => "The name of the custom field to retrieve.",
            "resultTo" => "The name of the context parameter where the custom field value is saved.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "" => "The parameter specified in the resultTo setting will be saved as the value of the custom field.",
        );

        return new StageDescriptor("WPGetPostCustomField", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}