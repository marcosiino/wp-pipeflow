# 2024-12-19 - 1.2.0 #

## New features ##

- Added new stage `WPGetPosts` which allows to get the latest N posts from wordpress (or all the posts), by specifying which fields (and custom fields) to retrieve

- Added new stage `WPGetPostCustomField` which allows to get a specified custom field from a wordpress post

- Added new stage `WPSetPostCustomField` which allows to set a specific custom field to a wordpress post

- Added a new very useful stage `ArrayPath` which allows to get a specific element inside an array or associative array given the keypath to traverse it.

## Breaking changes ##

- The attribute `contextReference` of `<param>` nodes in pipelines XML configurations cannot have the `indexed` value anymore: instead you need to use the `keypath` value along with the `keypath` attribute. This allows you to specify a dot-separated key path, which enables to also reference nested elements from an array context parameter. If you need to reference a simple key or array element, just specify the key or the element number as a string, e.g. "theKey" or "1". 
In addition to this, now you can reference to complex array nested elements, for example, if you need to reference the element at key "info" of the second element of the array at the "subArray" key of the referenced array, you can use this keypath: "subArray.1.info".

### How to update previous xml configurations: ###

Before:

```xml
    <param name="parameterName" contextReference="indexed" index="1">arrayParameter</param>
```

Now:

```xml
    <param name="parameterName" contextReference="keypath" keypath="1">arrayParameter</param>
```

Before:

```xml
    <param name="parameterName" contextReference="indexed" index="key">arrayParameter</param>
```

Now:

```xml
    <param name="parameterName" contextReference="keypath" keypath="key">arrayParameter</param>
```

Plus now you can also do something like this to reference to ```arrayParameter['nestedArray][2]['scores'][1]['value']```:

```xml
    <param name="parameterName" contextReference="keypath" keypath="nestedArray.2.scores.1.value">arrayParameter</param>
```