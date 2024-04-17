<?php
define('IMAGE_FIRST_DEFAULT', false);

define('TEXT_DEFAULT_PROMPT', "Generate a fairy tale for children about %TOPIC%");
define('IMAGE_DEFAULT_PROMPT', "Generate an modern water color illustration for the fairy tale below:\n\nTITLE:\n %GENERATED_ARTICLE_TITLE%\nDESCRIPTION:\n%GENERATED_ARTICLE_DESCRIPTION%");

define('DEFAULT_AUTO_GENERATION_INTERVAL', 43512);

define('AUTOMATIC_CATEGORIES_AND_TAGS_DEFAULT', false);

// The istructions for the text completion json return format
define('JSON_COMPLETION_FORMAT_INSTRUCTIONS', 'Istruzioni: Il formato della risposta deve rigorosamente essere un json con il campo title e il campo description che indicano rispettivamente il titolo e la descrizione dell\'articolo. Ci sono solo questi due campi e nessun\'altro');

define('IMAGE_GENERATION_ENABLE_HD_DEFAULT', false);
define('IMAGE_GENERATION_SIZE_DEFAULT', "512x512");
define('IMAGE_GENERATION_OPENAI_MODEL_DEFAULT', "dall-e-3");
define('TEXT_GENERATION_OPENAI_MODEL_DEFAULT', "gpt-4-turbo");
define('TEXT_GENERATION_OPENAI_TEMPERATURE_DEFAULT', 0.7);
define('TEXT_GENERATION_OPENAI_MAX_TOKENS_DEFAULT', 4096);

class Settings {

    public static function get_auto_generation_interval_secs() {
        return esc_attr(get_option('auto_generation_interval_secs', DEFAULT_AUTO_GENERATION_INTERVAL));
    }

    /**
     * @return string The topics to choose from for the generated content, separated by \n
     */
    public static function get_content_generation_topics() {
        return esc_attr(get_option('postbrewer_content_topics', ""));
    }

    /**
     * @return boolean Whether the article image is generated firstly, then the article text is generated based on the image, or vice-versa
     */
    public static function get_image_first_mode() {
        return esc_attr(get_option('image_first_flow', IMAGE_FIRST_DEFAULT));
    }

    /**
     * @return string The article generation prompt
     */
    public static function get_article_generation_prompt() {
        return esc_attr(get_option('article_generation_prompt', TEXT_DEFAULT_PROMPT));
    }

    /**
     * @return string The image generation prompt
     */
    public static function get_image_generation_prompt() {
        return esc_attr(get_option('image_generation_prompt', IMAGE_DEFAULT_PROMPT));
    }

    /**
     * @return string Whether the AI should choose the appropriate categories and tags to assign to the generated articles
     */
    public static function get_automatic_categories_and_tags() {
        return esc_attr(get_option("automatic_categories_and_tags", AUTOMATIC_CATEGORIES_AND_TAGS_DEFAULT));
    }

    /**
     * @return string The OpenAI api key
     */
    public static function get_openAI_api_key() {
        return esc_attr(get_option('openai_api_key', ""));
    }

    /**
     * @return string The OpenAI api key
     */
    public static function get_image_generation_openai_model() {
        return esc_attr(get_option('image_generation_openai_model', IMAGE_GENERATION_OPENAI_MODEL_DEFAULT));
    }

    /**
     * @return boolean The OpenAI api key
     */
    public static function get_image_generation_enable_hd() {
        return esc_attr(get_option('image_generation_enable_hd', IMAGE_GENERATION_ENABLE_HD_DEFAULT));
    }

    /**
     * @return string The Image Generation size
     */
    public static function get_image_generation_size() {
        return esc_attr(get_option('image_generation_size', IMAGE_GENERATION_SIZE_DEFAULT));
    }

    /**
     * @return string The Text Generation OpenAI Model
     */
    public static function get_text_generation_openai_model() {
        return esc_attr(get_option('text_generation_openai_model', TEXT_GENERATION_OPENAI_MODEL_DEFAULT));
    }

    /**
     * @return float The Text Generation Temperature
     */
    public static function get_text_generation_temperature() {
        return esc_attr(get_option('text_generation_temperature', TEXT_GENERATION_OPENAI_TEMPERATURE_DEFAULT));
    }

    /**
     * @return int The Text Generation Max Number of Tokens
     */
    public static function get_text_generation_max_tokens() {
        return esc_attr(get_option('text_generation_max_tokens', TEXT_GENERATION_OPENAI_MAX_TOKENS_DEFAULT));
    }
}