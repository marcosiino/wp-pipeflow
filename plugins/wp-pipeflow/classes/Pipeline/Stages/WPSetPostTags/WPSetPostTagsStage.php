<?php


require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";

class WPSetPostTagsStage extends AbstractPipelineStage
{
    private StageConfiguration $stageConfiguration;

    public function __construct(StageConfiguration $stageConfiguration)
    {
        $this->stageConfiguration = $stageConfiguration;
    }

    /**
     * @inheritDoc
     */
    public function execute(PipelineContext $context): PipelineContext
    {
        $postId = $this->stageConfiguration->getSettingValue("postId", $context, true);
        $tagsIds = $this->stageConfiguration->getSettingValue("tags", $context, true);

        if(!is_array($tagsIds)) {
            throw new PipelineExecutionException("tags parameter must be an array");
        }

        // Converts to an array of integer to avoid issues with wp_set_post_taggs
        $intTagIds = array();
        foreach($tagsIds as $tagId) {
            if(!is_numeric($tagId)) {
                throw new PipelineExecutionException("tags parameter must be an array of integer numeric values. Any other type is not admitted.");
            }

            $intTagIds[] = (int)$tagId;
        }

        wp_set_post_tags($postId, $intTagIds);
        return $context;
    }
}