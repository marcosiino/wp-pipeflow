<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";

class WPGetPostsStage extends AbstractPipelineStage
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
        $postType = $this->stageConfiguration->getSettingValue("postType", $context, false, 'post');
        $numberOfPosts = $this->stageConfiguration->getSettingValue("limit", $context, false, 20);
        $fieldsStr = $this->stageConfiguration->getSettingValue("fields", $context, false, 'id,title');
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, true);

        $args = array(
            'post_type' => $postType,
            'numberposts' => $numberOfPosts,
        );

        
        $allFields = explode(',', $fieldsStr);
        if(empty($allFields)) {
            throw new PipelineExecutionException("The fields setting parameter must contain at least one field.");
        }

        $fields = array();
        $customFields = array();

        //Separate the fields into core post fields (fields array) and custom fields (customFields array)
        foreach($allFields as $field) {
            if (trim($field) == 'id') {
                $fields[] = 'id';
            } elseif (trim($field) == 'title') {
                $fields[] = 'title';
            } elseif (trim($field) == 'excerpt') {
                $fields[] = 'excerpt';
            } elseif (trim($field) == 'status') {
                $fields[] = 'status';
            } elseif (trim($field) == 'content') {
                $fields[] = 'content';
            } elseif (trim($field) == 'author') {
                $fields[] = 'author';
            } elseif (trim($field) == 'post_date') {
                $fields[] = 'post_date';
            } else {
                $customFields[] = trim($field);
            }
        }
        
        $posts = get_posts($args);
        if (is_wp_error($posts)) {
            throw new PipelineExecutionException("Error getting posts: " . $posts->get_error_message());
        }

        $result = array();
        foreach ($posts as $wpPost) {
            $post = array();
            if (in_array('id', $fields)) {
                $post['id'] = $wpPost->ID;
            }

            if (in_array('title', $fields)) {
                $post['title'] = $wpPost->post_title;
            }

            if(in_array('excerpt', $fields)) {
                $post['excerpt'] = $wpPost->post_excerpt;
            }

            if(in_array('status', $fields)) {
                $post['status'] = $wpPost->post_status;
            }

            if(in_array('content', $fields)) {
                $post['content'] = $wpPost->post_content;
            }

            if(in_array('author', $fields)) {
                $post['author'] = $wpPost->post_author;
            }

            if(in_array('post_date', $fields)) {
                $post['post_date'] = $wpPost->post_date;
            }

            if(!empty($customFields)) {
                foreach($customFields as $customField) {
                    $post[$customField] = get_post_meta($wpPost->ID, $customField, true);
                }
            }

            //Adds the post
            $result[] = $post;
        }

        $context->setParameter($resultTo, $result);
        return $context;
    }
}