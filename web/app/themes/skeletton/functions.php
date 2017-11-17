<?php
/* %NameSpace% */

use le0daniel\System\App;
use le0daniel\System\Helpers\TwigFilters;
/* %UseWPExtender% */

/**
 * Basic Variables, needed here
 *
 * The Theme Name should be the same as the theme folder name!
 */

/* %ThemeName% */
$view_root  = __DIR__.'/resources/views';

/**
 * Run the Application
 * It's only necessary to run the app if you need additional features like
 * Routing and stuff like that.
 *
 * The app is initialized in the mu-plugins --> system-booter.php
 */
if( ! class_exists(App::class) ){
	echo 'Application not loaded!';
	die();
}

/**
 * Change root dir if needed!
 * Should be set outside the web dir!
 *
 * For more advanced features run the app!
 *
 * Plain Cache enables caching for HTML files,
 * The file will be recompiled if the Data changes,
 * but Plugins and Hooks like wp_head / wp_footer will not be runned again!!
 *
 * So disable it if you are not sure!
 */
App::init($GLOBALS['root_dir']);
//app()->run();

/* Load the translation file */
load_theme_textdomain($theme_name,__DIR__.'/resources/lang/');
TwigFilters::$translation_context = $theme_name;

/* Configure the view */
view()
	->setRootDir($view_root)
	->setPlainCache(false)
	->addIncludePath($view_root.'/components','c')
	->addIncludePath($view_root.'/pages',     'pages')
	->addIncludePath($view_root.'/layouts',   'layouts')
	->addIncludePath($view_root.'/shortcodes','shorts')

	/* Add The context */
	->addContext( resolve('wp.context') )

	/* Share some data! */
	->share('meta_tags',[]);

/* Extend Wordpress */
resolve(WordPressExtender::class)->boot();