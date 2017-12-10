<?php
/* %NameSpace% */

use le0daniel\System\App;
use le0daniel\System\Helpers\Language;
use le0daniel\System\Helpers\Path;
/* %UseWPExtender% */

/**
 * Basic Variables, needed here
 *
 * The Theme Name should be the same as the theme folder name!
 */

/* %ThemeName% */
$view_root  = __DIR__.'/resources/views';

/**
 * Checks if the App is available! If not, it fails!
 * Important, if you don't use the starter theme you
 * might need to init the App using App::init($root_dir)
 */
if( ! class_exists(App::class) || ! function_exists('app') ){
	echo 'Application not loaded!';
	die();
}

/**
 * -------------------------------------------------------------
 * Paths & Namespaces
 * -------------------------------------------------------------
 * Register all needed Paths and namespaces
 */
App::$config_dir = __DIR__.'/App/config';
load_theme_textdomain($theme_name,__DIR__.'/resources/lang/');
Language::$translation_context = $theme_name;
Path::$theme_dirname = $theme_name;
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
 */
$app = app();
$app->bind(AddLogicToWordpress::class,WordPressExtender::class);

/**
 * -------------------------------------------------------------
 * View
 * -------------------------------------------------------------
 * Configure the view object
 *
 * - Register needed Paths (especially @shorts for shortcuts)
 * - Add Context (default wp.context), resolved through container, lazy loaded
 *   must Implement CastArray
 * - Share Data with all views
 * - Enable global plain cache ()
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
 */
$app->run();