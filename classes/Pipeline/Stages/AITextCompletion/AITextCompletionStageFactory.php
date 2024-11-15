<?php

namespace Pipeline\Stages\AITextCompletion;
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/AITextCompletion/AITextCompletionStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Utils\Helpers;
use Pipeline\StageConfiguration\StageConfiguration;

class AITextCompletionStageFactory implements AbstractStageFactory
{
    public static string $defaultModel = "gpt-4o-mini";
    public static float $defaultTemperature = 0.7;
    public static int $defaultMaxTokens = 4096;
    public static bool $defaultOutputJSON = false;
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new AITextCompletionStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Requests a text completion to OpenAI and outputs the generated text into the output context.";
        $setupParameters = array(
            "prompt" => "The text completion prompt for the AI. You can use Context Placeholders to feed context values (i.e. results from previous stages) into the prompt.",
            "attachedImageURLs" => "(optional) An array of image urls that will be attached to the prompt",
            "model" => "(optional, default: " . AITextCompletionStageFactory::$defaultModel . ") The OpenAI model to use for image generation",
            "temperature" => "(optional, default: " . AITextCompletionStageFactory::$defaultTemperature . ")",
            "maxTokens" => "(optional, default: " . AITextCompletionStageFactory::$defaultMaxTokens . ")",
            "outputJSON" => "(optional, default: " . AITextCompletionStageFactory::$defaultOutputJSON . ")",
            "resultTo" => "(optional) The name of the context parameter to which the generated text is saved",
        );

        $contextInputs = array(
            "OPENAI_API_KEY" => "The OpenAI's API key to use to perform the image generation api request to OpenAI.",
        );

        $contextOutputs = array(
            "GENERATED_TEXT_COMPLETION" => "The generated text completion string. If resultTo input settings parameter is set, the text completion is wrote into the context parameter specified there instead.",
        );

        return new StageDescriptor("AITextCompletion", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}