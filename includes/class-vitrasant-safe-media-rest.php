<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://profiles.wordpress.org/adnanhyder/
 * @since      1.0.0
 *
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Virtasant_Safe_Media_Rest
 * @subpackage Virtasant_Safe_Media/includes
 * @author     Adnan <12345adnan@gmail.com>
 */
class Virtasant_Safe_Media_Posts_Controller
{

    private $safe_media;

    // Here initialize our namespace
    public function __construct()
    {

        add_action('rest_api_init', [$this, "prefix_register_vitrasant_rest_routes"]);
        $this->namespace = 'assignment/v1';
        $params = new Virtasant_Safe_Media();
        $safe_media = new Virtasant_Safe_Media_Admin($params->get_plugin_name(), $params->get_version());
        $this->safe_media = $safe_media;
    }


    // Register our routes.
    public function register_routes()
    {
        //fetch image rest method
        register_rest_route($this->namespace, '/getImage', [
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            [
                'methods' => 'Post',
                'callback' => [$this, 'getImage'],
            ],
        ]);
        //delete image rest method
        register_rest_route($this->namespace, '/deleteImage', [
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            [
                'methods' => 'Delete',
                'callback' => [$this, 'deleteImage'],
            ],
        ]);

    }


    /**
     * @param mixed $request
     * @return array response
     * @since    1.0.0
     */
    public function getImage($request)
    {

        $request_body = $request->get_body();
        $sting_to_array = json_decode($request_body);
        $imageID = intval($sting_to_array->post_id);
        $post_info = get_post($imageID);
        $post_meta_info = wp_get_attachment_metadata($imageID);
        $image_alt = get_post_meta($imageID, '_wp_attachment_image_alt', true);
        $featured = $this->safe_media->vitrasant_prevent_featured_image_deletion($imageID, 1);
        $content = $this->safe_media->vitrasant_prevent_content_image_deletion($imageID, 1);

        $term = $this->safe_media->vitrasant_prevent_term_image_deletion($imageID, 1);

        if (empty($featured)) {
            $featured = [];
        }
        if (empty($content)) {
            $content = [];
        }
        if (empty($term)) {
            $term = [];
        }
        $data_array = [
            'id' => $post_info->ID,
            'date' => $post_info->post_date,
            'date_gmt' => $post_info->post_date_gmt,
            'slug' => $post_info->guid,
            'type' => $post_info->post_mime_type,
            'link' => $post_info->guid,
            'alt_text' => $image_alt,
            'attached_objects' => [
                'post' => [
                    'featured' => implode(',', $featured),
                    'content' => implode(',', $content)
                ],
                'term' => implode(',', $term),
            ],
            'meta_data' => [
                'meta_obj' => $post_meta_info,
                'post_obj' => $post_info,
            ]
        ];

        $response['code'] = 200;
        $response['data'] = $data_array;
        return $response;

    }

    /**
     * @param mixed $request
     * @return array response
     * @since    1.0.0
     */
    public function deleteImage($request)
    {
        $request_body = $request->get_body();
        $sting_to_array = json_decode($request_body);
        $imageID = intval($sting_to_array->post_id);
        $post_info = get_post($imageID);
        $data_array = [
            "msg" => null
        ];
        if (!empty($post_info)) {
            $featured = $this->safe_media->vitrasant_prevent_featured_image_deletion($imageID, 1);
            $content = $this->safe_media->vitrasant_prevent_content_image_deletion($imageID, 1);
            $term = $this->safe_media->vitrasant_prevent_term_image_deletion($imageID, 1);
            $msg = "";
            if (!empty($featured)) {
                $msg .= __("Featured image {id} ", "virtasant-safe-media") . implode(',', $featured) . " ";
            }
            if (!empty($content)) {
                $msg .= __("Post content image {id} ", "virtasant-safe-media") . implode(',', $content) . " ";
            }
            if (!empty($term)) {
                $msg .= __("Term image {id} ", "virtasant-safe-media") . implode(',', $term);
            }
            if (!empty($msg)) {
                $data_array = [
                    'msg' => __("Deletion Failed because Image is linked to " . $msg, "virtasant-safe-media"),
                ];
            } else {

                $obj = wp_delete_attachment( $imageID , true );

                $data_array = [
                    'msg' => __("Deletion Success {id} ". $imageID , "virtasant-safe-media"),
                    'deleted_obj' => $obj,
                ];
            }
        }
        $response['code'] = 200;
        $response['data'] = $data_array;
        return $response;
    }


    // Function to register our new routes from the controller.
    public function prefix_register_vitrasant_rest_routes()
    {
        $controller = new Virtasant_Safe_Media_Posts_Controller();
        $controller->register_routes();
    }

}

$activate = new Virtasant_Safe_Media_Posts_Controller();


