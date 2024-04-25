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
use Pipeline\StageDescriptor;

class AIImageGenerationStage extends AbstractPipelineStage
{
    private string $prompt;
    private string $outputParamName;
    private string $imageCount;
    private string $hdQuality;
    private string $model;
    private string $imagesSize;

    public function __construct(string $prompt, mixed $outputParamName, mixed $model, mixed $imagesSize, mixed $imageCount, mixed $hdQuality)
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

        $model = $this->getInputValue($this->model, $context);
        $imagesSize = $this->getInputValue($this->imagesSize, $context);
        $hdQuality = (bool)$this->getInputValue($this->hdQuality, $context);
        $imageCount = (int)$this->getInputValue($this->imageCount, $context);
        $outputParamName = $this->getInputValue($this->outputParamName, $context);

        print("this->hdQuality: $this->hdQuality\n");
        print("this->imageSize: $this->imagesSize\n");
        print("this->model: $this->model\n");

        print("hdQuality: $hdQuality\n");
        print("imageSize: $imagesSize\n");
        print("model: $model\n");
        $openAIService = new OpenAIService($apiKey,"gpt-4-turbo", $model, $imagesSize, $hdQuality);

        $promptProcessor = new PlaceholderProcessor($context);
        $prompt = $promptProcessor->process($this->prompt);
        try
        {
            $image_urls = $openAIService->perform_image_completion($prompt, $imageCount);
        }
        catch (AICompletionException $e)
        {
            throw new PipelineExecutionException("An error occurred while performing the image completion: " . $e->getMessage());
        }

        foreach($image_urls as $url) {
            $context->setParameter($outputParamName, $url);
        }

        return $context;
    }
}