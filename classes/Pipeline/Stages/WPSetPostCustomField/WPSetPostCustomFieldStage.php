<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

class WPSetPostCustomFieldStage extends AbstractPipelineStage
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
        $customFieldName = $this->stageConfiguration->getSettingValue("customFieldName", $context, true);
        $customFieldValue = $this->stageConfiguration->getSettingValue("customFieldValue", $context, true);

        update_post_meta($postId, $customFieldName, $customFieldValue);

        return $context;
    }
}