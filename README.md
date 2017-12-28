# Wordpress Starter (Integration: wp_system)

This is the official documentation of le0daniel/wp_system

It is based on a custom version of Bedrock ([roots.io](roots.io)) and integrates the wp_system. The goal of the system is to have modern PHP tools for creating your **custom** WP Theme. It includes:

* The powerful [Laravel](https://laravel.com) 5 Service Container (Dependency injection, Type Hinting), 
* [Symfony Twig](https://twig.symfony.com/) 2 as Templating engine
* [Symfony Console](http://symfony.com/doc/current/components/console.html) for interacting with the App
  * [Symfony Process](http://symfony.com/doc/current/components/process.html) 
* Clear File Structure (almost same as [Bedrock](https://roots.io/bedrock/), except web -> public)
  * Must use plugins
  * .env config with different config files per environment
  * composer for dependency management
* Mix (webpack) for versioning and compiling assets (js|css)



## File Structure

```
YourProject/
|—— cache/
|   |—— rendered/
|   |—— twig/
|—— config/
|   |—— application.php
|   |—— system.php
|   |—— environments/
|       |—— development.php
|       |—— production.php
|       |—— staging.php
|—— public/
|   |—— app/
|       |—— mu-plugins/
|       |—— plugins/
|       |—— themes/
|       |—— uploads/
|       |—— maintenance.php
|   |—— wp/
|   |—— wp-config.php
|   |—— index.php
|   |—— .htaccess
|—— storage/
|   |—— log/
|—— vendor/
|—— node_modules/
```

The web root is `public` 

For better organizaion `wp-content` is in `app` and Wordpress is in `wp` and all dependencies, configurations, files are kept out of the web root itself.

**Theme Structure**

```
YourProject/public/app/themes/YourTheme
|—— App/
|   |—— PostTypes/
|   |—— ServiceProvider/
|   |—— ShortCodes/
|   |—— WordPressExtender.php
|—— resources/
|   |—— assets/
|       |—— js/
|       |—— scss/
|   |—— lang/
|   |—— views/
|       |—— components/
|       |—— layouts/
|       |—— pages/
|       |—— shortcodes/
|—— static/
|—— functions.php
|—— style.css
```

The goal is to keep a clear structure in the Theme. All files which are accessed over the internet should be in the static folder. By default, webpack compiles Js / Scss (script.js/style.scss) to static/

The `functions.php` file contains the configuration of the Application and some Bindings. Configure your theme here. 

The `WordPressExtender.php` wraps some common tasks (shortcodes including visual composer componets, navs, posttypes) and simplifies them by giveing them a clear structure.

All logic should be placed into the default wordpress templates (seen as controllers) and then passed to Twig to render (more in the view section).



## Installation

**Requirements**

* composer
* PHP >= 7

To install in the directory YourProjectName run:

 `composer create-project le0daniel\wp_starter YourProjectName`

After the installation `cd YourProjectName ` and run the installer `php installer`

This will help you create your theme with the correct namespace, connect to the DB. After this, take a look at the options in your `.env` file.

```
# Enables reloding of assets if you run 'npm run watch'
HOT_RELOAD=false

# Exposes the duration the request took
EXPOSE_DURATION=false

# Disable setting of security headers like 'X-FRAME-OPTIONS'
DISABLE_SECURITY_HEADERS=false

# Your environment and URL
WP_ENV=development
WP_HOME=http://mywordpress.localhost

# Salts, generate them using wp dotenv 
```

**After you're done, it's important to run** `composer dump-autoload` **because psr-4 autoloading paths have changed**

**Now you are ready**



## functions.php

First of all, namespaces are used and should be used where possible. The file contains basic configuration, like `$theme_name`  `$view_root`  and the usual wordpress theme setting. 

More important are the **bindings**. By default everything is resolved through the [Laravel Service Container](https://laravel.com/docs/5.5/container). You can overwrite everything the app uses. 

You can either add your bindings into the functions file or you can simply create and register (config/system) a ServiceProvider.

Following aliases can easylie be overwritten to add more specific functionality. Your class should always extend the base class to keep functionality. Take a look at the individual pages for more information about what they provide. A lot is also explained in the view section.

```php
/* Get the dependency container through the app helper */
$app = app();

/* Create your individual bindings */
$app->bind('wp.context',	YourClass::class); // le0daniel\System\WordPress\Context 
$app->bind('wp.metafield',	YourClass::class); // le0daniel\System\WordPress\MetaField
$app->bind('wp.page',		YourClass::class); // le0daniel\System\WordPress\Page
$app->bind('wp.post',		YourClass::class); // le0daniel\System\WordPress\Post
$app->bind('wp.site',		YourClass::class); // le0daniel\System\WordPress\Site
$app->bind('wp.user',		YourClass::class); // le0daniel\System\WordPress\User
$app->bind('wp.shortcode',	YourClass::class); // le0daniel\System\WordPress\ShortCode
```

The view configuration options are explaind below in the view section.



## View

The view class (`le0daniel\System\View\View`) is the wrapper around twig. It contains easy methods to render, add functions and filters, set include paths for twig. The view object is registered as a singleton in the Service Container. Following configuration options / methods exist:

```php
/** @var le0daniel\System\View\View $view */
$view = view();

/**
 * Sets the view root dir. 
 * MUST be set before anything can be rendered. Cannot be changed after 
 * something has been rendered.
 */
$view->setRootDir('path/to/your/views');

/**
 * This adds an alias (called: @alias in twig template)
 * Those paths can be added any time.
 */
$view->addIncludePath('path/to/your/path','alias')
  
/**
 * Share Data with all views, can be accessed by every template!
 */
$view->share('key','value');  

/**
 * Add Function and Filters
 * Documentation of Available filters and Functions found in twig official Docs
 */
$view->addFunction(
  /* callable */ '\le0daniel\myfunction',
  /* string   */ 'FunctionAliasTemplate',
  /* array    */ ['Params according to twig documentation']
);

/**
 * Render
 * Renders a view to html
 *
 * @return string $html
 */
$string_html = $view->render(
  'template.twig',
  ['Data'=>'Array'],
  /* Default: true  */ $with_context,
  /* Default: false */ $force_plain_cache,
  /* Default: -1    */ $cache_duration,
); 

/**
 * Show
 * Renders and outputs a view
 *
 * if terminate is true, the script quits after this.
 * @return void
 */
$view->show(
  'template.twig',
  ['Data'=>'Array'],
  /* Default: true  */ $terminate
); 
```



Following filters are available for all templates:

* `theme_path`  returns url relative to theme root
* `static_path`  returns url relative to the themes static folder. This should be used to include files any file
* `mix(rewrite=false)`  returns versioned url (with id query) if possible, optionally use `mix(true)` to rewrite the url into a more SEO friendly format: `.../static/##key##/script.js` 
* `t` or `translate` translate using the default context set in `functions.php` file
* `eclipse(lenght=55)` shortens a string to a max lenght, default is 55 
  You can pass any lenght using `eclips(200)`



Following functions are available for all templates:

* `function(callable)` calls a function (usecase mostly for Wordpress functions) and returns the output or echo. The output is always escaped, so if you want to use the output as safe HTML be sure to add the `raw` filter. Example: `{{ function('my_nav_function') | raw }}`
  By default this is used to call `wp_head` and `wp_footer` 
* `field(name,id=false)` returns a ACF field value (using `get_field`)
  ​