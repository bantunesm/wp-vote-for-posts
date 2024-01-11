<?php

/**
 * Plugin Name: Vote for Posts
 * Plugin URI: https://www.brunoantunes.fr
 * Description: A simple voting system for WordPress articles
 * Version: 1.0
 * Author: Bruno Antunes
 * Author URI: https://www.brunoantunes.fr
 * License: GPL2
 * Text Domain: bantunes-vote-posts
 * Domain Path: /languages
 */

// Avoid direct access
if (!defined('WPINC')) {
    die;
}

// Include the autoloader from composer
require_once __DIR__ . '/vendor/autoload.php';

// Use statements
use VoteForPosts\Vote_For_Posts;
use VoteForPosts\Vote_For_Posts_Activator;
use VoteForPosts\Vote_For_Posts_Deactivator;
use VoteForPosts\Ajax_Handler;
use VoteForPosts\Vote_For_Posts_Admin;

/**
 * The code that runs during plugin activation and deactivation.
 * If needed.
 */
function activate_vote_for_posts()
{
    Vote_For_Posts_Activator::activate();
}

function deactivate_vote_for_posts()
{
    Vote_For_Posts_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_vote_for_posts');
register_deactivation_hook(__FILE__, 'deactivate_vote_for_posts');

/**
 * Begins execution of the plugin.
 */
function run_vote_for_posts()
{
    $plugin = new Vote_For_Posts();
    $plugin->run();
    $ajax_handler = new Ajax_Handler();
    $ajax_handler->init();
    $plugin_admin = new Vote_For_Posts_Admin('vote-for-posts', '1.0.0');
    $plugin_admin->run();
}

// Fires the plugin.
run_vote_for_posts();
