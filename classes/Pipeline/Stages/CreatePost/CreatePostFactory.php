<?php

namespace Pipeline\Stages\CreatePost;
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\StageConfiguration\StageConfiguration;

class CreatePostFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new CreatePostStage();
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Creates a new wordpress post.";
        $setupParameters = array(
            "publishStatus" => "The post publication status. Available values: `draft`, `publish`",
            "authorId" => "(optional) If set, specifies the name of the context parameter where the id of the author to assign to this post is specified",
            "categoriesIds" => "(optional) If set, specifies the name of the context parameter where the ids of the categories to assign to this post are stored",
            "tagsIds" => "(optional) If set, specifies the name of the context parameter where the ids of the tags to assign to this post are stored",
            "outputTo" => "(optional) If set, the id of the created post is stored in the context parameter specified in this parameter. If not set, the id of the post is stored in the default `CREATED_POST_ID` context parameter",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "CREATED_POST_ID" => "If resultTo setup parameter is not set, the id of the created post is stored in this context parameter, otherwise it is stored in the context parameter specified in the `resultTo` field",
        );

        return new StageDescriptor("SaveMedia", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}