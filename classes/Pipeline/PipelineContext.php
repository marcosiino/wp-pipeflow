<?php

namespace Pipeline;
require_once "classes/Pipeline/ContextParameterValue.php";

/**
 * A Pipeline Context, which represents the full state of a pipeline at a given moments, with all its parameters and values at that point.
 */
class PipelineContext
{
    /**
     * @var array an associative array of string => ContextParameterValue which maps parameters name to their ContextParameterValue
     */
    private array $context = array();

    /**
     * Sets the parameter with the given name. If a parameter with the given name already exists in the context, adds the value to its array
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParameter(string $name, mixed $value): void {
        $name = strtoupper($name);

        if(array_key_exists($name, $this->context)) {
            // The parameter is already in the context, adding the value to it
            $currentParameterValue = $this->context[$name];
            $currentParameterValue->add($value);
        }
        else {
            // The parameter is new in the context, adding it with this value
            $this->context[$name] = new ContextParameterValue($value);
        }
    }

    /**
     * Deletes the given parameter from the context.
     *
     * @param string $name
     * @return void
     */
    public function deleteParameter(string $name): void {
        $name = strtoupper($name);
        unset($this->context[$name]);
    }

    /**
     * Gets the parameter with the given name.
     *
     * @param string $name
     * @return ContextParameterValue
     */
    public function getParameter(string $name): ?ContextParameterValue {
        $name = strtoupper($name);
        if(!array_key_exists($name, $this->context)) {
            return null;
        }
        return $this->context[$name];
    }

    /**
     * Checks if the given parameter exists in the context
     *
     * @param string $name
     * @return bool Returns true if the parameter exists, false otherwise
     */
    public function checkParameterExists(string $name): bool {
        $name = strtoupper($name);
        return array_key_exists($name, $this->context);
    }

    /**
     * Returns a html which visual represents the current context status
     * @return String
     */
    public function getHTMLDescription(): String {
        $html = "<div class=\"pipeline-context\">";
        $html .= "<table>";
        foreach($this->context as $paramName => $values) {
            $html .= "<tr>";
            //Parameter name column
            $html .= "<td>$paramName</td>";
            //Parameter value column
            $html .= "<td><ul>";
            foreach($values->getAll() as $value) {
                $html .= "<li>$value</li>";
            }
            $html .= "</ul></td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "</div>";
        return $html;
    }

}