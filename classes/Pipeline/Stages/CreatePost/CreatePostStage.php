<?php

class CreatePostStage extends AbstractPipelineStage
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
        $title = $this->stageConfiguration->getSettingValue("postTitle", $context, true);
        $content = $this->stageConfiguration->getSettingValue("postContent", $context, true);
        $publishStatus = $this->stageConfiguration->getSettingValue("publishStatus", $context, false, 'draft');
        $authorId = $this->stageConfiguration->getSettingValue("authorId", $context, false, get_current_user());
        $categoriesIds = $this->stageConfiguration->getSettingValue("categoriesIds", $context, false, array());
        $tagsIds = $this->stageConfiguration->getSettingValue("tagsIds", $context, false, array());
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, false, 'CREATED_POST_ID');

        $new_post = array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => $publishStatus,
            'post_author'   => $authorId,
            'post_category' => $categoriesIds,
            'post_tag' => $tagsIds,
        );

        // Inserisci il post
        $post_id = wp_insert_post($new_post);

        // Verifica che il post sia stato creato correttamente
        if (is_wp_error($post_id)) {
            throw new PipelineExecutionException($post_id->get_error_message());
        } else {
            error_log('Post creato con successo. ID: ' . $post_id);
            $context->setParameter($resultTo, $post_id);
        }

        return $context;
    }
}