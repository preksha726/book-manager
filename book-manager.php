<?php
/**
 * Plugin Name: Book Manager
 * Description: A simple plugin to manage books and integrate with an external microservice for recommendations.
 * Version: 1.0
 * Author: Your Name
 * License: GPL2
 */

// Prevent direct access to the file
defined('ABSPATH') or die('No script kiddies please!');

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-book-manager.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-microservice-integration.php';

// Initialize the plugin
function bm_initialize_plugin() {
    $book_manager = new Book_Manager();
    $book_manager->init();
}
add_action('plugins_loaded', 'bm_initialize_plugin');
