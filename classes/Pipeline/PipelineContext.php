<?php

namespace Pipeline;

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
        $currentParameterValue = $this->context[$name];
        if(isset($currentParameterValue)) {
            // The parameter is already in the context, adding the value to it
            $currentParameterValue->addValue($value);
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
        unset($this->context[$name]);
    }

    /**
     * Gets the parameter with the given name.
     *
     * @param string $name
     * @return ContextParameterValue
     */
    public function getParameter(string $name): ContextParameterValue {
        return $this->context[$name];
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
                echo "<li>$value</li>";
            }
            $html .= "</ul></td>";
            $html .= "</tr>";
        }
        $html .= "</table>";
        $html .= "</div>";
        return $html;
    }

}