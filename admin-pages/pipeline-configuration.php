<?php
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/utils/settings.php");
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/StageFactory.php");
require_once(ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/Interfaces/AbstractStageFactory.php");

function register_pipeline_configuration_setup_settings() {
    register_setting('pipeflow_pipeline_setup_group', 'pipeline_configuration_json');
}
function pipeline_configuration_page() {
    ?>
    <div class="wrap">
        <h2>Content Generation Pipeline Setup</h2>

        <h3>Available Stages</h3>
        <div id="available-stages">
        <?php
            foreach (StageFactory::getRegisteredFactories() as $factory) {
                echo $factory->getStageDescriptor()->getStageHTMLDescription();
            }
        ?>
        </div>

        <h2>Content Generation Pipeline Setup</h2>

        <form method="post" action="options.php">
            <?php
            settings_fields('pipeflow_pipeline_setup_group');
            do_settings_sections('pipeflow_pipeline_setup_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Pipeline Configuration JSON</th>
                    <td><textarea cols=80 rows=10 name="pipeline_configuration_json"><?php echo Settings::get_pipeline_configuration_json(); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
