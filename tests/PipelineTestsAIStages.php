<?php

namespace tests;
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Pipeline.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/AIImageGeneration/AIImageGenerationStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SetValue/SetValueStageFactory.php";

use PHPUnit\Framework\TestCase;
use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Pipeline;
use Pipeline\StageFactory;
use Pipeline\Stages\AIImageGeneration\AIImageGenerationStageFactory;
use Pipeline\Stages\SetValue\SetValueStageFactory;

class PipelineTestsAIStages extends TestCase
{
    private Pipeline $pipeline;

    protected function setUp(): void {
        StageFactory::clearRegisteredFactories();
        $this->pipeline = new Pipeline();
    }

    protected function tearDown(): void {
    }

    // ------------ IMAGE GENERATION -----------
    /**
     * @throws StageConfigurationException
     */
    public function testAIImageGenerationStage(): void {
        StageFactory::registerFactory(new AIImageGenerationStageFactory());
        StageFactory::registerFactory(new SetValueStageFactory());

        $imagesCount = 2;

        $openAIKey = getenv("POSTBREWER_UNIT_TESTS_OPENAI_API_KEY");
        $this->assertTrue(!is_null($openAIKey), "POSTBREWER_UNIT_TESTS_OPENAI_API_KEY environment variable is not set. Set it with a valid OpenAI Api Key using export POSTBREWER_UNIT_TESTS_OPENAI_API_KEY=\"some value\"");
        $this->assertTrue(!empty($openAIKey), "POSTBREWER_UNIT_TESTS_OPENAI_API_KEY environment variable is not set. Set it with a valid OpenAI Api Key using export POSTBREWER_UNIT_TESTS_OPENAI_API_KEY=\"some value\"");

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",
                    \"parameterName\": \"OPENAI_API_KEY\",
                    \"parameterValue\": \"$openAIKey\"
                },
                {
                    \"identifier\": \"SetValue\",
                    \"parameterName\": \"TOPIC\",
                    \"parameterValue\": \"Godzilla walking in Rome, realistic photo.\"
                },
                {
                    \"identifier\": \"AIImageGeneration\",            
                    \"prompt\": \"Generate an image of: %%TOPIC%%\",
                    \"model\": \"dall-e-2\",
                    \"imagesSize\": \"512x512\",
                    \"imagesCount\": $imagesCount,
                    \"higherQuality\": false
                }
            ]
        }";

        $this->pipeline->setup($configuration);
        $outputContext = $this->pipeline->execute();

        $this->assertEquals($outputContext->checkParameterExists("GENERATED_AI_IMAGES"), true, "GENERATED_AI_IMAGES should exists in the contest since it should be added by the stage");
        $imagesArray = $outputContext->getParameter("GENERATED_AI_IMAGES")->getAll();
        $this->assertEquals(count($imagesArray), $imagesCount, "GENERATED_AI_IMAGES should have $imagesCount images since $imagesCount images has been requested.");

        print("Generated images: " . print_r($imagesArray, true));
    }
}