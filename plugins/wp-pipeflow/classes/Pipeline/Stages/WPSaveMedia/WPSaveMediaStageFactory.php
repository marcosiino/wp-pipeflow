<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractPipelineStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageDescriptor.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Utils/Helpers.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSaveMedia/WPSaveMediaStage.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageConfiguration/StageConfiguration.php";

class WPSaveMediaStageFactory implements AbstractStageFactory
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
            "convertToFormat" => "(optional, default: jpeg) The format of the saved image. Possible values: `png` or `jpeg`",
            "compression" => "(optional, default: 65) The image compression. Only valid when convertToFormat is `jpeg`. Possible values: 0-100",
            "resultTo" => "(optional) The name of the context parameter where the saved media ids are stored.",
        );

        $contextInputs = array();
        $contextOutputs = array(
            "SAVED_IMAGES_IDS" => "An array of one or more attachment ids for the medias that has been saved into wordpress media gallery. If resultTo input setting parameter is set, the saved media ids is output on the context parameter specified there instead.",
        );

        return new StageDescriptor("WPSaveMedia", $description, $setupParameters, $contextInputs, $contextOutputs);
    }
}