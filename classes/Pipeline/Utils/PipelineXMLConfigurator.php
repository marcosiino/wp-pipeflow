<?php

namespace Pipeline\Utils;
require_once PLUGIN_PATH . "classes/Pipeline/Pipeline.php";

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use Pipeline\Pipeline;
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
        $errors = array();
        if($this->validateXMLConfiguration($document, $errors) === true) {
            $allStages = $document->documentElement->getElementsByTagName("stage");
            foreach($allStages as $stage) {
                $this->processStage($stage, $document);
            }
        } else {
            //TODO: don't print errors, propagate them someway (exception?)
            print("XML Configuration Validation errors:");
            print_r($errors);
            return false;
        }
        return true;
    }

    private function processStage(DOMElement $stage, DOMDocument $document) {
        $stageType = $stage->getAttribute("type");
        $configuration = array();
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
                $configuration[$paramName] = $paramArray;
            }
            // Otherwise it is a single value parameter
            else {
                $configuration[$paramName] = $param->nodeValue;
            }
        }

        $stage = StageFactory::instantiateStageOfType($stageType, $configuration);
        $this->pipeline->addStage($stage);
    }

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