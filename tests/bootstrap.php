// file: tests/bootstrap.php
<?php

// Definisce PLUGIN_PATH se non è già definito
if (!defined('PLUGIN_PATH')) {
    define('PLUGIN_PATH', realpath(__DIR__ . '/../') . '/');
}
