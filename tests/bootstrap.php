// file: tests/bootstrap.php
<?php

// Definisce WP_PIPEFLOW_PLUGIN_PATH se non è già definito
if (!defined('WP_PIPEFLOW_PLUGIN_PATH')) {
    define('WP_PIPEFLOW_PLUGIN_PATH', realpath(__DIR__ . '/../') . '/');
}
