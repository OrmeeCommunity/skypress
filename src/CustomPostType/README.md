# Skypress Custom Post Type

## Getting started

```sh
composer require ormeecommunity/skypress-custom-post-type
```

### Use a plugin

```php
<?php

/*
Plugin Name: Example Skypress
Description: Example Skypress
Author: OrmeeCommunity
Domain Path: /languages/
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use Skypress\Core\Kernel;

Kernel::execute('plugin', ['file' => __FILE__, 'slug' => 'example-skypress'], [
    'custom-post-type' => true, // Active custom post type module
]);
```

### Add a custom post type

Create a folder `mu-plugins/skypress/custom-post-types/` folder.

Create a ".json" file that you call as you wish (eg. `movie.json` => `mu-plugins/skypress/custom-post-types/movie.json` ) :


```json
{
    "key" : "movies",
    "params" : {
        "public"             : true,
        "publicly_queryable" : true,
        "show_ui"            : true,
        "show_in_menu"       : true,
        "query_var"          : true,
        "labels" : {
            "name": "Movie"
        },
        "supports" : [
            "title", "editor","author"
        ]
    }
}
```
