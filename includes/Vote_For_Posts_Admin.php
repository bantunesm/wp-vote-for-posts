<?php

namespace VoteForPosts;

class Vote_For_Posts_Admin
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
        // Enqueue admin-specific styles here.
    }

    public function enqueue_scripts()
    {
        // Enqueue admin-specific scripts here.
    }

    /**
     * Adds a metabox to the post edit screen to show the voting results.
     */
    public function add_meta_boxes()
    {
        add_meta_box(
            'vote_for_posts_metabox',                 // Metabox ID
            __('Vote Results', 'vote-for-posts'),    // Metabox title, localized
            [$this, 'display_votes_metabox'],        // Callback function to display the metabox content
            'post',                                   // Post type where the metabox will be added
            'side',                                   // Context (where on the page)
            'high'                                    // Priority of the metabox
        );
    }

    /**
     * Displays the voting results in the metabox.
     */
    public function display_votes_metabox($post)
    {
        // Retrieve the votes for the post
        $votes = get_post_meta($post->ID, 'votes', true);
        if (!$votes) {
            $votes = ['yes' => 0, 'no' => 0];
        }

        $total_votes = $votes['yes'] + $votes['no'];
        $yes_percentage = $total_votes > 0 ? round(($votes['yes'] / $total_votes) * 100, 2) : 0;
        $no_percentage = $total_votes > 0 ? round(($votes['no'] / $total_votes) * 100, 2) : 0;

        // Display the percentages
        echo "<p>" . sprintf(__('Percentage of 🙂: %s%%', 'vote-for-posts'), $yes_percentage) . "</p>";
        echo "<p>" . sprintf(__('Percentage of 😔: %s%%', 'vote-for-posts'), $no_percentage) . "</p>";
        // dump($votes);
    }

    /**
     * Adds a new column to the Posts admin table.
     */
    public function add_votes_column($columns)
    {
        $columns['vote_results'] = __('Vote Results', 'vote-for-posts');
        return $columns;
    }

    /**
     * Displays the vote results in the custom column for each post.
     */
    public function display_votes_column($column, $post_id)
    {
        if ($column == 'vote_results') {
            $votes = get_post_meta($post_id, 'votes', true);

            if (!$votes) {
                $votes = ['yes' => 0, 'no' => 0];
            }

            $total_votes = $votes['yes'] + $votes['no'];
            $yes_percentage = $total_votes > 0 ? round(($votes['yes'] / $total_votes) * 100, 2) : 0;
            $no_percentage = $total_votes > 0 ? round(($votes['no'] / $total_votes) * 100, 2) : 0;

            if ($total_votes > 0) {
                echo "🙂 $yes_percentage% | 😔 $no_percentage% ($total_votes)";
            } else {
                echo __('No votes for now.', 'vote-for-posts');
            }
        }
    }

    /**
     * Initializes the hooks related to the admin area.
     */
    public function run()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_filter('manage_posts_columns', [$this, 'add_votes_column']);
        add_action('manage_posts_custom_column', [$this, 'display_votes_column'], 10, 2);
    }
}
