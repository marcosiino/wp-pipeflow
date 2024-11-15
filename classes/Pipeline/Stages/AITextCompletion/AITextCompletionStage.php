<?php

namespace Pipeline\Stages\AITextCompletion;
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

class AITextCompletionStage extends AbstractPipelineStage
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
        $apiKey = $context->getParameter("OPENAI_API_KEY");
        if(is_null($apiKey)) {
            throw new PipelineExecutionException("OpenAI API Key not set. Set the api key in the OPENAI_API_KEY context parameter of the pipeline");
        }

        $prompt = (string)$this->stageConfiguration->getSettingValue("prompt", $context, true);
        $attachedImageURLs = (array)$this->stageConfiguration->getSettingValue("attachedImageURLs", $context, false);
        $model = (string)$this->stageConfiguration->getSettingValue("model", $context, false, AITextCompletionStageFactory::$defaultModel);
        $temperature = (string)$this->stageConfiguration->getSettingValue("temperature", $context, false, AITextCompletionStageFactory::$defaultTemperature);
        $maxTokens = (string)$this->stageConfiguration->getSettingValue("maxTokens", $context, false, AITextCompletionStageFactory::$defaultMaxTokens);
        $outputJSON = (string)$this->stageConfiguration->getSettingValue("outputJSON", $context, false, AITextCompletionStageFactory::$defaultOutputJSON);
        $resultTo = $this->stageConfiguration->getSettingValue("resultTo", $context, false, "GENERATED_TEXT_COMPLETION");

        $openAIService = new OpenAIService($apiKey, $model);

        $promptProcessor = new PlaceholderProcessor($context);
        $prompt = $promptProcessor->process($prompt);
        try
        {
            $generatedOutput = $openAIService->perform_text_completion($prompt, $outputJSON, $attachedImageURLs, $temperature, $maxTokens);
        }
        catch (AICompletionException $e)
        {
            throw new PipelineExecutionException("An error occurred while performing the image completion: " . $e->getMessage());
        }

        $context->setParameter($resultTo, $generatedOutput);
        return $context;
    }
}