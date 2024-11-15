<?php

namespace Pipeline;
require_once PLUGIN_PATH . "classes/Pipeline/Stages/AIImageGeneration/AIImageGenerationStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/AITextCompletion/AITextCompletionStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SetValue/SetValueStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SumOperation/SumOperationStageFactory.php";
require_once PLUGIN_PATH . "classes/Pipeline/Stages/SaveMedia/SaveMediaStageFactory.php";

use Pipeline\Stages\AIImageGeneration\AIImageGenerationStageFactory;
use Pipeline\Stages\AITextCompletion\AITextCompletionStageFactory;
use Pipeline\Stages\SaveMedia\SaveMediaStageFactory;
use Pipeline\Stages\SetValue\SetValueStageFactory;
use Pipeline\Stages\SumOperation\SumOperationStageFactory;

class StagesRegistration
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {
        StageFactory::registerFactory(new SetValueStageFactory());
        StageFactory::registerFactory(new SumOperationStageFactory());
        StageFactory::registerFactory(new AIImageGenerationStageFactory());
        StageFactory::registerFactory(new AITextCompletionStageFactory());
        StageFactory::registerFactory(new SaveMediaStageFactory());
    }
}