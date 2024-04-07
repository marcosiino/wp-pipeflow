<?php


function automatic_generation_settings_page() {
    ?>
    <div>
        <h2>Automatic Content Generation Settings</h2>

        <?php
        $next_scheduled = wp_next_scheduled( 'paginedacolorare_ai_cron_hook' );
        echo "<h3>Next article generation scheduled on:</h3>";
        echo(date('Y-m-d H:i:s', $next_scheduled) . " - " . date_default_timezone_get());
        ?>
    </div>
    <?php
}
