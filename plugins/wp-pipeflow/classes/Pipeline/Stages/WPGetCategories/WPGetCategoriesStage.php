<?php


require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";

class WPGetCategoriesStage extends AbstractPipelineStage
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
        $taxonomy = $this->stageConfiguration->getSettingValue("taxonomy", $context, false, null);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        $params = array(
            'orderby' => 'name',
            'order'   => 'ASC'
        );

        if(isset($taxonomy)) {
            $params['taxonomy'] = $taxonomy;
        }

        $wpCategories = get_categories($params);
        if(is_wp_error($wpCategories)) {
            throw new PipelineExecutionException("Error getting wordpress categories: " . $wpCategories->get_error_message());
        }
        $result = array();
        foreach($wpCategories as $cat) {
            $result[] = array(
                'id' => $cat->term_id,
                'name' => $cat->name,
                'slug' => $cat->slug,
            );
        }

        $context->setParameter($resultTo, $result);
        return $context;
    }
}