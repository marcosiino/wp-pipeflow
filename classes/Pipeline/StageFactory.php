<?php

namespace Pipeline;
require_once PLUGIN_PATH . "classes/Pipeline/Exceptions/StageConfigurationException.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";

use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;

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
     * Instantiates a stage given its configuration
     *
     * @param array $configuration - The stage configuration
     *
     * @returns AbstractPipelineStage
     * @throws StageConfigurationException
     */
    static public function instantiateStage(array $configuration): AbstractPipelineStage {
        if(!array_key_exists('identifier', $configuration)) {
            throw StageConfigurationException::stageIdentifierNotSpecified();
        }
        $configIdentifier = $configuration['identifier'];
        foreach(self::$factories as $factoryIdentifier => $factory) {
            if($configIdentifier === $factoryIdentifier) {
                return $factory->instantiate($configuration);
            }
        }

        throw StageConfigurationException::invalidStageIdentifier($configIdentifier);
    }

    /**
     * Instantiates a stage given a stage type and a stage configuration
     *
     * @param string $stageType - The stage type
     * @param array $configuration - The stage configuration
     *
     * @returns AbstractPipelineStage
     * @throws StageConfigurationException
     */
    static public function instantiateStageOfType(string $stageType, array $configuration): AbstractPipelineStage {
        foreach(self::$factories as $factoryIdentifier => $factory) {
            if($stageType === $factoryIdentifier) {
                return $factory->instantiate($configuration);
            }
        }

        throw StageConfigurationException::invalidStageIdentifier($stageType);
    }
}
