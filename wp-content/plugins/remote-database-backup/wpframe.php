<?php
/**
 * WPFrame
 * A simple framework to make WP Plugin development easier.
 */

$GLOBALS['wpframe_home'] = get_option('home');
$GLOBALS['wpframe_wordpress'] = $GLOBALS['wpframe_siteurl'] = get_option('siteurl');
if(!$GLOBALS['wpframe_wordpress']) $GLOBALS['wpframe_wordpress'] = $GLOBALS['wpframe_home'];
$GLOBALS['wpframe_plugin_name'] = basename(dirname(__FILE__));
$GLOBALS['wpframe_plugin_folder'] = $GLOBALS['wpframe_siteurl'] . '/wp-content/plugins/' . $GLOBALS['wpframe_plugin_name'];
//$GLOBALS['wpframe_plugin_data'] = get_plugin_data($GLOBALS['wpframe_plugin_name'] . '.php');
//* :DEBUG: */ $GLOBALS['wpdb']->show_errors();

if(!function_exists('stopDirectCall')) { //Make sure multiple plugins can be created using WPFrame

/// Need to register a page to use it in a plugin in WP2.8.1+. THIS IS A HACK.
function wpf_register_pages($pages, $folder) {
	foreach($pages as $p) wpf_register_page($folder . DIRECTORY_SEPARATOR . $p);
}
function wpf_register_page($file) {
	global $_registered_pages;
	$hookname = get_plugin_page_hookname($file, '' );
	$_registered_pages[$hookname] = true;
}

/// Make sure that the user don't call this file directly - forces the use of the WP interface
function stopDirectCall($file) {
	if(preg_match('#' . basename($file) . '#', $_SERVER['PHP_SELF'])) die('Don\'t call this page directly.'); // Stop direct call
}

/// Shows a message in the admin interface of Wordpress
function showMessage($message, $type='updated') {
	if($type == 'updated') $class = 'updated fade';
	elseif($type == 'error') $class = 'updated error';
	else $class = $type;
	
	print '<div id="message" class="'.$class.'"><p>' . __($message, $GLOBALS['wpframe_plugin_name']) . '</p></div>';
}

/// Globalization function - Returns the transilated string
function t($message) {
	return __($message, $GLOBALS['wpframe_plugin_name']);
}

/// Globalization function - prints the transilated string
function e($message) {
	_e($message, $GLOBALS['wpframe_plugin_name']);
}

}