<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/PipelineXMLConfigurator.php";

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
    public array $stages;

    /**
     * @param PipelineContext|null $initialContext
     * @param string $jsonConfiguration - The json configuration used to set up the pipeline
     * @throws StageConfigurationException
     */
    public function __construct(?PipelineContext $initialContext = null)
    {
        if(is_null($initialContext)) {
            $initialContext = new PipelineContext();
        }
        $this->contextHistory = array($initialContext);
        $this->stages = array();
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
     * @throws PipelineExecutionException
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

    public function setupWithXML(string $xmlConfiguration): void {
        $xmlConfigurator = new PipelineXMLConfigurator($this);
        $xmlConfigurator->configure($xmlConfiguration);
    }
}