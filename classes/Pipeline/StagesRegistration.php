<?php

require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SetValue/SetValueStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/RandomValue/RandomValueStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/RandomArrayItem/RandomArrayItemStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ArrayCount/ArrayCountStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ExplodeString/ExplodeStringStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/JSONDecode/JSONDecodeStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/JSONEncode/JSONEncodeStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/SumOperation/SumOperationStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSaveMedia/WPSaveMediaStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPCreatePost/WPCreatePostStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetCategories/WPGetCategoriesFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetTags/WPGetTagsFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSetPostTags/WPSetPostTagsFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSetPostCategories/WPSetPostCategoriesFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetPosts/WPGetPostsStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPSetPostCustomField/WPSetPostCustomFieldStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/WPGetPostCustomField/WPGetPostCustomFieldStageFactory.php";
require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Stages/ArrayPath/ArrayPathStageFactory.php";

class StagesRegistration
{
    /**
     * Registers all the available stages for usage in the plugin
     */
    public static function registerStages() {

        // Operational and misc stages
        StageFactory::registerFactory(new ArrayCountStageFactory());
        StageFactory::registerFactory(new ArrayPathStageFactory());
        StageFactory::registerFactory(new ExplodeStringStageFactory());
        StageFactory::registerFactory(new JSONDecodeStageFactory());
        StageFactory::registerFactory(new JSONEncodeStageFactory());
        StageFactory::registerFactory(new RandomArrayItemStageFactory());
        StageFactory::registerFactory(new RandomValueStageFactory());
        StageFactory::registerFactory(new SetValueStageFactory());
        StageFactory::registerFactory(new SumOperationStageFactory());

        // Wordpress related stages
        StageFactory::registerFactory(new WPCreatePostStageFactory());
        StageFactory::registerFactory(new WPGetCategoriesFactory());
        StageFactory::registerFactory(new WPGetPostCustomFieldStageFactory());
        StageFactory::registerFactory(new WPGetPostsStageFactory());
        StageFactory::registerFactory(new WPGetTagsFactory());
        StageFactory::registerFactory(new WPSaveMediaStageFactory());
        StageFactory::registerFactory(new WPSetPostCategoriesFactory());
        StageFactory::registerFactory(new WPSetPostCustomFieldStageFactory());
        StageFactory::registerFactory(new WPSetPostTagsFactory());
    }
}