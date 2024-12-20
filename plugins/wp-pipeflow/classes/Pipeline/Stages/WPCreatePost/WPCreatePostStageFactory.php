<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPCreatePost/WPCreatePostStage.php";

class WPCreatePostStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new WPCreatePostStage($configuration);
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
            "featuredImageId" => "(optional) The media id of the featured image for this post. Must be a valid wordpress media id. You can use the SaveMedia stage to download an image from an URL and gets its media id.",
            "publishStatus" => "(optional, default: draft) The post publication status. Available values: `draft`, `publish`",
            "authorId" => "(optional) If set, specifies the id of the author to assign to this post",
            "categoriesIds" => "(optional, array) If set, specifies an array with the ids of the categories to assign to this post",
            "tagsIds" => "(optional, array) If set, specifies an array with the ids of the tags to assign to this post",
            "resultTo" => "(optional) If set, specifies the context parameter where the id of the created post is stored saved.",
        );
        $contextInputs = array();

        $contextOutputs = array(
            "CREATED_POST_ID" => "If resultTo setup parameter is not set, the id of the created post is stored in this context parameter, otherwise it is stored in the context parameter specified in the `resultTo` field",
        );

        return new StageDescriptor("WPCreatePost", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}