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
 * @package    Virtasant_Safe_Media
 * @subpackage Virtasant_Safe_Media/includes
 * @author     Adnan <12345adnan@gmail.com>
 */
class Virtasant_Safe_Media_Posts_Controller
{
    // Here initialize our namespace
    public function __construct()
    {

        add_action('rest_api_init', [$this, "prefix_register_vitrasant_rest_routes"]);
        $this->namespace = 'assignment/v1';

    }

    // Register our routes.
    public function register_routes()
    {

        register_rest_route($this->namespace,  '/getImage', [
            // Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
            [
                'methods' => 'Post',
                'callback' => [$this, 'getImage'],
            ],
        ]);

    }

    public function getImage($request)
    {
        $request_body = $request->get_body();
        $sting_to_array = json_decode($request_body);
        $imageID = $sting_to_array->id;

        $data_array = $imageID;

        $response = [];
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


