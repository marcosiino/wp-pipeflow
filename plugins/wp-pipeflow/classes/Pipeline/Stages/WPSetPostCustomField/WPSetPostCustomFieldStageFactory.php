<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSetPostCustomField/WPSetPostCustomFieldStage.php";

class WPSetPostCustomFieldStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPSetPostCustomFieldStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Sets a custom field for a specified post.";
        $setupParameters = array(
            "postId" => "The ID of the post to update.",
            "customFieldName" => "The name of the custom field to set. Note: if the specified custom fields does not exists, it will be created.",
            "customFieldValue" => "The value of the custom field to set.",
        );

        $contextInputs = array();
        $contextOutputs = array();

        return new StageDescriptor("WPSetPostCustomField", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}