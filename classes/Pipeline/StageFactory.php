<?php

namespace Pipeline;
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageConfiguration\StageConfiguration;

/**
 * A class which allows to instantiate Pipeline Stages given their configuration
 */
class StageFactory
{
    static private array $factories = array();

    /**
     * Register a StageFactory which manages the instantiation of a specific stage
     *
     * @param AbstractStageFactory $factory - The factory instance to register
     * @return void
     */
    static public function registerFactory(AbstractStageFactory $factory): void {
        self::$factories[$factory->getStageDescriptor()->getIdentifier()] = $factory;
    }

    /**
     * Returns the registered factories as a plain array
     *
     * @return array
     */
    static public function getRegisteredFactories(): array {
        $factories_array = array();
        foreach (self::$factories as $identifier => $factory) {
            $factories_array[] = $factory;
        }
        return $factories_array;
    }

    /**
     * Removes all the registered factories.
     *
     * @return void
     */
    static public function clearRegisteredFactories(): void {
        self::$factories = array();
    }

    /**
     * Instantiates a stage given a stage type identifier and a stage configuration
     *
     * @param string $stageTypeIdentifier - The stage type identifier which identifies the type of stage to instantiate, must be previously registered in StagesRegistration
     * @param StageConfiguration $configuration - The stage configuration
     *
     * @returns AbstractPipelineStage
     * @throws StageConfigurationException
     */
    static public function instantiateStageOfType(string $stageTypeIdentifier, StageConfiguration $configuration): AbstractPipelineStage {
        foreach(self::$factories as $factoryIdentifier => $factory) {
            if($stageTypeIdentifier === $factoryIdentifier) {
                return $factory->instantiate($configuration);
            }
        }

        throw StageConfigurationException::invalidStageTypeIdentifier($stageTypeIdentifier);
    }
}
