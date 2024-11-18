<?php

namespace Pipeline\Stages\SaveMedia;
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/StageDescriptor.php";
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Utils/Helpers.php";
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Stages/SaveMedia/SaveMediaStage.php";
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/StageConfiguration/StageConfiguration.php";

use Pipeline\Interfaces\AbstractPipelineStage;
use Pipeline\Interfaces\AbstractStageFactory;
use Pipeline\StageDescriptor;
use Pipeline\Utils\Helpers;
use Pipeline\StageConfiguration\StageConfiguration;

class SaveMediaStageFactory implements AbstractStageFactory
{
    /**
     * @inheritDoc
     */
    public function instantiate(StageConfiguration $configuration): AbstractPipelineStage
    {
        //TODO: validate $configuration
        return new SaveMediaStage($configuration);
    }

    /**
     * @inheritDoc
     */
    public function getStageDescriptor(): StageDescriptor
    {
        $description = "Downloads and save one or more media files into the Wordpress Media Library.";
        $setupParameters = array(
            "mediaURLs" => "The URLs of the media to save into the wordpress media library.",
            "resultTo" => "(optional) The name of the context parameter where the saved media ids are stored.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "SAVED_IMAGES_IDS" => "An array of one or more attachment ids for the medias that has been saved into wordpress media gallery. If resultTo input setting parameter is set, the saved media ids is output on the context parameter specified there instead.",
        );

        return new StageDescriptor("SaveMedia", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}