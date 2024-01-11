<?php

namespace VoteForPosts;

class Vote_For_Posts_Front_End
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(dirname(__FILE__)) . 'assets/css/front-end.css', array(), $this->version, 'all');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(dirname(__FILE__)) . 'assets/js/front-end.js', array('jquery'), $this->version, true);
        wp_localize_script($this->plugin_name, 'voteForPosts', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vote-for-posts-nonce'),
            'post_id' => get_the_ID(),
            'is_admin' => current_user_can('administrator')
        ));
    }


    public function display_vote_form()
    {
        include plugin_dir_path(__FILE__) . '../templates/vote-form-template.php';
    }

    public function add_vote_form_to_content($content)
    {
        if (is_single() && in_the_loop() && is_main_query()) {
            ob_start();
            $this->display_vote_form();
            $form = ob_get_clean(); // Read and delete

            $content .= $form;
        }
        return $content;
    }

    public function run()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('the_content', array($this, 'add_vote_form_to_content'));
    }
}
