<?php

namespace Pipeline\Utils;
require_once PLUGIN_PATH . "classes/Pipeline/Pipeline.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageSetting.php";

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use Pipeline\Exceptions\StageConfigurationException;
use Pipeline\Pipeline;
use Pipeline\StageConfiguration\StageConfiguration;
use Pipeline\StageConfiguration\StageSetting;
use Pipeline\StageFactory;

class PipelineXMLConfigurator
{
    private Pipeline $pipeline;
    public function __construct(Pipeline $pipeline) {
        $this->pipeline = $pipeline;
    }

    public function configure($xmlConfiguration): bool {
        $document = new DOMDocument();
        $document->loadXML($xmlConfiguration);

        //Validates the xml configuration
        $errors = array();
        if($this->validateXMLConfiguration($document, $errors) === true) {
            $allStages = $document->documentElement->getElementsByTagName("stage");
            foreach($allStages as $stage) {
                $this->processStage($stage, $document);
            }
        } else {
            //TODO: don't print the errors, propagate them someway (exception?)
            print("XML Configuration Validation errors:");
            print_r($errors);
            return false;
        }
        return true;
    }

    /**
     * @throws StageConfigurationException
     */
    private function processStage(DOMElement $stage, DOMDocument $document): void
    {
        $stageConfiguration = new StageConfiguration();

        $stageType = $stage->getAttribute("type");
        $params = $stage->getElementsByTagName("param");
        foreach ($params as $param) {
            $paramName = $param->getAttribute("name");
            $subItems = $param->getElementsByTagName("item");
            //If the param element has <item> sub elements, it means it is an array parameter
            if(count($subItems) > 0) {
                $paramArray = array();
                foreach ($subItems as $item) {
                    $paramArray[] = $item->nodeValue;
                }
                $stageConfiguration->addSetting(new StageSetting($paramName, $paramArray));
            }
            // Otherwise it is a single value parameter
            else {
                $stageConfiguration->addSetting(new StageSetting($paramName, $param->nodeValue));
            }

            //TODO: manage referenced context parameters settings
        }

        $stage = StageFactory::instantiateStageOfType($stageType, $stageConfiguration);
        $this->pipeline->addStage($stage);
    }

    /**
     * Validates the loaded document xml content against the custom XML Schema Definition
     *
     * @param DOMDocument $document - The document, with the pipeline xml configuration loaded previously
     * @param array $validationErrors output if validation errors occurs
     * @return bool - Returns true if the document validates successfully, false otherwise
     */
    private function validateXMLConfiguration(DOMDocument $document, array &$validationErrors): bool {
        libxml_use_internal_errors(true);
        $result = $document->schemaValidate(PLUGIN_PATH . 'classes/Pipeline/pipeline_schema_definition.xsd');
        if($result === false) {
            $validationErrors = array();
            $errors = libxml_get_errors();
            foreach($errors as $error) {
                $validationErrors[] = $error->message;
            }
        }
        libxml_use_internal_errors(false);

        return $result;
    }
}