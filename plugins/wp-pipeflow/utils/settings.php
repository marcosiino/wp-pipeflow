<?php
define('DEFAULT_AUTO_GENERATION_INTERVAL', 43512);

const PIPELINE_CONFIGURATION_DEFAULT = "
<?xml version=\"1.0\" encoding=\"utf-8\" ?>
<pipeline id=\"example-pipeline\">
    <stages>
        <!-- Set the value of exampleArray to an array of three items: One, Two, Tree -->
        <stage type=\"SetValue\">
    		<settings>
    			<param name=\"parameterName\">exampleArray</param>
    			<param name=\"parameterValue\">
    				<item>One</item>
    				<item>Two</item>
    				<item>Three</item>
    			</param>
    		</settings>
    	</stage>
    	<!-- Set the value of exampleArrayReference parameter to the item at index 1 of exampleArray parameter -->
    	<stage type=\"SetValue\">
    		<settings>
    			<param name=\"parameterName\">exampleArrayReference</param>
    			<param name=\"parameterValue\" contextReference=\"indexed\" index=\"1\">exampleArray</param>
    		</settings>
    	</stage>
    	
    	<!-- Example of string concatenation with a context parameter using %% placeholders -->
    	<stage type=\"SetValue\">
    		<settings>
    			<param name=\"parameterName\">exampleString1</param>
    			<param name=\"parameterValue\">This is an example string which references the value of an existing parameter in the context: %%exampleArrayReference%%</param>
    		</settings>
    	</stage>
    	
    	<!-- Example of string concatenation with a specific context array item using %% placeholders -->
    	<stage type=\"SetValue\">
    		<settings>
    			<param name=\"parameterName\">exampleString2</param>
    			<param name=\"parameterValue\">This is another example string which references the value of a specific item inside an existing array parameter in the context: %%exampleArray[0]%%</param>
    		</settings>
    	</stage>
    	
    	<!-- Sums the value of the referenced parameters specified in operandA and operandB. (In this case, since the operands are strings, the sum operation results in a string concatenation) -->
    	<stage type=\"SumOperation\">
    		<settings>
    			<param name=\"operandA\" contextReference=\"plain\">exampleString1</param>
    			<param name=\"operandB\" contextReference=\"plain\">exampleString2</param>
    			<param name=\"resultTo\">resultString</param>
    		</settings>
    	</stage>
    </stages>
</pipeline>
";

class Settings {
    public static function get_auto_generation_interval_secs() {
        return esc_attr(get_option('auto_generation_interval_secs', DEFAULT_AUTO_GENERATION_INTERVAL));
    }

    /**
     * @return int The Content Generation Pipeline configuration JSON
     */
    public static function get_pipeline_configuration() {
        return get_option('pipeline_configuration', PIPELINE_CONFIGURATION_DEFAULT);
    }
}