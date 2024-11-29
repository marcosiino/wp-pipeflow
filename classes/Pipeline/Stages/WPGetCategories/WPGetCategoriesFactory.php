<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetCategories/WPGetCategoriesStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

class WPGetCategoriesFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPGetCategoriesStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Gets the wordpress categories available in this site and assign them to an array context parameter.";
        $setupParameters = array(
            "taxonomy" => "(optional) The taxonomy for which you want to get the categories. If not specified the Post's categories are returned",
            "resultTo" => "The name of the context parameter where the array containing the categories is saved.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "" => "The parameter specified in the resultTo setting will be saved as an array where each item is an associative array with the `id`, `name`, and 'slug' keys for each category",
        );

        return new StageDescriptor("WPGetCategories", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}