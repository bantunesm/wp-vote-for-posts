jQuery(document).ready(function ($) {
    var post_id = voteForPosts.post_id;
    var is_admin = voteForPosts.is_admin;

    // Define original and post-vote titles
    var original_title = 'Was this article helpful?';
    var title_after_vote = 'Thank you for your feedback!';

    // Display the original title initially
    $('.vote-title').text(original_title);

    // Check if the user has already voted for this post
    var hasVoted = localStorage.getItem('voted_for_post_' + post_id);

    // Apply changes if the user has already voted
    if (hasVoted) {
        $('.vote-title').text(title_after_vote);
        applyVotedStyle(hasVoted);
        displayVoteResults(); // Display the voting results
    }

    // Event handler for the vote buttons
    $('.vote-btn').on('click', function (e) {
        e.preventDefault();
        var vote = $(this).data('vote');
        submitVote(vote);
    });

    function submitVote(vote) {
        // AJAX request to submit the vote
        $.ajax({
            url: voteForPosts.ajax_url,
            type: 'post',
            data: {
                action: 'vote_for_posts',
                nonce: voteForPosts.nonce,
                vote: vote,
                post_id: post_id
            },
            success: function (response) {
                if (response.success) {
                    updateUIAfterVote(response, vote);
                } else {
                    // Handle error in voting
                    $('.vote-results').show().text("Error during voting.");
                }
            },
            error: function () {
                // Handle AJAX request errors
                $('.vote-results').show().text("AJAX request failed.");
            }
        });
    }

    function displayVoteResults() {
        var cachedResults = localStorage.getItem('vote_results_post_' + post_id);
        if (cachedResults) {
            try {
                var resultsData = JSON.parse(cachedResults);
                updateVotePercentages(resultsData);
            } catch (e) {
                console.error("Error parsing cached results: ", e);
            }
        } else {
            // AJAX request to get the current voting results
            $.ajax({
                url: voteForPosts.ajax_url,
                type: 'post',
                data: {
                    action: 'get_vote_results',
                    post_id: post_id
                },
                success: function (response) {
                    if (response.success) {
                        updateVotePercentages(response.data);
                        // Cache the results for future use
                        localStorage.setItem('vote_results_post_' + post_id, JSON.stringify(response.data));
                    } else {
                        // Handle failure to fetch voting results
                        $('.vote-results').show().text("Unable to fetch voting results.");
                    }
                },
                error: function () {
                    // Handle errors in AJAX request for voting results
                    $('.vote-results').show().text("AJAX request for results failed.");
                }
            });
        }
    }

    function updateVotePercentages(data) {
        $('.vote-btn[data-vote="yes"] .text-black').text(data.yes_percentage + "%");
        $('.vote-btn[data-vote="no"] .text-black').text(data.no_percentage + "%");
    }

    function applyVotedStyle(votedOption) {
        // Remove the voted style from all buttons
        $('.vote-btn').removeClass('voted-style');

        // Add the voted style to the button that was clicked
        $('.vote-btn[data-vote="' + votedOption + '"]').addClass('voted-style');
    }

    function updateUIAfterVote(response, vote) {
        // Update the UI with the voting results
        $('.vote-title').text(title_after_vote);
        applyVotedStyle(vote);
        updateVotePercentages(response.data);
        // Disable the voting buttons and store the vote in local storage
        $('.vote-btn').prop('disabled', true);
        localStorage.setItem('voted_for_post_' + post_id, vote);
    }
});
