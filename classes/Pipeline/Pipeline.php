<?php

namespace Pipeline;
require_once "classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once "classes/Interfaces/AbstractPipelineStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;

/**
 * Represents a Content Generation Pipeline
 */
class Pipeline
{
    /**
     * An array containing the history of manipulation of the context, from the first one, to the output context of each executed stage
     * @var array|PipelineContext[]
     */
    private array $contextHistory;

    /**
     * An array containing the stages of the pipelines
     * @var array|AbstractPipelineStage[]
     */
    private array $stages;

    /**
     * @param PipelineContext|null $initialContext
     * @param string $jsonConfiguration - The json configuration used to set up the pipeline
     * @throws StageConfigurationException
     */
    public function __construct(?PipelineContext $initialContext, string $jsonConfiguration)
    {
        if(is_null($initialContext)) {
            $initialContext = new PipelineContext();
        }
        $this->contextHistory = array($initialContext);
        $this->stages = array();
        $this->setup($jsonConfiguration);
    }

    /**
     * Adds a stage to the pipeline
     *
     * @param AbstractPipelineStage $stage
     * @return void
     */
    public function addStage(AbstractPipelineStage $stage): void {
        $this->stages[] = $stage;
    }

    /**
     * Executes the pipeline and returns the resulting output context
     *
     * @return PipelineContext
     */
    public function execute(): PipelineContext
    {
        foreach($this->stages as $stage) {
            $outputContext = $stage->execute($this->getCurrentContext());
            $this->contextHistory[] = $outputContext;
        }
        return $this->getCurrentContext();
    }

    /**
     * Returns the current pipeline context
     *
     * @return PipelineContext
     */
    public function getCurrentContext(): PipelineContext {
        return end($this->contextHistory);
    }

    /**
     * Clears the pipeline context
     *
     * @return void
     */
    public function clearContext(): void {
        $this->contextHistory = array(new PipelineContext());
    }

    /**
     * Builds the pipeline's stages using the given json configuration
     *
     * @param string $jsonConfiguration
     * @return void
     * @throws StageConfigurationException
     */
    private function setup(string $jsonConfiguration): void {
        $configuration = json_decode($jsonConfiguration, true);
        if(!isset($configuration)) {
            throw new StageConfigurationException("Invalid json configuration provided: error decoding json.");
        }

        $stages = $this->getField($configuration, "stages", true);
        if(!is_array($stages)) {
            throw new StageConfigurationException("Invalid json configuration provided: expected an array as the root object.");
        }

        foreach($stages as $stageConfiguration) {
            $stage = StageFactory::instantiateStage($stageConfiguration);
            $this->addStage($stage);
        }
    }

    /**
     * Gets the specified field value from the provided associative array and checks that it is present in the array (if $required is true)
     *
     * @param array $array An associative array
     * @param string $fieldName - The field to get
     * @param bool $required - Default: false. Whether the field is required. If true, an exception is thrown if the field is not present
     * @return mixed
     * @throws StageConfigurationException if field with $fieldName is not found in $array and $required is true
     */
    private function getField(array $array, string $fieldName, bool $required = false): mixed {
        $value = $array[$fieldName];
        if($required && !isset($value)) {
            throw new StageConfigurationException("Invalid json configuration provided: expected field \"$fieldName\" not found");
        }
        return $value;
    }
}