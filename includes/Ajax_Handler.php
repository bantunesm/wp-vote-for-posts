<?php

namespace VoteForPosts;

class Ajax_Handler
{
    /**
     * Attaches functions to the WordPress AJAX hooks.
     */
    public function init()
    {
        add_action('wp_ajax_vote_for_posts', [$this, 'handle_vote']);
        add_action('wp_ajax_nopriv_vote_for_posts', [$this, 'handle_vote']);
        add_action('wp_ajax_get_vote_results', [$this, 'handle_get_vote_results']);
        add_action('wp_ajax_nopriv_get_vote_results', [$this, 'handle_get_vote_results']);
    }

    /**
     * Handles the AJAX request for voting.
     */
    public function handle_vote()
    {
        // Check the nonce for security
        check_ajax_referer('vote-for-posts-nonce', 'nonce');

        $vote = isset($_POST['vote']) ? sanitize_text_field($_POST['vote']) : '';
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        if (!get_post_status($post_id)) {
            wp_send_json_error(__('L\'article pour le vote n\'existe pas.', 'vote-for-posts'));
            wp_die();
        }

        // Validates the received data
        if (!$post_id || !in_array($vote, ['yes', 'no'])) {
            wp_send_json_error(__('Invalid voting data.', 'vote-for-posts'));
            wp_die();
        }

        // Saves the vote
        $this->save_vote($post_id, $vote);

        // Retrieves updated vote data for the post
        $votes = get_post_meta($post_id, 'votes', true);
        $total_votes = $votes['yes'] + $votes['no'];
        $yes_percentage = $total_votes > 0 ? round(($votes['yes'] / $total_votes) * 100, 2) : 0;
        $no_percentage = $total_votes > 0 ? round(($votes['no'] / $total_votes) * 100, 2) : 0;

        // Sends the voting percentages in response
        wp_send_json_success([
            'yes_percentage' => $yes_percentage,
            'no_percentage' => $no_percentage
        ]);
    }

    /**
     * Saves the vote in the post's meta field.
     */
    private function save_vote($post_id, $vote) {
        $voter_ip = $_SERVER['REMOTE_ADDR']; // Get visitor's IP
        $current_votes = get_post_meta($post_id, 'votes', true);

        if (!$current_votes) {
            $current_votes = ['yes' => 0, 'no' => 0, 'ips' => []];
        }

        // Check if IP already voted
        if (in_array($voter_ip, $current_votes['ips'])) {
            wp_send_json_error(__('You already voted.', 'vote-for-posts'));
            wp_die();
        }

        $current_votes[$vote]++;
        $current_votes['ips'][] = $voter_ip;

        update_post_meta($post_id, 'votes', $current_votes);
    }

    /**
     * Handles the AJAX request to get the voting results.
     */
    public function handle_get_vote_results()
    {
        // Get post_id from request
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

        // Get votes
        $votes = get_post_meta($post_id, 'votes', true);
        if (!$votes) {
            $votes = ['yes' => 0, 'no' => 0];
        }

        $total_votes = $votes['yes'] + $votes['no'];
        $yes_percentage = $total_votes > 0 ? round(($votes['yes'] / $total_votes) * 100, 2) : 0;
        $no_percentage = $total_votes > 0 ? round(($votes['no'] / $total_votes) * 100, 2) : 0;

        // Send response
        wp_send_json_success([
            'yes_percentage' => $yes_percentage,
            'no_percentage' => $no_percentage
        ]);
    }
}
