<?php
// Avoid direct access.
if (!defined('WPINC')) {
    die;
}
$original_title = __('Was this article helpful?', 'bantunes-vote-posts');
$title_after_vote = __('Thank you for your feedback!', 'bantunes-vote-posts');
?>

<!-- Voting form -->
<div id="vote-for-posts-form" class="vote-for-posts">
    <div class="inner">
        <p class="vote-title"></p>
        <div class="buttons">
            <button class="vote-btn" data-vote="yes">☻ <span class="text-black"><?php esc_html_e('Yes', 'bantunes-vote-posts'); ?></span></button>
            <button class="vote-btn" data-vote="no">☹︎ <span class="text-black"><?php esc_html_e('No', 'bantunes-vote-posts'); ?></span></button>
        </div>
    </div>
</div>
