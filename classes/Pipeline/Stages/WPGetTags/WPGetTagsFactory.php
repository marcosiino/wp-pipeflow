<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetTags/WPGetTagsStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

class WPGetTagsFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPGetTagsStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Gets the wordpress tags available in this site and assign them to an array context parameter.";
        $setupParameters = array(
            "taxonomy" => "(optional) The taxonomy for which you want to get the tags. If not specified the Post's tags are returned",
            "resultTo" => "The name of the context parameter where the array containing the tags is saved.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "" => "The parameter specified in the resultTo setting will be saved as an array where each item is an associative array with the `id`, `name`, and 'slug' keys for each tag",
        );

        return new StageDescriptor("WPGetTags", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}