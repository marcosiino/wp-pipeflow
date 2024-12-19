<?php
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/utils/settings.php");
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageFactory.php");
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php");

function register_pipeline_configuration_setup_settings() {
    register_setting('pipeflow_pipeline_setup_group', 'pipeline_configuration');
}
function pipeline_configuration_page() {
    ?>
    <div class="wrap">
        <h2>Content Generation Pipeline Setup</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('pipeflow_pipeline_setup_group');
            do_settings_sections('pipeflow_pipeline_setup_group');
            ?>
            <h3>Pipeline Configuration JSON</h3>
            <textarea id="pipeflow-configuration-editor" name="pipeline_configuration"><?php echo Settings::get_pipeline_configuration(); ?></textarea>

            <?php submit_button(); ?>
        </form>

        <h3>Available Stages</h3>
        <div id="available-stages">
            <?php
            foreach (StageFactory::getRegisteredFactories() as $factory) {
                echo "<pre>" . $factory->getStageDescriptor()->getStageHTMLDescription() . "</pre>";
            }
            ?>
        </div>

    </div>
    <?php
}
