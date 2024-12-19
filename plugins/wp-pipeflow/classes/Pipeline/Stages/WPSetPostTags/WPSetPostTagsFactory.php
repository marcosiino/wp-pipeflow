<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSetPostTags/WPSetPostTagsStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

class WPSetPostTagsFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPSetPostTagsStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Set the given tags to an existing post.";
        $setupParameters = array(
            "postId" => "The id of an existing post to which the specified tags will be assigned",
            "tags" => "An array containing the tag ids to assign to the specified post. Must be an array of integers.",
        );

        $contextInputs = array();
        $contextOutputs = array(
        );

        return new StageDescriptor("WPSetPostTags", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}