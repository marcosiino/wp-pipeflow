<?php

namespace Pipeline\Stages\AIImageGeneration;
require_once PLUGIN_PATH . "classes/AIServices/OpenAIService.php";
require_once PLUGIN_PATH . "classes/Pipeline/PromptProcessor.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use AIServices\AICompletionException;
use AIServices\OpenAIService;
use Pipeline\Exceptions\PipelineExecutionException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\PipelineContext;
use Pipeline\PromptProcessor;
use Pipeline\StageDescriptor;

class AIImageGenerationStage implements AbstractPipelineStage
{
    private string $prompt;
    private string $outputParamName;
    private int $imageCount;
    private bool $hdQuality;
    private string $model;
    private string $imagesSize;

    public function __construct(string $prompt, string $outputParamName, string $model, string $imagesSize, int $imageCount, bool $hdQuality)
    {
        $this->prompt = $prompt;
        $this->outputParamName = $outputParamName;
        $this->imageCount = $imageCount;
        $this->hdQuality = $hdQuality;
        $this->model = $model;
        $this->imagesSize = $imagesSize;
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
        $openAIService = new OpenAIService($apiKey,"gpt-4-turbo", $this->model, $this->imagesSize, $this->hdQuality);

        $promptProcessor = new PromptProcessor($context);
        $prompt = $promptProcessor->process($this->prompt);

        try
        {
            $image_urls = $openAIService->perform_image_completion($prompt, $this->imageCount);
        }
        catch (AICompletionException $e)
        {
            throw new PipelineExecutionException("An error occurred while performing the image completion: " . $e->getMessage());
        }

        foreach($image_urls as $url) {
            $context->setParameter($this->outputParamName, $url);
        }

        return $context;
    }
}