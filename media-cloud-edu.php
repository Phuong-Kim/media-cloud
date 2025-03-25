<?php

/**
 * Plugin Name: Media Cloud Edu
 * Plugin URI: https://edu2word.com/
 * Description: Used properly with Edu2Work utilities
 * Version: 1.0
 * Author: Team EDU Work
 * Author URI: ///
 * License: GPLv2
 *
 *      ___                       ___           ___           ___     
 *     /\  \          ___        /\  \         /\  \         /\__\    
 *     \:\  \        /\  \       \:\  \       /::\  \       /::|  |   
 *      \:\  \       \:\  \       \:\  \     /:/\:\  \     /:|:|  |   
 *      /::\  \      /::\__\      /::\  \   /::\~\:\  \   /:/|:|  |__ 
 *     /:/\:\__\  __/:/\/__/     /:/\:\__\ /:/\:\ \:\__\ /:/ |:| /\__\
 *    /:/  \/__/ /\/:/  /       /:/  \/__/ \/__\:\/:/  / \/__|:|/:/  /
 *   /:/  /      \::/__/       /:/  /           \::/  /      |:/:/  / 
 *   \/__/        \:\__\       \/__/            /:/  /       |::/  /  
 *                 \/__/                       /:/  /        /:/  /   
 *                                             \/__/         \/__/    
 *
 */
$dir_MC_data = ABSPATH . 'wp-content/uploads/media-cloud-edu';
if(!file_exists($dir_MC_data)){
    mkdir($dir_MC_data);
}

define('MC_DATA', $dir_MC_data . '/');
define('API_URL', 'https://api2.toidayhoc.com/');
define('MC_FILE', '/wp-content/plugins/media-cloud-edu/');
define('MC_PATH', plugin_dir_path( __FILE__ ));
define('MC_CHANGE', str_replace('\\', '/', plugin_dir_path(__FILE__)));
define('MC_INCLUDES', dirname( __FILE__ ) . '/setting/includes/');
define('MC_ASSETS', plugins_url('settings/assets/', __FILE__ ));
if(file_exists(MC_PATH . 'view.php')) {
    require_once MC_PATH . 'view.php';
}
if (file_exists(MC_PATH . 'vendor/autoload.php')) {
    require_once MC_PATH . 'vendor/autoload.php';
}

if(file_exists(MC_PATH . 'settings/ajax.php')) {
    require_once MC_PATH . 'settings/ajax.php';
}

function MC_script() {
    if (isset($_GET['page']) && $_GET['page'] == 'MediaCloud-admin') {
        wp_enqueue_script('axios', 'https://cdn.jsdelivr.net/npm/axios@0.24.0/dist/axios.min.js', array(), null, false);
        wp_enqueue_script('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js', array(), null, false);
        wp_enqueue_style('sweetalert2', 'https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css', array(), null);
        wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js', array('jquery'), null, false);
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), null);
        wp_enqueue_style('bootstrap-icon', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css', array(), null);
		wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/1330f21d64.js', array(), null);
        wp_enqueue_script('MC', MC_ASSETS . 'index.js', array('jquery'), null, false);
        wp_enqueue_script('login', MC_ASSETS . 'login.js', array('jquery'), null, false);
        wp_enqueue_style('MC', MC_ASSETS . 'css/main.css', array(), '1.0', false);
        wp_localize_script('MC', 'MC_obj', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_localize_script('login', 'login_obj', array('ajaxurl' => admin_url('admin-ajax.php')));
    }
	wp_enqueue_script('src2', MC_ASSETS . '404src.js', array(), null, false);
}
add_action('admin_enqueue_scripts', 'MC_script');

function custom_enqueue_scripts() {
  wp_enqueue_script('src2', MC_ASSETS . '404src.js', array(), null, false);
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links_MediaCloud');

function add_action_links_MediaCloud ($actions) {
   $mylinks = array(
      '<a href="' . admin_url('options-general.php?page=MediaCloud-admin') . '">Settings</a>',
   );
   $actions = array_merge( $actions, $mylinks );
   return $actions;
}

require_once plugin_dir_path( __FILE__ ) . 'plugin-update-checker-master/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://edu2work.com/wp-content/uploads/update-media-cloud-edu.json',
    __FILE__,
    'media-cloud-edu'
);

