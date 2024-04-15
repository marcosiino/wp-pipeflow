<?php
require_once(PLUGIN_PATH . "classes/AIServiceMock.php");
require_once(PLUGIN_PATH . "classes/OpenAIService.php");

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
            return new OpenAIService(Settings::get_openAI_api_key(), "gpt-4-turbo", "dall-e-3", "1024x1024", true);
        }
    }
}