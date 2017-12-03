<?php
/**
 * Plugin Name: System Booter
 * Description: Autoloaded Plugin that registers the System and adds additional functionality. Can then be used globally using the app() helper function!
 * Version: 1.0.0
 * Author: Leo Studer
 * License: MIT License
 */
if( ! isset($GLOBALS['root_dir']) ){
	die('Bedrock Root dir not set!');
}

\le0daniel\System\App::init($GLOBALS['root_dir']);
