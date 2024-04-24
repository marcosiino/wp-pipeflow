<?php

namespace Pipeline\Stages\SaveMedia;
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/StageDescriptor.php";
require_once PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SaveMedia/SaveMediaStage.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Utils\Helpers;

class SaveMediaStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(array $configuration): AbstractPipelineStage
    {
        $urlsParamName = Helpers::getField($configuration, "mediaURLs", true);
        $outputParamName = Helpers::getField($configuration, "resultTo", true);
        return new SaveMediaStage($urlsParamName, $outputParamName);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Downloads and save one or more media files into the Wordpress Media Library.";
        $setupParameters = array(
            "mediaURLs" => "The URLs of the media to save into the wordpress media library.",
            "resultTo" => "The name of the context parameter where the saved media ids are stored.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "" => "A parameter named after the value specified in the `resultTo` setup parameter, which will contains one or more attachment ids for the medias that has been saved into wordpress media gallery",
        );

        return new StageDescriptor("SaveMedia", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}