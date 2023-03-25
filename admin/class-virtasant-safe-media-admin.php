<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/adnanhyder/
 * @since      1.0.0
 *
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/admin
 * @author     Adnan <12345adnan@gmail.com>
 */
class Virtasant_Safe_Media_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Virtasant_Safe_Media_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Virtasant_Safe_Media_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/virtasant-safe-media-admin.css', [], $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Virtasant_Safe_Media_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Virtasant_Safe_Media_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/virtasant-safe-media-admin.js');
        $translation_array = [
            'confirm_error' => __('You are about to permanently delete these items from your site.
This action cannot be undone.
\'Cancel\' to stop, \'OK\' to delete', 'virtasant-safe-media'),
        ];
        wp_localize_script($this->plugin_name, 'error_message', $translation_array);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/virtasant-safe-media-admin.js', ['jquery'], $this->version, false);

    }

    /**
     * Adding CMB2 Term Image hook attached (cmb2_admin_init).
     *
     * @since    1.0.0
     */

    public function vitrasant_edit_term_fields()
    {
        $prefix = 'vitrasant_';

        $cmb = new_cmb2_box([
            'id' => $prefix . 'metabox',
            'title' => esc_html__('Category Metabox', 'cmb2'),
            'object_types' => ['term'], // Only display on category pages
            'taxonomies' => ['category'], // Only display on category taxonomy
        ]);

        $cmb->add_field([
            'name' => esc_html__('Custom Field', 'cmb2'),
            'id' => $prefix . 'upload_image',
            'type' => 'file',
            'desc' => esc_html__('Enter your custom field description', 'cmb2'),
        ]);

    }


    /**
     * Disable media deletion hook attached (delete_attachment).
     *
     * @since    1.0.0
     */

    public function vitrasant_disable_media_deletion($post_ID)
    {


        $this->vitrasant_prevent_featured_image_deletion($post_ID);
        $this->vitrasant_prevent_content_image_deletion($post_ID);
        $this->vitrasant_prevent_term_image_deletion($post_ID);

        wp_die(__('Main You cannot delete this image because it is being used as a in an article.', 'virtasant-safe-media'));

    }


    /**
     * Disable media deletion Featured Image Check.
     *
     * @return  null or error
     * @since    1.0.0
     */
    public function vitrasant_prevent_featured_image_deletion($post_ID)
    {
        $featured_image_query = new WP_Query(array(
            'post_type' => 'post',
            'meta_key' => '_thumbnail_id',
            'meta_value' => $post_ID
        ));
        $post_url = [];
        if ($featured_image_query->have_posts()) {
            while ($featured_image_query->have_posts()) {
                $featured_image_query->the_post();
                $id = get_the_ID();
                $post_url[$id] = add_query_arg([
                    'post' => $id,
                    'action' => 'edit',
                ], admin_url('post.php'));
            }
            $comma_separated = "";
            if (!empty($post_url)) {
                foreach ($post_url as $key => $single) {
                    $comma_separated .= "<a href='$single'>$key</a> ";
                }
            }
            wp_die(__('This image cannot be deleted because it is being used as a featured image. ' . $comma_separated, 'virtasant-safe-media'));
        }
    }

    /**
     * Disable media deletion Content Image Check.
     *
     * @return  null or error
     * @since    1.0.0
     */

    public function vitrasant_prevent_content_image_deletion($post_ID)
    {
        $post_url = get_post($post_ID);
        $url = $post_url->guid;

        $posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish', //not using 'any' (trash not included)
            'numberposts' => -1, //All post
        ]);
        $post_url = [];
        foreach ($posts as $post) {
            $content = $post->post_content;
            if (strpos($content, $url) !== false) { //PHP 8.0 supported
                $id = $post->ID;
                $post_url[$id] = add_query_arg([
                    'post' => $id,
                    'action' => 'edit',
                ], admin_url('post.php'));
            }
        }
        $comma_separated = "";
        if (!empty($post_url)) {
            foreach ($post_url as $key => $single) {
                $comma_separated .= "<a href='$single'>$key</a> ";
            }

            wp_die(__('You cannot delete this image because it is being used in the content of a post. ' . $comma_separated, 'virtasant-safe-media'));
        }
    }

    /**
     * Disable media deletion Term Image Check.
     *
     * @return  null or error
     * @since    1.0.0
     */

    public function vitrasant_prevent_term_image_deletion($post_ID)
    {

        $args = array(
            'taxonomy' => 'category',
            'hide_empty' => false,
            'meta_key' => 'vitrasant_upload_image_id',
            'meta_value' => $post_ID,
        );
        $terms = get_terms($args);
        foreach ($terms as $term) {
            $id = $term->term_id;
            $post_url[$id] = get_edit_term_link($id);
        }
        $comma_separated = "";
        if (!empty($post_url)) {
            foreach ($post_url as $key => $single) {
                $comma_separated .= "<a href='$single'>$key</a> ";
            }
            $error_message = __('You cannot delete this image because it is being used in the Term Edit Page. ' . $comma_separated, 'virtasant-safe-media');


            if (!function_exists('is_ajax') || !is_ajax()) {
                echo $error_message;
            } else {
                wp_die($error_message);
            }

        }
    }

    /**
     * Add media columns.
     *
     * @return  null or error
     * @since    1.0.0
     */
    public function vitrasant_custom_media_columns($columns)
    {

        unset($columns['cb']);
        unset($columns['title']);
        unset($columns['author']);
        unset($columns['comments']);
        unset($columns['parent']);
        unset($columns['date']);


        $columns['cb'] = __('cb', 'text-domain');
        $columns['title'] = __('Title', 'text-domain');
        $columns['author'] = __('Author', 'text-domain');
        $columns['parent'] = __('Uploaded To', 'text-domain');
        $columns['comments'] = __('<i class="fa comment-grey-bubble" aria-hidden="true"></i>', 'text-domain');
        $columns['attached_objects'] = __('Attached Objects', 'text-domain');
        $columns['date'] = __('Date', 'text-domain');
        return $columns;


    }


    /**
     * Add media columns Content.
     *
     * @return  null or error
     * @since    1.0.0
     */
    public function vitrasant_custom_media_columns_content($column_name, $attachment_id)
    {
        if ('attached_objects' == $column_name) {
            print_r($attachment_id);
            $attached_objects = get_attached_media('image', $attachment_id);
            echo count($attached_objects);
        }
    }


    /**
     * Unfortunately, there is no standardized best practice instituted by WordPress for hooking into Backbone templates.
     * There have been plans suggested bringing the familiar filters and actions API to Javascript in WordPress, but there is a lack of traction in this movement.
     * https://github.com/WordPress/WordPress/blob/master/wp-includes/media-template.php#L119
     * Override the "Attachments Details Two Column" Backbone micro template in WordPress 4.0
     *
     * @see https://stackoverflow.com/a/25948448/2078474
     */


    public function vitrasant_modified_attachments_details_two_column_template()
    {
        $alt_text_description = sprintf(
        /* translators: 1: Link to tutorial, 2: Additional link attributes, 3: Accessibility text. */
            __('<a href="%1$s" %2$s>Learn how to describe the purpose of the image%3$s</a>. Leave empty if the image is purely decorative.'),
            esc_url('https://www.w3.org/WAI/tutorials/images/decision-tree'),
            'target="_blank" rel="noopener"',
            sprintf(
                '<span class="screen-reader-text"> %s</span>',
                /* translators: Accessibility text. */
                __('(opens in a new tab)')
            )
        );
        ?>
        <script type="text/html" id="tmpl-attachment-details-two-column-custom">
            <div class="attachment-media-view {{ data.orientation }}">
                <div class="thumbnail thumbnail-{{ data.type }}">
                    <# if ( data.uploading ) { #>
                    <div class="media-progress-bar">
                        <div></div>
                    </div>
                    <# } else if ( 'image' === data.type && data.sizes && data.sizes.large ) { #>
                    <img class="details-image" src="{{ data.sizes.large.url }}" draggable="false"/>
                    <# } else if ( 'image' === data.type && data.sizes && data.sizes.full ) { #>
                    <img class="details-image" src="{{ data.sizes.full.url }}" draggable="false"/>
                    <# } else if ( -1 === jQuery.inArray( data.type, [ 'audio', 'video' ] ) ) { #>
                    <img class="details-image" src="{{ data.icon }}" class="icon" draggable="false"/>
                    <# } #>

                    <# if ( 'audio' === data.type ) { #>
                    <div class="wp-media-wrapper">
                        <audio style="visibility: hidden" controls class="wp-audio-shortcode" width="100%"
                               preload="none">
                            <source type="{{ data.mime }}" src="{{ data.url }}"/>
                        </audio>
                    </div>
                    <# } else if ( 'video' === data.type ) {
                    var w_rule = h_rule = '';
                    if ( data.width ) {
                    w_rule = 'width: ' + data.width + 'px;';
                    } else if ( wp.media.view.settings.contentWidth ) {
                    w_rule = 'width: ' + wp.media.view.settings.contentWidth + 'px;';
                    }
                    if ( data.height ) {
                    h_rule = 'height: ' + data.height + 'px;';
                    }
                    #>
                    <div style="{{ w_rule }}{{ h_rule }}" class="wp-media-wrapper wp-video">
                        <video controls="controls" class="wp-video-shortcode" preload="metadata"
                        <# if ( data.width ) { #>width="{{ data.width }}"<# } #>
                        <# if ( data.height ) { #>height="{{ data.height }}"<# } #>
                        <# if ( data.image && data.image.src !== data.icon ) { #>poster="{{ data.image.src }}"<# } #>>
                        <source type="{{ data.mime }}" src="{{ data.url }}"/>
                        </video>
                    </div>
                    <# } #>

                    <div class="attachment-actions">
                        <# if ( 'image' === data.type && ! data.uploading && data.sizes && data.can.save ) { #>
                        <a class="button edit-attachment" href="#"><?php _e('Edit Image'); ?></a>
                        <# } #>
                    </div>
                </div>
            </div>
            <div class="attachment-info">
			<span class="settings-save-status" role="status">
				<span class="spinner"></span>
				<span class="saved"><?php esc_html_e('Saved.'); ?></span>
			</span>
                <div class="details">
                    <h2 class="screen-reader-text"><?php _e('Details'); ?></h2>
                    <div class="uploaded"><strong><?php _e('Uploaded on:'); ?></strong> {{ data.dateFormatted }}</div>
                    <div class="uploaded-by">
                        <strong><?php _e('Uploaded by:'); ?></strong>
                        <# if ( data.authorLink ) { #>
                        <a href="{{ data.authorLink }}">{{ data.authorName }}</a>
                        <# } else { #>
                        {{ data.authorName }}
                        <# } #>
                    </div>
                    <# if ( data.uploadedToTitle ) { #>
                    <div class="uploaded-to">
                        <strong><?php _e('Uploaded to:'); ?></strong>
                        <# if ( data.uploadedToLink ) { #>
                        <a href="{{ data.uploadedToLink }}">{{ data.uploadedToTitle }}</a>
                        <# } else { #>
                        {{ data.uploadedToTitle }}
                        <# } #>
                    </div>
                    <# } #>
                    <div class="filename"><strong><?php _e('File name:'); ?></strong> {{ data.filename }}</div>
                    <div class="file-type"><strong><?php _e('File type:'); ?></strong> {{ data.mime }}</div>
                    <div class="file-size"><strong><?php _e('File size:'); ?></strong> {{ data.filesizeHumanReadable }}
                    </div>
                    <# if ( 'image' === data.type && ! data.uploading ) { #>
                    <# if ( data.width && data.height ) { #>
                    <div class="dimensions"><strong><?php _e('Dimensions:'); ?></strong>
                        <?php
                        /* translators: 1: A number of pixels wide, 2: A number of pixels tall. */
                        printf(__('%1$s by %2$s pixels'), '{{ data.width }}', '{{ data.height }}');
                        ?>
                    </div>
                    <# } #>

                    <# if ( data.originalImageURL && data.originalImageName ) { #>
                    <div class="word-wrap-break-word">
                        <?php _e('Original image:'); ?>
                        <a href="{{ data.originalImageURL }}">{{data.originalImageName}}</a>
                    </div>
                    <# } #>
                    <# } #>

                    <# if ( data.fileLength && data.fileLengthHumanReadable ) { #>
                    <div class="file-length"><strong><?php _e('Length:'); ?></strong>
                        <span aria-hidden="true">{{ data.fileLength }}</span>
                        <span class="screen-reader-text">{{ data.fileLengthHumanReadable }}</span>
                    </div>
                    <# } #>

                    <# if ( 'audio' === data.type && data.meta.bitrate ) { #>
                    <div class="bitrate">
                        <strong><?php _e('Bitrate:'); ?></strong> {{ Math.round( data.meta.bitrate / 1000 ) }}kb/s
                        <# if ( data.meta.bitrate_mode ) { #>
                        {{ ' ' + data.meta.bitrate_mode.toUpperCase() }}
                        <# } #>
                    </div>
                    <# } #>

                    <# if ( data.mediaStates ) { #>
                    <div class="media-states"><strong><?php _e('Used as:'); ?></strong> {{ data.mediaStates }}</div>
                    <# } #>

                    <div class="compat-meta">
                        <# if ( data.compat && data.compat.meta ) { #>
                        {{{ data.compat.meta }}}
                        <# } #>
                    </div>
                </div>

                <div class="settings">
                    <# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
                    <# if ( 'image' === data.type ) { #>
                    <span class="setting alt-text has-description" data-setting="alt">
						<label for="attachment-details-two-column-alt-text"
                               class="name"><?php _e('Alternative Text'); ?></label>
						<textarea id="attachment-details-two-column-alt-text" aria-describedby="alt-text-description" {{
                                  maybeReadOnly }}>{{ data.alt }}</textarea>
					</span>
                    <p class="description" id="alt-text-description"><?php echo $alt_text_description; ?></p>
                    <# } #>
                    <?php if (post_type_supports('attachment', 'title')) : ?>
                        <span class="setting" data-setting="title">
					<label for="attachment-details-two-column-title" class="name"><?php _e('Title'); ?></label>
					<input type="text" id="attachment-details-two-column-title" value="{{ data.title }}" {{
                           maybeReadOnly }}/>
				</span>
                    <?php endif; ?>
                    <# if ( 'audio' === data.type ) { #>
                    <?php
                    foreach (array(
                                 'artist' => __('Artist'),
                                 'album' => __('Album'),
                             ) as $key => $label) :
                        ?>
                        <span class="setting" data-setting="<?php echo esc_attr($key); ?>">
					<label for="attachment-details-two-column-<?php echo esc_attr($key); ?>"
                           class="name"><?php echo $label; ?></label>
					<input type="text" id="attachment-details-two-column-<?php echo esc_attr($key); ?>"
                           value="{{ data.<?php echo $key; ?> || data.meta.<?php echo $key; ?> || '' }}"/>
				</span>
                    <?php endforeach; ?>
                    <# } #>
                    <span class="setting" data-setting="caption">
					<label for="attachment-details-two-column-caption" class="name"><?php _e('Caption'); ?></label>
					<textarea id="attachment-details-two-column-caption" {{ maybeReadOnly
                              }}>{{ data.caption }}</textarea>
				</span>
                    <span class="setting" data-setting="description">
					<label for="attachment-details-two-column-description"
                           class="name"><?php _e('Description'); ?></label>
					<textarea id="attachment-details-two-column-description" {{ maybeReadOnly
                              }}>{{ data.description }}</textarea>
				</span>
                    <span class="setting" data-setting="url">
					<label for="attachment-details-two-column-copy-link" class="name"><?php _e('File URL:'); ?></label>
					<input type="text" class="attachment-details-copy-link" id="attachment-details-two-column-copy-link"
                           value="{{ data.url }}" readonly/>
					<span class="copy-to-clipboard-container">
						<button type="button" class="button button-small copy-attachment-url"
                                data-clipboard-target="#attachment-details-two-column-copy-link"><?php _e('Copy URL to clipboard'); ?></button>
						<span class="success hidden" aria-hidden="true"><?php _e('Copied!'); ?></span>
					</span>
				</span>
                    <div class="attachment-compat"></div>
                </div>

                <div class="actions">
                    <# if ( data.link ) { #>
                    <a class="view-attachment" href="{{ data.link }}"><?php _e('View attachment page'); ?></a>
                    <# } #>
                    <# if ( data.can.save ) { #>
                    <# if ( data.link ) { #>
                    <span class="links-separator">|</span>
                    <# } #>
                    <a href="{{ data.editLink }}"><?php _e('Edit more details'); ?></a>
                    <# } #>
                    <# if ( ! data.uploading && data.can.remove ) { #>
                    <# if ( data.link || data.can.save ) { #>
                    <span class="links-separator">|</span>
                    <# } #>
                    <?php if (MEDIA_TRASH) : ?>
                        <# if ( 'trash' === data.status ) { #>
                        <button type="button"
                                class="button-link untrash-attachment"><?php _e('Restore from Trash'); ?></button>
                        <# } else { #>
                        <button type="button"
                                class="button-link trash-attachment"><?php _e('Move to Trash'); ?></button>
                        <# } #>
                    <?php else : ?>
                        <button type="button"
                                class="button-link delete-attachment-custom"
                                data-attachment-id="{{data.id}}"><?php _e('Delete permanently'); ?></button>
                    <?php endif; ?>
                    <# } #>
                </div>
            </div>

        </script>
        <script>
            jQuery(document).ready(function ($) {
                if (typeof wp.media.view.Attachment.Details.TwoColumn != 'undefined') {
                    wp.media.view.Attachment.Details.TwoColumn.prototype.template = wp.template('attachment-details-two-column-custom');
                }
            });
        </script>
        <?php
    }


    public function vitrasant_delete_handler()
    {
        $post_id = $_POST['post_id'];

        // Do something with the post ID
        $result = "Post ID $post_id processed.";

        echo $result;

        wp_die();
    }


}
