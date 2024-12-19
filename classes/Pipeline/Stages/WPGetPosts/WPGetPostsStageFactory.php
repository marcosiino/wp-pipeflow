<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetPosts/WPGetPostsStage.php";

class WPGetPostsStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPGetPostsStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Gets posts from WordPress and assigns them to an associative array context parameter. Each post in the array will have the keys corresponding to the fields to retrieve specified in the fields setting parameter.";
        $setupParameters = array(
            "postType" => "(optional) The type of posts to retrieve. Default is 'post'.",
            "limit" => "(optional) The maximum number of posts to retrieve. Set to -1 to return all the posts. Default is 20.",
            "fields" => "(optional) A comma separated list containing the fields to return for each post. The following fields are available: 'id', 'title', 'excerpt', 'status', 'content', 'author', 'post_date'. You can also specify custom fields. Default is: id,title",
            "resultTo" => "The name of the context parameter where the associative array containing the posts is saved.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "" => "The parameter specified in the resultTo setting will be saved as an array where each item is an associative array containing the keys specified in the fields setting parameter",
        );

        return new StageDescriptor("WPGetPosts", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}