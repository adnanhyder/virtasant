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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/virtasant-safe-media-admin.css', array(), $this->version, 'all');

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/virtasant-safe-media-admin.js', array('jquery'), $this->version, false);

    }


    public function vitrasant_edit_term_fields($taxonomy)
    {
        ?>
        <div class="form-field">
            <tr class="form-field">
                <th>
                    <label for="vitrasant-upload"><?php _e('Picture of the category', ''); ?></label>
                </th>
                <td>
                    <input type="file" class="button vitrasant-upload" id="vitrasant-upload" value="Upload Image"
                           name="vitrasant_img"/>
                </td>
                <br>
            </tr>

        </div>
        <?php
    }

}