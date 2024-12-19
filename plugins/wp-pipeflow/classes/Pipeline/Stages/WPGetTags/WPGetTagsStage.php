<?php


require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/PipelineExecutionException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/PipelineContext.php";

class WPGetTagsStage extends AbstractPipelineStage
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

        $wpTags = get_tags($params);
        if(is_wp_error($wpTags)) {
            throw new PipelineExecutionException("Error getting wordpress tags: " . $wpTags->get_error_message());
        }

        $result = array();
        foreach($wpTags as $tag) {
            $result[] = array(
                'id' => $tag->term_id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            );
        }

        $context->setParameter($resultTo, $result);
        return $context;
    }
}