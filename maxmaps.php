<?php
/*
Plugin Name: Maxmaps
Description: Easy map with markers
Version: 1.0
Author: Max Tkachov
Author URI: http://maxmaps.dev
Plugin URI: http://maxmaps.dev
*/

define('MAX_MAPS_DIR', plugin_dir_path(__FILE__));
define('MAX_MAPS_URL', plugin_dir_url(__FILE__));


function max_maps_load(){
 
    if(is_admin()) // подключаем файлы администратора, только если он авторизован
        require_once(MAX_MAPS_DIR.'includes/admin.php');
		require_once(MAX_MAPS_DIR.'includes/core.php');
}
max_maps_load();

register_activation_hook(__FILE__, 'max_maps_activation');
 
function max_maps_activation() {
    register_uninstall_hook(__FILE__, 'max_maps_uninstall');
}
 
function max_maps_uninstall(){
}