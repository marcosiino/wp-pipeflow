<?php
require_once(PLUGIN_PATH . "utils/defaults.php");
require_once(PLUGIN_PATH . "utils/utils.php");
require_once(PLUGIN_PATH . "classes/OpenAIService.php");

class ArticleGenerator {
    /**
     * Gets the prompt for text generation from settings and adds input parameters
     */
    private function generate_prompt($input_params) {
        $prompt = get_option('article_generation_prompt', TEXT_DEFAULT_PROMPT);
        $prompt .= JSON_COMPLETION_FORMAT_INSTRUCTIONS;

        $final_params = array(
            /*    array(
                    "key" => "CATEGORIES",
                    "value" => json_encode(get_available_categories())
                ),
                array(
                    "key" => "TAGS",
                    "value" => json_encode(get_available_tags())
                ),*/
        );

        if(isset($input_params)) {
            foreach ($input_params as $param) {
                array_push($final_params, $param);
            }
        }

        $prompt = prompt_with_inputs($prompt, $final_params);
        return $prompt;
    }

    /**
     * Generate an article with AI and returns it
     *
     * @param $topic: the coloring page image topic
     * @return GeneratedArticle the generated article data or null on error
     */

    function generate_article($input_params = array(), $attached_image_url) {

        $prompt = $this->generate_prompt($input_params);
        $model = "gpt-4-turbo";
        $aiService = new OpenAIService(get_option('openai_api_key'), $model);

        echo "<p>Generating text completion with prompt: " . $prompt . "</p>";
        echo "<p>Image URL for vision analysis: " . $attached_image_url . "</p>";

        try {
            $structured_completion = $aiService->perform_text_completion($prompt, $attached_image_url);

            $decoded_completion = json_decode($structured_completion, true);
            $article_title = $decoded_completion['title'];
            $article_description = $decoded_completion['description'];

            if(isset($article_title) && isset($article_description)) {
                echo "<p>The returned completion is formed correctly as json and has been decoded. The title is: \"" . $article_title . "\"</p>";
                return new GeneratedArticle($article_title, $article_description);
            }
            else {
                echo "<p>Error decoding the completion from OpenAI api call: maybe the returned completion isn't\'t a json formed as described in the prompt?</p>";
                return null;
            }
        }
        catch (AICompletionException $e) {
            echo "<p>OpenAI api call failed: " . $e->getMessage() . "</p>";
            if(isset($e->request)) { //If the ai completion request object is provided in the exception, show it
                echo "<textarea cols='50' rows='50'>";
                print_r($e->request);
                echo "</textarea>";
            }
            if(isset($e->response)) { //If the ai completion response object is provided in the exception, show it
                echo "<textarea cols='50' rows='50'>";
                print_r($e->response);
                echo "</textarea>";
            }

            return null;
        }
    }
}

class GeneratedArticle {
    public function __construct($title, $description)
    {
        $this->title = $title;
        $this->description = $description;
    }

    public $title;
    public $description;
}