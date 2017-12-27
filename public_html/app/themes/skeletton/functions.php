<?php
/* %NameSpace% */

use le0daniel\System\App;
use le0daniel\System\Helpers\Language;
use le0daniel\System\Helpers\Path;
use le0daniel\System\Contracts\AddLogicToWordpress;
/* %UseWPExtender% */

/**
 * Basic Variables, needed here
 *
 * The Theme Name should be the same as the theme folder name!
 */

/* %ThemeName% */
$view_root  = __DIR__.'/resources/views';

/**
 * -------------------------------------------------------------
 * Check for App Existance
 * -------------------------------------------------------------
 * Checks if the App is available! If not, it fails!
 * Important, if you don't use the starter theme you
 * might need to init the App yourself using App::init($root_dir)
 *
 * $root_dir should be outside the webroot!
 */
if( ! class_exists(App::class) || ! function_exists('app') ){
	echo 'Application not loaded!'.PHP_EOL;
	die();
}

/**
 * -------------------------------------------------------------
 * Explanation & config
 * -------------------------------------------------------------
 * You can use the Default config dir if you don't set the
 * config dir! Not needed if you don't register any
 * Service Provider
 *
 * IMPORTANT: All described commands should be runned in the root
 *            dir!
 *
 * In the App directory you find the most logic:
 * --> PostTypes are a easy way to define custom Post types
 * --> ShortCodes are an easy way to generate Shortcodes and
 *     VisualComposer components & separating Logic from templates
 * --> ServiceProviders are used to add Services to the App,
 *     they must implement register and boot methods
 *
 * Use php console make:(shortcut|posttype) to generate Shorcuts
 * and Posttypes interactively!
 *
 * In the resource directory you find all Assets & View
 * --> views: includes all templates (Twig template engine)
 *     (https://twig.symfony.com/doc/2.x/templates.html)
 * --> assets/(js|scss) all compiled with laravel mix (WebPack Wrapper)
 *     check /webpack.mix.js
 *     Version control is enabled, use mix filter in Twig, always export to static
 *     Commands: $ npm run watch (Hot reload for dev, make sure to enable it in .env)
 *               $ npm run dev   (Same config as watch, but no reload)
 *               $ npm run production (All assets minified and assembled)
 *     (https://github.com/JeffreyWay/laravel-mix/blob/master/docs/basic-example.md)
 * --> lang/ contains language files, namespace is always the $theme_name
 * --> static/ contains all rendered assets!
 */


/**
 * Configure in /config/application.php if possible!
 * @var theme_dirname */
//Path::$theme_dirname = $theme_name;

/**
 * -------------------------------------------------------------
 * Paths & Namespaces
 * -------------------------------------------------------------
 * Register all needed Paths and namespaces
 *
 * You can use the Default config dir if you don't set the
 * config dir! Not needed if you don't register any
 * Service Provider
 *
 * Load the default Theme Text domain using the theme slug
 * --> This should not be changed
 *
 * Sets the Theme dirname!
 * Is used for path generateion! Do not change!
 */

load_theme_textdomain($theme_name,__DIR__.'/resources/lang/');
Language::$translation_context = $theme_name;


/**
 * -------------------------------------------------------------
 * Theme Support
 * -------------------------------------------------------------
 * Add theme support below
 */
//add_theme_support( 'title-tag' );
//add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', ]);

/**
 * -------------------------------------------------------------
 * Bindings
 * -------------------------------------------------------------
 * Register the needed bindings to run the App
 *
 * Here is the best point to change default classes like the
 * Wordpress\Context Class (extend|replace ...)
 * +--> bind('wp.context',MyClassWhichExtendsContext::class)
 * +--> wp.page, wp.site, wp.user
 *
 * All classes are resolved through the ServiceContainer, so
 * you can Type-Hint all dependencies!
 */
$app = app();
$app->bind(AddLogicToWordpress::class,WordPressExtender::class);

/**
 * -------------------------------------------------------------
 * View
 * -------------------------------------------------------------
 * Configure the view object
 *
 * - Register needed Paths (especially @shorts for shortcuts_builder)
 * - Add Context (default wp.context), resolved through container, lazy loaded
 *   must Implement CastArray
 * - Share Data with all views
 * - Enable global plain cache (Experimental!)
 */
view()
	->setRootDir($view_root)
	//->setPlainCache(true)
	->addIncludePath($view_root.'/components','c')
	->addIncludePath($view_root.'/pages',     'pages')
	->addIncludePath($view_root.'/layouts',   'layouts')
	->addIncludePath($view_root.'/shortcodes','shorts')

	/**
	 * Add The context
	 * Must implement CastArray contract
	 *
	 * Is lazy loaded! Resolved when needed!
	 * will be resolve container!
	 */
	//->addContext( 'wp.context' )

	/* Share some data! */
	->share('meta_tags',[]);

/**
 * -------------------------------------------------------------
 * Run
 * -------------------------------------------------------------
 * Runs the app, depending on mode, Http- or Cli Kernel is
 * loaded and runned!
 *
 * To follow the MVC Pattern, use the individual files as
 * Controllers, which should only pass data to the view!
 *
 * You can use them as classes too, just declare a class and define
 * a render method which should return an array [template, data]
 * The controller Class and method are resolved through the container
 * so you can Type Hint!
 *
 * To use this feature, set map_controllers_to_classes to true in
 * the config file, should not have any impact on performance
 */
$app->run();