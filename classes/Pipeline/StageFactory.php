<?php

namespace Pipeline;
require_once "classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once "classes/Pipeline/Interfaces/AbstractPipelineStage.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;

/**
 * A class which allows to instantiate Pipeline Stages given their configuration
 */
class StageFactory
{
    static private array $factories = array();

    /**
     * Register a StageFactory which manages the instantiation of a specific stage
     *
     * @param AbstractPipelineStage $factory - The factory instance to register
     * @return void
     */
    static public function registerFactory(AbstractPipelineStage $factory): void {
        self::$factories[$factory->getDescriptor()->getIdentifier()] = $factory;
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
     * Instantiates a stage given its configuration
     *
     * @param array $configuration - The stage configuration
     *
     * @returns AbstractPipelineStage
     * @throws StageConfigurationException
     */
    static public function instantiateStage(array $configuration): AbstractPipelineStage {
        $configIdentifier = $configuration['identifier'];
        if(!isset($configIdentifier)) {
            throw new StageConfigurationException("identifier not found in stage configuration");
        }

        foreach(self::$factories as $factoryIdentifier => $factory) {
            if($configIdentifier === $factoryIdentifier) {
                return $factory->instantiate($configuration);
            }
        }
        throw new StageConfigurationException("There isn't any factory registered for the stage identifier: \($configIdentifier)");
    }
}
