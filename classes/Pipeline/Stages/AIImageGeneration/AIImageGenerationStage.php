<?php

namespace Pipeline\Stages\AIImageGeneration;
require_once PLUGIN_PATH . "classes/AIServices/OpenAIService.php";
require_once PLUGIN_PATH . "classes/Pipeline/PlaceholderProcessor.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use AIServices\AICompletionException;
use AIServices\OpenAIService;
use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\PlaceholderProcessor;
use Pipeline\StageConfiguration\StageConfiguration;
use Pipeline\StageDescriptor;

class AIImageGenerationStage extends AbstractPipelineStage
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
        // Takes the OpenAI api key from the context
        $apiKeyContextParam = $context->getParameter("OPENAI_API_KEY");
        if(is_null($apiKeyContextParam)) {
            throw new PipelineExecutionException("OpenAI API Key not set. Set the api key in the OPENAI_API_KEY context parameter of the pipeline");
        }
        $apiKey = $apiKeyContextParam->getLast();

        $prompt = (string)$this->stageConfiguration->getSettingValue("prompt", $context, true);
        $model = (string)$this->stageConfiguration->getSettingValue("model", $context, false, "dall-e-2");
        $imagesSize = (string)$this->stageConfiguration->getSettingValue("size", $context, false, "512x512");
        $hdQuality = (bool)$this->stageConfiguration->getSettingValue("highFidelity", $context, false, false);
        $imageCount = (int)$this->stageConfiguration->getSettingValue("count", $context, false, 1);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, false, "GENERATED_IMAGES_URL");

        $openAIService = new OpenAIService($apiKey,"gpt-4-turbo", $model, $imagesSize, $hdQuality);

        $promptProcessor = new PlaceholderProcessor($context);
        $prompt = $promptProcessor->process($prompt);
        try
        {
            $image_urls = $openAIService->perform_image_completion($prompt, $imageCount);
        }
        catch (AICompletionException $e)
        {
            throw new PipelineExecutionException("An error occurred while performing the image completion: " . $e->getMessage());
        }

        $generatedImageURLs = array();
        foreach($image_urls as $url) {
            $generatedImageURLs[] = $url;
        }
        $context->setParameter($resultTo, $generatedImageURLs);
        return $context;
    }
}