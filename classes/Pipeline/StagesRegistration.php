<?php

namespace Pipeline;
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SetValue/SetValueStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SumOperation/SumOperationStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SaveMedia/SaveMediaStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/CreatePost/CreatePostStageFactory.php";

use Pipeline\Stages\CreatePost\CreatePostStageFactory;
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
        StageFactory::registerFactory(new SaveMediaStageFactory());
        StageFactory::registerFactory(new CreatePostStageFactory());
    }
}