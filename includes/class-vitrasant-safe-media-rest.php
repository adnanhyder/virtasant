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
class Virtasant_Safe_Media_Rest {

	/**
	 * Also maintains the safe_media class data thorough out the class
	 * version of the plugin
	 *
	 * @private  safe_media storing Admin class
	 * @var  $safe_media
	 * @since    1.0.0
	 * */
	private $safe_media;

	/**
	 * Define the core functionality of the class.
	 *
	 * Get the plugin name and the plugin version that can be used throughout the plugin.
	 * set namespace etc.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'prefix_register_vitrasant_rest_routes' ) );
		$this->namespace  = 'assignment/v1';
		$params           = new Virtasant_Safe_Media();
		$safe_media       = new Virtasant_Safe_Media_Admin( $params->get_plugin_name(), $params->get_version() );
		$this->safe_media = $safe_media;
	}


	/**
	 * Register rest our routes.
	 */
	public function register_routes() {
		// fetch image rest method.
		register_rest_route(
			$this->namespace,
			'/getImage',
			array(
				// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
				array(
					'methods'             => 'Post',
					'callback'            => array( $this, 'get_image' ),
					'permission_callback' => '__return_true',
				),
			)
		);
		// delete image rest method.
		register_rest_route(
			$this->namespace,
			'/deleteImage',
			array(
				// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
				array(
					'methods'             => 'Delete',
					'callback'            => array( $this, 'delete_image' ),
					'permission_callback' => '__return_true',
				),
			)
		);

	}


	/**
	 * This function used to get image detail by id in rest api
	 *
	 * @param array $request got in json from in rest post method.
	 * @return array response
	 * @since    1.0.0
	 */
	public function get_image( $request ) {
		$request_body   = $request->get_body();
		$sting_to_array = json_decode( $request_body );
		$image_id       = intval( $sting_to_array->post_id );
		$is_attachment  = wp_get_attachment_image_src( $image_id );
		if ( ! empty( $is_attachment ) ) {
			$post_info      = get_post( $image_id );
			$post_meta_info = wp_get_attachment_metadata( $image_id );
			$image_alt      = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			$featured       = $this->safe_media->vitrasant_prevent_featured_image_deletion( $image_id, 1 );
			$content        = $this->safe_media->vitrasant_prevent_content_image_deletion( $image_id, 1 );
			$term           = $this->safe_media->vitrasant_prevent_term_image_deletion( $image_id, 1 );

			if ( empty( $featured ) ) {
				$featured = array();
			}
			if ( empty( $content ) ) {
				$content = array();
			}
			if ( empty( $term ) ) {
				$term = array();
			}
			$data_array = array(
				'id'               => $post_info->ID,
				'date'             => $post_info->post_date,
				'date_gmt'         => $post_info->post_date_gmt,
				'slug'             => $post_info->guid,
				'type'             => $post_info->post_mime_type,
				'link'             => $post_info->guid,
				'alt_text'         => $image_alt,
				'attached_objects' => array(
					'post' => array(
						'featured' => implode( ',', $featured ),
						'content'  => implode( ',', $content ),
					),
					'term' => implode( ',', $term ),
				),
				'meta_data'        => array(
					'meta_obj' => $post_meta_info,
					'post_obj' => $post_info,
				),
			);
		} else {
			$data_array = array(
				'msg' => null,
			);
		}

		$response['code'] = 200;
		$response['data'] = $data_array;
		return $response;

	}

	/**
	 * This function used to delete image by id in rest api
	 *
	 * @param array $request got in json from in rest Delete method.
	 * @return array response
	 * @since    1.0.0
	 */
	public function delete_image( $request ) {
		$request_body   = $request->get_body();
		$sting_to_array = json_decode( $request_body );
		$image_id       = intval( $sting_to_array->post_id );
		$is_attachment  = wp_get_attachment_image_src( $image_id );
		$data_array     = array(
			'msg' => null,
		);
		if ( ! empty( $is_attachment ) ) {
			$msg = $this->safe_media->vitrasant_get_response_message( $image_id, 1 );
			if ( ! empty( $msg ) ) {
				$data_array = array(
					'msg' => $this->safe_media->error_message . $msg,
				);
			} else {
				$obj         = wp_delete_attachment( $image_id, true );
				$success_msg = __( 'Deletion Success {id} ', 'virtasant-safe-media' );
				$data_array  = array(
					'msg'         => $success_msg . $image_id,
					'deleted_obj' => $obj,
				);
			}
		}
		$response['code'] = 200;
		$response['data'] = $data_array;
		return $response;
	}


	/**
	 * Function to register our new routes from the controller.
	 *
	 * @return void
	 */
	public function prefix_register_vitrasant_rest_routes() {
		$controller = new Virtasant_Safe_Media_Rest();
		$controller->register_routes();
	}

}

$activate = new Virtasant_Safe_Media_Rest();


