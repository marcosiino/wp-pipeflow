<?php

namespace Pipeline\Stages\AIImageGeneration;
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/AIImageGeneration/AIImageGenerationStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Utils\Helpers;

class AIImageGenerationStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $prompt = Helpers::getField($configuration, "prompt", true);
        $outputParamName = Helpers::getField($configuration, "outputParamName", true);
        $imagesCount = Helpers::getField($configuration, "imagesCount", false, 1);
        $hdQuality = Helpers::getField($configuration, "higherQuality", false, false);
        $model = Helpers::getField($configuration, "model", false, "dall-e-2");
        $imagesSize = Helpers::getField($configuration, "imagesSize", false, "512x512");
        return new AIImageGenerationStage($prompt, $outputParamName, $model, $imagesSize, $imagesCount, $hdQuality);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Generates one or more images with OpenAI and outputs the generated image urls into the output context.";
        $setupParameters = array(
            "prompt" => "The image generation prompt for the AI. You can use Context Placeholders to feed context values (i.e. results from previous stages) into the prompt.",
            "outputParamName" => "The name of the context parameter to which the generated images URLS are saved",
            "imagesCount" => "(optional, default: 1) An integer which specified the number of images to generate.",
            "higherQuality" => "(optional, default: false) A boolean which specifies if the images should be generated with higher quality. Implies higher costs.",
            "imagesSize" => "(optional, default: \"512x512\") A string specifying the size of the generated image, i.e. \"512x512\". The specified size must by supported by the selected model",
            "model" => "(optional, default: dall-e-2) The OpenAI model to use for image generation",
        );

        $contextInputs = array(
            "OPENAI_API_KEY" => "The OpenAI's API key to use to perform the image generation api request to OpenAI.",
            "" => "If the prompts specified one or more Contex Placeholders, those placeholders is taken as input from the context and replaced in place of the placeholders",
        );
        $contextOutputs = array(
            "" => "A parameter named after `outputParamName` setup parameter which will contains an array of strings with the URLs of the images generated by the AI",
        );

        return new StageDescriptor("AIImageGeneration", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}