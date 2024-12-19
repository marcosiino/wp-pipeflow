<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSetPostCategories/WPSetPostCategoriesStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

class WPSetPostCategoriesFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPSetPostCategoriesStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Set the given categories to an existing post.";
        $setupParameters = array(
            "postId" => "The id of an existing post to which the specified categories will be assigned",
            "categories" => "An array containing the categories ids to assign to the specified post. Must be an array of integers.",
        );

        $contextInputs = array();
        $contextOutputs = array(
        );

        return new StageDescriptor("WPSetPostCategories", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}