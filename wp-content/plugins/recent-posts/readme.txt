=== Recent Posts ===
Contributors: nickmomrik
Tags: posts, recent, list
Requires at least: 2.8
Tested up to: 3.0
Stable tag: trunk

Retrieves a list of the most recent posts

== Installation ==
1. Upload `recent-posts.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php mdv_recent_posts(); ?>` in your templates.

== Configuration ==
You may pass parameters when calling the function to configure some of the options.
Example: `mdv_recent_posts(10, '', '<br />', true, 5, false, false)`

The parameters:
$no_posts - sets the number of recent posts to display
$before - text to be displayed before the link to the recent post
$after - text to be displayed after the link to the recent post
$hide_pass_post - whether or not to display password protected posts
$skip_posts - allows skipping of a number of posts before showing the number of posts specified with the $no_posts parameter
$show_excerpts - allows the post excerpt to be output after the post title
$include_pages - allows recent pages to be show with recent posts

== Version History ==
1.1.2 - Added a space before rel="bookmark"
1.1.3 - Fix the space for real. Props Sandro Bonazzola
1.2   - Use the esc_* functions and cleanup coding standards
