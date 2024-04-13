<?php
require_once(PLUGIN_PATH . "utils/defaults.php");
require_once(PLUGIN_PATH . "classes/OpenAIService.php");

class ArticleGenerator {

    private $aiService;

    public function __construct()
    {
        $this->aiService = new OpenAIService(get_option('openai_api_key'), "gpt-4-turbo", "dall-e-3", "1024x1024", true);
    }

    static function get_random_topic() {
        $topics_str = get_option('coloring_page_topics');
        if(isset($topics_str) AND empty($topics_str) == false) {
            $topics_array = explode("\n", $topics_str);
            $random_index = random_int(0,count($topics_array)-1);
            return $topics_array[$random_index];
        }
        else {
            return "";
        }
    }
    public function generate_new_article($topic = null) {
        if(!isset($topic)) {
            $topic = ArticleGenerator::get_random_topic();
        }

        $input_params = array(
            array(
                "key" => "TOPIC",
                "value" => $topic,
            )
        );

        $result = $this->generate_with_mode(get_option('image_first_flow', IMAGE_FIRST_DEFAULT), $input_params);
        $generatedImageData = $result['generatedImage'];
        $generatedArticle = $result['generatedArticle'];

        if (isset($generatedImageData) && isset($generatedArticle)) {
            echo "<p style='color: blue;'>Inserting the new post...</p>";
            $category_ids = array(); //TODO
            $tag_ids = array();
            insert_post($generatedArticle->title, $generatedArticle->description, $category_ids, $tag_ids, $generatedImageData->savedImageId, 'publish');
            echo "<p><strong>Post generated successful!</strong></p>";
        }
        else {
            echo "<p style='color: red;'><strong>Article generation failed.</strong></p>";
        }
    }

    private function generate_with_mode($image_first_mode, $input_params) {
        if($image_first_mode) {
            echo "<p style='color: blue;'>Generating the image (image first mode = true)...</p>";
            $generatedImageData = $this->generate_and_save_image($input_params);
            echo "<p style='color: blue;'>Generating the text description for the image...</p>";
            $generatedArticle = $this->generate_article(array(), $generatedImageData->generatedImageExternalURL);

            return array(
                "generatedArticle" => $generatedArticle,
                "generatedImage" => $generatedImageData,
            );
        }
        else {
            echo "<p style='color: blue;'>Generating the text description (image first mode = false)...</p>";
            $generatedArticle = $this->generate_article($input_params, null);

            echo "<p style='color: blue;'>Generating the image for the description</p>";

            $image_generation_inputs = array(
                array(
                    "key" => "GENERATED_ARTICLE_DESCRIPTION",
                    "value" => $generatedArticle->description,
                ),
                array(
                    "key" => "GENERATED_ARTICLE_TITLE",
                    "value" => $generatedArticle->title,
                ),
            );

            $generatedImageData = $this->generate_and_save_image($image_generation_inputs);

            return array(
                "generatedArticle" => $generatedArticle,
                "generatedImage" => $generatedImageData,
            );
        }
    }

    /**
     * Returns the prompt with the occurrencies of the input parameters replaced with their values into the prompt text
     * $input_params is an array of dictionaries with key (the parameter name, i.e. "TOPIC"), and value (the parameter value, i.e. "A topic"). In this case all the occurrencies of %TOPIC% in the prompt are replaced with the parameter value: "A topic")
     */
    private function prompt_with_inputs($prompt, $input_params) {

        //Replaces the parameters in the prompt with the input parameters values provided
        if(isset($input_params)) {
            foreach($input_params as $param) {
                $prompt = str_replace("%" . strtoupper($param['key']) . "%", $param['value'], $prompt);
            }
        }

        return $prompt;
    }


    /**
     * Generate an article with AI and returns it
     *
     * @param $topic: the coloring page image topic
     * @return GeneratedArticle the generated article data or null on error
     */

    private function generate_article($input_params = array(), $attached_image_url) {

        $prompt = get_option('article_generation_prompt', TEXT_DEFAULT_PROMPT); // Gets the prompt template from the plugin settings
        $prompt .= JSON_COMPLETION_FORMAT_INSTRUCTIONS; // Adds the structured json completion format instructions
        $prompt = $this->prompt_with_inputs($prompt, $input_params); // replaces the input_parameters in the prompt template

        echo "<p>Generating text completion with prompt: " . $prompt . "</p>";
        if(isset($attached_image_url)) {
            echo "<p>Provided Image URL attachment: " . $attached_image_url . "</p>";
        }

        try {
            $structured_completion = $this->aiService->perform_text_completion($prompt, $attached_image_url);

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
            $this->print_ai_exception_details($e);
            return null;
        }
    }

    private function generate_and_save_image($input_params = array()) {
        $prompt = get_option('image_generation_prompt'); // Get the image generation prompt set by the user
        $prompt = $this->prompt_with_inputs($prompt, $input_params);

        echo "<p>Generating image with prompt: " . $prompt . "</p>";

        try {
            $generated_image_url = $this->aiService->perform_image_completion($prompt);
            $imageDownloader = new ImageDownloader($generated_image_url);
            $saved_image_id = $imageDownloader->download_and_save();

            if(!isset($saved_image_id)) {
                return null;
            }

            return new GeneratedImage($saved_image_id, $generated_image_url);
        }
        catch (AICompletionException $e) {
            $this->print_ai_exception_details($e);
            return null;
        }
    }

    private function print_ai_exception_details(AICompletionException $e) {
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

class GeneratedImage {
    public function __construct($savedImageId, string $generatedImageExternalURL)
    {
        $this->savedImageId = $savedImageId;
        $this->generatedImageExternalURL = $generatedImageExternalURL;
    }

    public $savedImageId;
    public $generatedImageExternalURL;
}

class ImageDownloader {

    public function __construct($imageURL)
    {
        $this->externalImageURL = $imageURL;
    }

    public function download_and_save() {
        $saved_image_id = $this->download_image($this->externalImageURL);
        if(isset($saved_image_id)) {
            return $saved_image_id;
        }
        else {
            return null;
        }
    }

    public $externalImageURL;

    /**
     * Download and save an image to the media library
     *
     * @param $image_url
     * @return mixed
     */
    private function download_image($image_url) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Scarica l'immagine
        $temp_file = download_url($image_url);

        // Estrai l'estensione dell'URL dell'immagine
        $image_ext = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_EXTENSION);

        // Se l'estensione è stata trovata, rinomina il file temporaneo
        if ($image_ext) {
            $new_file_path = $temp_file . '.' . $image_ext;
            rename($temp_file, $new_file_path);
            $temp_file = $new_file_path;
        }
        else {
            return null;
        }

        if (is_wp_error($temp_file)) {
            return null;
        }

        // Imposta il nome del file e l'array per `media_handle_sideload()`
        $file = array(
            'name' => basename($temp_file), // Usa il nome originale dell'immagine
            'tmp_name' => $temp_file, // Percorso al file temporaneo
        );

        // Upload the image in wp media
        $id = media_handle_sideload($file);

        if (is_wp_error($id)) {
            @unlink($file['tmp_name']); // Cancella il file temporaneo in caso di errore
            return null;
        }

        // returns the id of the image
        return $id;
    }
}