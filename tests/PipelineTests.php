<?php declare(strict_types=1);

namespace tests;
require_once "classes/Pipeline/Pipeline.php";
require_once "classes/Pipeline/StageFactory.php";
require_once "classes/Pipeline/Stages/TestStage/TestStageFactory.php";
require_once "classes/Pipeline/Exceptions/StageConfigurationExceptionCases.php";

use PHPUnit\Framework\TestCase;
use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Exceptions\StageConfigurationExceptionCases;
use Pipeline\Pipeline;
use Pipeline\StageFactory;
use Pipeline\Stages\TestStage\TestStageFactory;

final class PipelineTests extends TestCase
{
    private Pipeline $pipeline;

    protected function setUp(): void {
        StageFactory::clearRegisteredFactories();
        $this->pipeline = new Pipeline();
    }

    protected function tearDown(): void {
    }
    public function testPipelineValidConfiguration(): void
    {
        StageFactory::registerFactory(new TestStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"TestStage\",            
                    \"prompt\": \"Example prompt. Param1 = %%PARAM1%%, Param2[1] = %%PARAM2[1]%%;\"
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
        //StageFactory::registerFactory(new TestStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"TestStage\",            
                    \"prompt\": \"Example prompt. Param1 = %%PARAM1%%, Param2[1] = %%PARAM2[1]%%;\"
                }
            ]
        }";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::InvalidStageIdentifier->value);

        $this->pipeline->setup($configuration);
    }

    public function testPipelineStageIdNotSpecified(): void
    {
        //StageFactory::registerFactory(new TestStageFactory());

        $configuration = "{
            \"stages\": [
                {            
                    \"prompt\": \"Example prompt. Param1 = %%PARAM1%%, Param2[1] = %%PARAM2[1]%%;\"
                }
            ]
        }";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::StageIdentifierNotSpecified->value);

        $this->pipeline->setup($configuration);
    }

    public function testPipelineMissingRequiredStageParameter(): void
    {
        StageFactory::registerFactory(new TestStageFactory());

        $configuration = "{
            \"stages\": [
                {
                    \"identifier\": \"TestStage\"
                }
            ]
        }";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::ExpectedFieldNotFound->value);

        $this->pipeline->setup($configuration);
    }

    public function testPipelineUnableToDecodeJSONException(): void
    {
        //StageFactory::registerFactory(new TestStageFactory());

        $configuration = "
            \"stages\": [
        ";

        $this->expectException(StageConfigurationException::class);
        $this->expectExceptionCode(StageConfigurationExceptionCases::UnableToDecodeJSONConfiguration->value);

        $this->pipeline->setup($configuration);
    }
}