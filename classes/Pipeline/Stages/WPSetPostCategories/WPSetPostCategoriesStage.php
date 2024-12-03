<?php


require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";

class WPSetPostCategoriesStage extends AbstractPipelineStage
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
        $categoriesIds = $this->stageConfiguration->getSettingValue("categories", $context, true);

        if(!is_array($categoriesIds)) {
            throw new PipelineExecutionException("categories parameter must be an array");
        }

        // Converts to an array of integer to avoid issues with wp_set_post_categories
        $intCatIds = array();
        foreach($categoriesIds as $catId) {
            if(!is_numeric($catId)) {
                throw new PipelineExecutionException("categories parameter must be an array of integer numeric values. Any other type is not admitted.");
            }

            $intCatIds[] = (int)$catId;
        }

        wp_set_post_categories($postId, $intCatIds);
        return $context;
    }
}