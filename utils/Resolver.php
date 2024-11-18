<?php

require_once(WP_PIPEFLOW_PLUGIN_PATH . "classes/AIServices/AIServiceMock.php");
require_once(WP_PIPEFLOW_PLUGIN_PATH . "classes/AIServices/OpenAIService.php");

use AIServices\AIServiceMock;
use AIServices\OpenAIService;

class Resolver
{
    static private $mockAI = false;

    /**
     * @return AIImageCompletionServiceInterface a concrete AI Service class to be used in the plugin to query the AI
     **/
    static function getAIService() {
        if(Resolver::$mockAI) {
            return new AIServiceMock();
        }
        else {
            return new OpenAIService(Settings::get_openAI_api_key(), Settings::get_text_generation_openai_model(), Settings::get_image_generation_openai_model(), Settings::get_image_generation_size(), Settings::get_image_generation_enable_hd());
        }
    }
}