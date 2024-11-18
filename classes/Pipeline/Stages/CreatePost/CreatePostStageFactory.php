<?php

namespace Pipeline\Stages\CreatePost;
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Stages/CreatePost/CreatePostStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\StageConfiguration\StageConfiguration;

class CreatePostStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new CreatePostStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Creates a new wordpress post.";
        $setupParameters = array(
            "postTitle" => "The title of the post",
            "postContent" => "The content of the post",
            "publishStatus" => "The post publication status. Available values: `draft`, `publish`",
            "authorId" => "(optional) If set, specifies the id of the author to assign to this post",
            "categoriesIds" => "(optional, array) If set, specifies an array with the ids of the categories to assign to this post",
            "tagsIds" => "(optional, array) If set, specifies an array with the ids of the tags to assign to this post",
            "resultTo" => "(optional) If set, specifies the context parameter where the id of the created post is stored saved.",
        );
        $contextInputs = array();

        $contextOutputs = array(
            "CREATED_POST_ID" => "If resultTo setup parameter is not set, the id of the created post is stored in this context parameter, otherwise it is stored in the context parameter specified in the `resultTo` field",
        );

        return new StageDescriptor("CreatePost", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}