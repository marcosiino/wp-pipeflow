<?php declare(strict_types=1);

namespace tests;
require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/StageConfigurationExceptionCases.php";
require_once PLUGIN_PATH . "classes/Pipeline/Pipeline.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SetValue/SetValueStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SumOperation/SumOperationStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/AIImageGeneration/AIImageGenerationStageFactory.php";

use PHPUnit\Framework\TestCase;
use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Exceptions\StageConfigurationExceptionCases;
use Pipeline\Pipeline;
use Pipeline\StageFactory;
use Pipeline\Stages\AIImageGeneration\AIImageGenerationStageFactory;
use Pipeline\Stages\SetValue\SetValueStageFactory;
use Pipeline\Stages\SumOperation\SumOperationStageFactory;

final class PipelineTests extends TestCase
{
    private Pipeline $pipeline;

    protected function setUp(): void {
        StageFactory::clearRegisteredFactories();
        $this->pipeline = new Pipeline();
    }

    protected function tearDown(): void {
    }

    // -------------- CONFIGURATIONS ---------------

    public function testPipelineValidConfiguration(): void
    {
        StageFactory::registerFactory(new SetValueStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",
                    \"parameterName\": \"PARAM_A\",
                    \"parameterValue\": 2
                }
            ]
        }";
        $this->pipeline->setup($configuration);
        $this->assertEquals(count($this->pipeline->stages), 1, "Wrong number of stages in pipeline. Should be 1");
    }

    /**
     * @throws StageConfigurationException
     */
    public function testPipelineValidConfigurationNotRegisteredStageIdentifier(): void
    {
        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",
                    \"parameterName\": \"PARAM_A\",
                    \"parameterValue\": 2
                }
            ]
        }";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::InvalidStageTypeIdentifier->value);

        $this->pipeline->setup($configuration);
    }

    public function testPipelineStageIdNotSpecified(): void
    {
        StageFactory::registerFactory(new SetValueStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"parameterName\": \"PARAM_A\",
                    \"parameterValue\": 2
                }
            ]
        }";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::StageIdentifierNotSpecified->value);

        $this->pipeline->setup($configuration);
    }

    public function testPipelineMissingRequiredStageParameter(): void
    {
        StageFactory::registerFactory(new SetValueStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",
                    \"parameterName\": \"PARAM_A\"
                }
            ]
        }";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::ExpectedFieldNotFound->value);

        $this->pipeline->setup($configuration);
    }

    public function testPipelineUnableToDecodeJSONException(): void
    {
        $configuration = "
            \"stages\": [
        ";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::UnableToDecodeJSONConfiguration->value);

        $this->pipeline->setup($configuration);
    }

    // -------------- STAGES ---------------

    public function testPipelineOneStage(): void
    {
        StageFactory::registerFactory(new SetValueStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"NUMBER_A\",
                    \"parameterValue\": 10
                }
            ]
        }";
        $this->pipeline->setup($configuration);
        $outputContext = $this->pipeline->execute();

        $this->assertEquals($outputContext->checkParameterExists("NUMBER_A"), true, "NUMBER_A should exists in the contest since it should be added by the SetValue stage");
        $this->assertEquals($outputContext->getParameter("NUMBER_A")->getLast(), 10, "NUMBER_A should be equal to 10");
    }

    public function testPipelineMultipleParameters(): void
    {
        StageFactory::registerFactory(new SetValueStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"PARAM_A\",
                    \"parameterValue\": 10
                },
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"PARAM_A\",
                    \"parameterValue\": \"Hello world\"
                },
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"PARAM_B\",
                    \"parameterValue\": 1.12
                },
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"PARAM_C\",
                    \"parameterValue\": null
                }
            ]
        }";
        $this->pipeline->setup($configuration);
        $outputContext = $this->pipeline->execute();

        $this->assertEquals($outputContext->checkParameterExists("PARAM_A"), true, "PARAM_A should exists in the contest since it should be added by the SetValue stage");
        $this->assertEquals($outputContext->checkParameterExists("PARAM_B"), true, "PARAM_B should exists in the contest since it should be added by the SetValue stage");
        $this->assertEquals($outputContext->checkParameterExists("PARAM_C"), true, "PARAM_C should exists in the contest since it should be added by the SetValue stage");

        $this->assertEquals($outputContext->getParameter("PARAM_A")->getAll()[0], 10, "PARAM_A[0] should be equal to 10");
        $this->assertEquals($outputContext->getParameter("PARAM_A")->getAll()[1], "Hello world", "PARAM_A[1] should be equal to \"Hello World\"");

        $this->assertEquals($outputContext->getParameter("PARAM_B")->getLast(), 1.12, "PARAM_B[0] should be equal to 1.12");

        $this->assertEquals($outputContext->getParameter("PARAM_C")->getLast(), null, "PARAM_C[0] should be equal to null");
    }

    public function testPipelineSetValuesAndSumThem(): void
    {
        StageFactory::registerFactory(new SetValueStageFactory());
        StageFactory::registerFactory(new SumOperationStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"PARAM_A\",
                    \"parameterValue\": 10
                },
                {
                    \"identifier\": \"SetValue\",            
                    \"parameterName\": \"PARAM_B\",
                    \"parameterValue\": 12.5
                },
                {
                    \"identifier\": \"SumOperation\",            
                    \"parameterA\": \"PARAM_A\",
                    \"parameterB\": \"PARAM_B\",
                    \"resultParameter\": \"RESULT\"
                }
            ]
        }";
        $this->pipeline->setup($configuration);
        $outputContext = $this->pipeline->execute();

        $this->assertEquals($outputContext->checkParameterExists("RESULT"), true, "RESULT should exists in the contest since it should be added by the SumOperation stage");
        $this->assertEquals($outputContext->getParameter("RESULT")->getLast(), 22.5, "PARAM_A should be equal to 22.5");
    }
}