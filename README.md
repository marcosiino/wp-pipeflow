# WP-PipeFlow Plugin

WP-PipeFlow is a WordPress plugin designed to automatize wordpress tasks and workflows by setting up pipelines and running then (manually or automatically using crons).

Pipelines consists in an ordered list of stages which are executed one after the other, each one producing parameters as output and saving them in the pipeline Execution Context. Each stage can reference and use the context parameters that previous stages has saved in the context.

You can implement your custom stages, use third party stages created by someone else, and/or use the core stages.

You can, for example, create pipelines which creates wordpress post with data coming from internal or external sources, combining them, or any other workflow by implementing your custom stages.

## Table of Contents
- [Installation](#installation)
- [Setup Pipelines with XML](#setup-pipelines-with-xml)
- [XML Configuration Elements](#xml-configuration-elements)
- [Setting Up Stages Using Param Nodes](#setting-up-stages-using-param-nodes)
- [Referencing Context Parameters](#referencing-context-parameters)
    - [Plain Reference](#plain-reference)
    - [Keypath Reference](#keypath-reference)
    - [Last Reference](#last-reference)
- [Available Stages](#available-stages)
- [Wordpress Related Stages](#wordpress-related-stages)
    - [WPCreatePost](#wpcreatepost)
    - [WPGetCategories](#wpgetcategories)
    - [WPGetPostCustomField](#wpgetpostcustomfield)
    - [WPGetPosts](#wpgetposts)
    - [WPGetTags](#wpgettags)
    - [WPSaveMedia](#wpsavemedia)
    - [WPSetPostCategories](#wpsetpostcategories)
    - [WPSetPostCustomField](#wpsetpostcustomfield)
    - [WPSetPostTags](#wpsetposttags)
- [Operational Stages](#operational-stages)
    - [ArrayCount](#arraycount)
    - [ArrayPath](#arraypath)
    - [ExplodeString](#explodestring)
    - [JSONDecode](#jsondecode)
    - [JSONEncode](#jsonencode)
    - [RandomArrayItem](#randomarrayitem)
    - [RandomValue](#randomvalue)
    - [SetValue](#setvalue)
    - [SumOperation](#sumoperation)

## Installation

1. Download the WP-PipeFlow plugin.
2. Upload the plugin files to the `/wp-content/plugins/wp-pipeflow` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Setup Pipelines with XML

To set up a pipeline, create an XML configuration file with the following structure:

```xml
<?xml version="1.0" encoding="utf-8" ?>
<pipeline>
    <stage name="ExampleStage">
        <settings>
            <param name="exampleParam">value</param>
        </setings>
    </stage>
    <stage name="AnotherStage">
        <settings>
            <!-- A parameter which reference another context parameter -->
            <param name="anotherParam" contextReference="plain">exampleParam</param>
        </settings>
    </stage>
</pipeline>
```

For more informations on the available XML elements, see [XML Configuration Elements](#xml-configuration-elements).

## The Pipeline Context

The context is a mechanism that keeps track of parameters (variables) that are output by each executed stage. This context allows these parameters to be used as inputs for subsequent stages, enabling data to flow through the pipeline seamlessly.

When a stage completes its execution, it can produce output parameters (usally you specify the result of the operation into the resultTo settings param, which is the name of the Context Parameter which the stage will save into the context with the result of his work). These parameters might include strings, numbers, arrays (even associative arrays, which can be traversed using the [ArrayPath](#arraypath) stage, or encoded / decoded from JSON using [JSONEncode](#jsonencode) and [JSONDecode](#jsondecode) stages), or any other data generated during the stage.

Referencing Parameters: Subsequent stages in the pipeline can reference these stored parameters. This allows them to use the data produced by previous stages as their input. See [Referencing Context Parameters](#referencing-context-parameters) for more info.


## XML Configuration Elements

- `<pipeline>`: The root element that contains all stages.
- `<stage>`: Defines a stage in the pipeline. The `name` attribute specifies the stage type.
- `<settings>`: Sets the stage input parameters using `<param>` elements. The `name` attribute of `<param>` nodes specifies the parameter name. You can reference values of other existing [Context Parameter](#the-pipeline-execution-context) (See [Referencing Context Parameters](#referencing-context-parameters))


## Setting Up Stages Using Param Nodes

Each stage can have multiple inputs. You can setup them by using `<param>` nodes inside the `<settings>` node of each stage. For example:

```xml
<stage name="ExampleStage">
    <settings>
        <param name="exampleParam">value</param>
        <param name="anotherParam">anotherValue</param>
    </settings>
</stage>
```


## Referencing Context Parameters

You can reference context parameters in three ways:

### Plain Reference

Set the exampleParam setting of the stage with the value taken from the `contextParam`

```xml
<param name="exampleParam" contextReference="plain">contextParam</param>
```

### Keypath Reference
Set the exampleParam to the value of the inner element of `arrayContextParam` at `arrayContextParam['key1'][1]['key2']`

```xml
<param name="exampleParam" contextReference="keypath" keypath="key1.1.key2">arrayContextParam</param>
```

### Last element Reference Type

Returns the last element of the referenced array context parameter

```xml
<param name="exampleParam" contextReference="last">array</param>
```

## Available Stages

### Wordpress Related Stages

#### WPCreatePost
Creates a new WordPress post.

**Setup Parameters:**
- `postTitle`: The title of the post.
- `postContent`: The content of the post.
- `featuredImageId`: (optional) The media id of the featured image for this post.
- `publishStatus`: (optional, default: draft) The post publication status.
- `authorId`: (optional) The id of the author to assign to this post.
- `categoriesIds`: (optional, array) The ids of the categories to assign to this post.
- `tagsIds`: (optional, array) The ids of the tags to assign to this post.
- `resultTo`: (optional) The context parameter where the id of the created post is stored.

**Example XML:**
```xml
<stage type="WPCreatePost">
    <settings>
        <param name="postTitle">My Post Title</param>
        <param name="postContent">This is the content of the post.</param>
        <param name="publishStatus">publish</param>
        <param name="resultTo">createdPostId</param>
    </settings>
</stage>
```

#### WPGetCategories
Gets the WordPress categories available in this site and assigns them to an array context parameter.

**Setup Parameters:**
- `taxonomy`: (optional) The taxonomy for which you want to get the categories.
- `resultTo`: The name of the context parameter where the array containing the categories is saved.

**Example XML:**
```xml
<stage type="WPGetCategories">
    <settings>
        <param name="resultTo">categoriesArray</param>
    </settings>
</stage>
```

#### WPGetPostCustomField
Gets a custom field value for a specified post and assigns it to a context parameter.

**Setup Parameters:**
- `postId`: The ID of the post to retrieve the custom field from.
- `customFieldName`: The name of the custom field to retrieve.
- `resultTo`: The name of the context parameter where the custom field value is saved.

**Example XML:**
```xml
<stage type="WPGetPostCustomField">
    <settings>
        <param name="postId">123</param>
        <param name="customFieldName">my_custom_field</param>
        <param name="resultTo">customFieldValue</param>
    </settings>
</stage>
```

#### WPGetPosts
Gets posts from WordPress and assigns them to an associative array context parameter.

**Setup Parameters:**
- `postType`: (optional) The type of posts to retrieve. Default is 'post'.
- `limit`: (optional) The maximum number of posts to retrieve. Default is 20.
- `fields`: (optional) A comma-separated list of fields to return for each post.
- `resultTo`: The name of the context parameter where the associative array containing the posts is saved.

**Example XML:**
```xml
<stage type="WPGetPosts">
    <settings>
        <param name="postType">post</param>
        <param name="limit">10</param>
        <param name="fields">id,title,content</param>
        <param name="resultTo">postsArray</param>
    </settings>
</stage>
```

#### WPGetTags
Gets the WordPress tags available in this site and assigns them to an array context parameter.

**Setup Parameters:**
- `taxonomy`: (optional) The taxonomy for which you want to get the tags.
- `resultTo`: The name of the context parameter where the array containing the tags is saved.

**Example XML:**
```xml
<stage type="WPGetTags">
    <settings>
        <param name="resultTo">tagsArray</param>
    </settings>
</stage>
```

#### WPSaveMedia
Downloads and saves one or more media files into the WordPress Media Library.

**Setup Parameters:**
- `mediaURLs`: The URLs of the media to save into the WordPress media library.
- `convertToFormat`: (optional, default: jpeg) The format of the saved image.
- `compression`: (optional, default: 65) The image compression.
- `resultTo`: (optional) The name of the context parameter where the saved media ids are stored.

**Example XML:**
```xml
<stage type="WPSaveMedia">
    <settings>
        <param name="mediaURLs">https://example.com/image.jpg</param>
        <param name="resultTo">savedMediaIds</param>
    </settings>
</stage>
```

#### WPSetPostCategories
Sets the given categories to an existing post.

**Setup Parameters:**
- `postId`: The id of an existing post to which the specified categories will be assigned.
- `categories`: An array containing the categories ids to assign to the specified post.

**Example XML:**
```xml
<stage type="WPSetPostCategories">
    <settings>
        <param name="postId">123</param>
        <param name="categories">[1, 2, 3]</param>
    </settings>
</stage>
```

#### WPSetPostCustomField
Sets a custom field for a specified post.

**Setup Parameters:**
- `postId`: The ID of the post to update.
- `customFieldName`: The name of the custom field to set.
- `customFieldValue`: The value of the custom field to set.

**Example XML:**
```xml
<stage type="WPSetPostCustomField">
    <settings>
        <param name="postId">123</param>
        <param name="customFieldName">my_custom_field</param>
        <param name="customFieldValue">custom value</param>
    </settings>
</stage>
```

#### WPSetPostTags
Sets the given tags to an existing post.

**Setup Parameters:**
- `postId`: The id of an existing post to which the specified tags will be assigned.
- `tags`: An array containing the tag ids to assign to the specified post.

**Example XML:**
```xml
<stage type="WPSetPostTags">
    <settings>
        <param name="postId">123</param>
        <param name="tags">[1, 2, 3]</param>
    </settings>
</stage>
```

### Operational stages

#### ArrayCount
Counts the items in the specified array context parameter.

**Setup Parameters:**
- `arrayParameterName`: The name of the context parameter which contains the array.
- `resultTo`: The output context parameter where the item count is saved.

**Example XML:**
```xml
<stage type="ArrayCount">
    <settings>
        <param name="arrayParameterName">myArray</param>
        <param name="resultTo">itemCount</param>
    </settings>
</stage>
```

#### ArrayPath
Gets an item from an array given the item path within the array.

**Setup Parameters:**
- `array`: The array.
- `path`: A dot-separated list of keys within the array.
- `defaultValue`: (Optional) The value to return if the path does not exist.
- `resultTo`: The name of the context parameter where the item value is saved.

**Example XML:**
```xml
<stage type="ArrayPath">
    <settings>
        <param name="array">myArray</param>
        <param name="path">key1.key2</param>
        <param name="resultTo">itemValue</param>
    </settings>
</stage>
```

#### ExplodeString
Splits a string by using a string separator and returns an array with the split strings.

**Setup Parameters:**
- `inputString`: The string to split.
- `separator`: The separator string.
- `resultTo`: The output context parameter where the array of split strings is saved.

**Example XML:**
```xml
<stage type="ExplodeString">
    <settings>
        <param name="inputString">one,two,three</param>
        <param name="separator">,</param>
        <param name="resultTo">splitArray</param>
    </settings>
</stage>
```

#### JSONDecode
Decodes a JSON encoded string into an associative array saved as a context parameter.

**Setup Parameters:**
- `jsonString`: The string containing the JSON to decode.
- `resultTo`: The output context parameter where the decoded associative array is saved.

**Example XML:**
```xml
<stage type="JSONDecode">
    <settings>
        <param name="jsonString">{"key": "value"}</param>
        <param name="resultTo">decodedArray</param>
    </settings>
</stage>
```

#### JSONEncode
Encodes a JSON from an associative array saved in the given context parameter.

**Setup Parameters:**
- `associativeArray`: The name of the context parameter containing the associative array to encode to JSON.
- `resultTo`: The output context parameter where the encoded JSON string is saved.

**Example XML:**
```xml
<stage type="JSONEncode">
    <settings>
        <param name="associativeArray">myArray</param>
        <param name="resultTo">jsonString</param>
    </settings>
</stage>
```

#### RandomArrayItem
Picks and returns a random array item from the specified array context parameter.

**Setup Parameters:**
- `arrayParameterName`: The name of the array context parameter.
- `resultTo`: The name of the context parameter where the random picked element is saved.

**Example XML:**
```xml
<stage type="RandomArrayItem">
    <settings>
        <param name="arrayParameterName">myArray</param>
        <param name="resultTo">randomItem</param>
    </settings>
</stage>
```

#### RandomValue
Generates a random number.

**Setup Parameters:**
- `parameterName`: The name of the context parameter where the generated random value is saved.
- `minValue`: The minimum random value (included).
- `maxValue`: The maximum random value (not included).

**Example XML:**
```xml
<stage type="RandomValue">
    <settings>
        <param name="parameterName">randomNumber</param>
        <param name="minValue">1</param>
        <param name="maxValue">100</param>
    </settings>
</stage>
```

#### SetValue
Sets the specified value into a context's parameter with the specified name.

**Setup Parameters:**
- `parameterName`: The name of the parameter to which the fixed value is assigned.
- `parameterValue`: The fixed value to assign to the specified parameter.

**Example XML:**
```xml
<stage type="SetValue">
    <settings>
        <param name="parameterName">myParam</param>
        <param name="parameterValue">myValue</param>
    </settings>
</stage>
```

#### SumOperation
Sums (scalars), merges (arrays or array+scalar), or concatenates (strings or strings+scalars) two context's parameters and stores the result into the specified context's parameter.

**Setup Parameters:**
- `operandA`: The name of the context's parameter to be used as the first operand of the operation.
- `operandB`: The name of the context's parameter to be used as the second operand of the operation.
- `resultTo`: The name of the context's parameter to be used to store the result of the operation.

**Example XML:**
```xml
<stage type="SumOperation">
    <settings>
        <param name="operandA">paramA</param>
        <param name="operandB">paramB</param>
        <param name="resultTo">sumResult</param>
    </settings>
</stage>
```

## Creating Custom Stages

You can create your custom stages and publish them as an external WordPress plugin. This allows you to extend the functionality of WP-PipeFlow with your own custom logic, and if you want publish them for the public or keep it private for your own use.

### Steps to Create a Custom Stage

1. **Create a New Plugin:**
    - Create a new folder in the `wp-content/plugins` directory.
    - Inside this folder, create a PHP file for your plugin, e.g., `my-custom-stages.php`.

2. **Define the Plugin Header:**
    - Add the following header to your PHP file:
      ```php
      <?php
      /**
        * Plugin Name: My Custom Stages
        * Description: Custom stages for WP-PipeFlow.
        * Version: 1.0.0
        * Author: Your Name
        */
      ```

3. **Include Required Files:**
    - Include the necessary WP-PipeFlow files:
      ```php
      require_once ABSPATH . "wp-content/plugins/wp-pipeflow/classes/Pipeline/CorePipeFlow.php";
      ```

4. **Create Your Custom Stage:**
    - Create a new PHP file for your custom stage, e.g., `MyCustomStage.php`.
    - Define your custom stage class by extending `AbstractPipelineStage`:
      ```php
      <?php

      class MyCustomStage extends AbstractPipelineStage {
            private StageConfiguration $stageConfiguration;

            public function __construct(StageConfiguration $stageConfiguration) {
                 $this->stageConfiguration = $stageConfiguration;
            }

            public function execute(PipelineContext $context): PipelineContext {
                 // Your custom logic here
                 return $context;
            }
      }
      ```

5. **Create a Factory for Your Custom Stage:**
    - Create a factory class for your custom stage, e.g., `MyCustomStageFactory.php`:
      ```php
      <?php

      class MyCustomStageFactory implements AbstractStageFactory {
            public function instantiate(StageConfiguration $configuration): AbstractPipelineStage {
                 return new MyCustomStage($configuration);
            }

            public function getStageDescriptor(): StageDescriptor {
                 $description = "Description of your custom stage.";
                 $setupParameters = array(
                      "paramName" => "Description of the parameter.",
                 );
                 $contextInputs = array();
                 $contextOutputs = array();

                 return new StageDescriptor("MyCustomStage", $description, $setupParameters, $contextInputs, $contextOutputs);
            }
      }
      ```

6. **Register Your Custom Stage:**
    - In your main plugin file, register your custom stage factory:
      ```php
      add_action('plugins_loaded', function() {
            StageFactory::registerFactory(new MyCustomStageFactory());
      });
      ```

7. **Activate Your Plugin:**
    - Go to the WordPress admin panel and activate your plugin from the 'Plugins' menu.

By following these steps, you can create and publish custom stages for WP-PipeFlow as an external WordPress plugin. This allows you to extend the pipeline functionality with your own custom logic and share it with others.
## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Contributing

We welcome contributions to the WP-PipeFlow project! You can contribute by implementing new core stages to be added to the core WP-PipeFlow plugin or by improving the existing functionality. Your work could be part of the upcoming updates of WP-PipeFlow!

### How to Contribute

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Implement your changes.
4. Submit a pull request with a detailed description of your changes.

Thank you for your contributions!