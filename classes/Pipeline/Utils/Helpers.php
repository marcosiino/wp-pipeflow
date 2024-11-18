<?php
namespace Pipeline\Utils;

use Pipeline\Exceptions\StageConfigurationException;
require_once WP_PIPEFLOW_PLUGIN_PATH . "classes/Pipeline/Exceptions/StageConfigurationException.php";

class Helpers
{
    /**
     * Gets the specified field value from the provided associative array and checks that it is present in the array (if $required is true)
     *
     * @param array $array An associative array
     * @param string $fieldName - The field to get
     * @param bool $required - Default: false. Whether the field is required. If true, an exception is thrown if the field is not present
     * @return mixed
     * @throws StageConfigurationException if field with $fieldName is not found in $array and $required is true
     */
    static function getField(array $array, string $fieldName, bool $required = false, mixed $default = null): mixed
    {
        // If the field is required and it doesn't exists in the array, an exception is thrown
        if ($required && !array_key_exists($fieldName, $array)) {
            throw StageConfigurationException::expectedFieldNotFound($fieldName);
        }

        // If the field is not required, and it doesn't exists in the array, the default value is returned
        if(!$required && !array_key_exists($fieldName, $array)) {
            return $default;
        }

        // The value is taken from the array
        $value = $array[$fieldName];

        // If it is required, and it is not set, throw an exception
        if ($required && !isset($value)) {
            throw StageConfigurationException::expectedFieldNotFound($fieldName);
        }
        return $value;
    }
}