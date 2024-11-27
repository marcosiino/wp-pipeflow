<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SetValue/SetValueStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/RandomValue/RandomValueStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/RandomArrayItem/RandomArrayItemStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ArrayCount/ArrayCountStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ExplodeString/ExplodeStringStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/JSONDecode/JSONDecodeStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SumOperation/SumOperationStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SaveMedia/SaveMediaStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/CreatePost/CreatePostStageFactory.php";

class StagesRegistration
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {
        StageFactory::registerFactory(new SetValueStageFactory());
        StageFactory::registerFactory(new RandomValueStageFactory());
        StageFactory::registerFactory(new RandomArrayItemStageFactory());
        StageFactory::registerFactory(new ArrayCountStageFactory());
        StageFactory::registerFactory(new ExplodeStringStageFactory());
        StageFactory::registerFactory(new JSONDecodeStageFactory());
        StageFactory::registerFactory(new SumOperationStageFactory());
        StageFactory::registerFactory(new SaveMediaStageFactory());
        StageFactory::registerFactory(new CreatePostStageFactory());
    }
}