<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

class WPGetPostCustomFieldStage extends AbstractPipelineStage
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
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        $customFieldValue = get_post_meta($postId, $customFieldName, true);

        $context->setParameter($resultTo, $customFieldValue);
        return $context;
    }
}