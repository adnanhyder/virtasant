Safe Media Delete
Contributors: (this should be a list of wordpress.org userid's)
Donate link: https://profiles.wordpress.org/adnanhyder/
Tags: comments, spam
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Important things to consider, - Git Commits – Make use of proper git commits and commit messages to ensure a healthy git history. - Performance – Consider performance aspects, assume the plugin will be used in high traffic sites. - Edge Cases - Best Practices - Code Quality

== Description ==

Add a field on the Term Add [1] and Term Edit [2] page that allows users to upload or select an existing image in either PNG or JPEG format from the WordPress Media Library. The image preview should be displayed on the same screen. You can use third-party libraries such as CMB2 to achieve this. 2. Prevent users from deleting any images from the WordPress Media Library in the following scenarios, a. Prevent the deletion of an image if it is being used as a Featured Image [3] in an article. b. Prevent the deletion of an image if it is being used in the content of a post [4] (Post Body). c. Prevent the deletion of an image if it is being used in a Term Edit Page (as implemented in point 1). d. If the image is being used in any of these places and the user attempts to delete it, display a message asking the user to remove the image from the post(s) or term(s) where it is being used. The interface should display IDs of the post(s) or term(s) with an edit link [5]. The user should be able to determine whether the given ID is for a post or for a term. This message should appear in every place in WordPress from where images can be deleted, such as the Media List table [6], Media Library Popup [5] etc. e. In the Media Library Table, add a column named "Attached Objects" [6] that displays a comma-separated list of IDs (linked to the corresponding edit page). The user should be able to determine whether the given ID is for a post or for a term.
3. Provide following functionalities via REST API under `/assignment/v1/` namespace. a. An API endpoint that returns details of a given image ID i. Response should contain a JSON object with the following details 1. ID 2. Date 3. Slug 4. Type ( JPEG, PNG ) 5. Link 6. Alt text 7. Attached Objects Should contain Post or Term IDs to which the given image is attached. Structure the field structure accordingly. b. An API endpoint that deletes a given image if it is not attached to any of the posts or terms. If the given image is attached to any of the posts or terms then return a response stating that deletion failed. 4. Add unit tests for all critical functions.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `virtasant-safe-media.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= This is home Assigmnment =

== Screenshots ==


== Changelog ==

= 1.0 =

