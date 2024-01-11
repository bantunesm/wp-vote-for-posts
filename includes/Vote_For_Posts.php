<?php

namespace VoteForPosts;

class Vote_For_Posts
{
    private $plugin_slug;
    private $version;
    private $plugin_admin;
    private $plugin_public;

    public function __construct()
    {
        $this->plugin_slug = 'vote-for-posts';
        $this->version = '1.0.0';

        $this->initialize_objects();
    }

    private function initialize_objects()
    {
        $this->plugin_admin = new Vote_For_Posts_Admin($this->plugin_slug, $this->version);
        $this->plugin_public = new Vote_For_Posts_Front_End($this->plugin_slug, $this->version);
    }

    public function run()
    {
        // Hooks for admin area

        // Hooks for public-facing part of the site
        add_action('wp_enqueue_scripts', array($this->plugin_public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this->plugin_public, 'enqueue_scripts'));
        add_filter('the_content', array($this->plugin_public, 'add_vote_form_to_content'), 20, 1);
    }

    public function get_plugin_slug()
    {
        return $this->plugin_slug;
    }

    public function get_version()
    {
        return $this->version;
    }
}
