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
class Virtasant_Safe_Media_Admin {



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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $error_message The default error of images.
	 */
	public $error_message;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name   = $plugin_name;
		$this->version       = $version;
		$this->error_message = __( 'Deletion Failed because Image is linked to post(s) or term(s) remove the image from the post(s) or term(s) where it is being used to continue delete. ', 'virtasant-safe-media' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/virtasant-safe-media-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/virtasant-safe-media-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'ajax_var',
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'ajax-nonce' ),
			)
		);
	}

	/**
	 * Adding CMB2 Term Image hook attached (cmb2_admin_init).
	 *
	 * @since    1.0.0
	 */
	public function vitrasant_edit_term_fields() {
		$prefix = 'vitrasant_';

		$cmb = new_cmb2_box(
			array(
				'id'           => $prefix . 'metabox',
				'title'        => esc_html__( 'Category Metabox', 'cmb2' ),
				'object_types' => array( 'term' ), // Only display on category pages.
				'taxonomies'   => array( 'category' ), // Only display on category taxonomy.
			)
		);

		$cmb->add_field(
			array(
				'name' => esc_html__( 'Category Image', 'cmb2' ),
				'id'   => $prefix . 'upload_image',
				'type' => 'file',

			)
		);

	}


	/**
	 * Function vitrasant_disable_media_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @since 1.0.0
	 */
	public function vitrasant_disable_media_deletion( $post_ID ) {
		$msg = $this->vitrasant_get_response_message( $post_ID, 0 );
		if ( ! empty( $msg ) ) {
			$error_string = $this->error_message . $msg; // error string already escaped.
            wp_die($error_string); // phpcs:ignore
		}

	}


	/**
	 * Function vitrasant_prevent_featured_image_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function vitrasant_prevent_featured_image_deletion( $post_ID, $ajax_res = 0 ) {
		$featured_image_query = new WP_Query(
			array(
				'post_type'  => 'post',
				'meta_key'   => '_thumbnail_id', // phpcs:ignore
				'meta_value' => $post_ID, // phpcs:ignore
			)
		);
		$post_url             = array();
		if ( $featured_image_query->have_posts() ) {
			while ( $featured_image_query->have_posts() ) {
				$featured_image_query->the_post();
				$id = get_the_ID();
				if ( 1 === $ajax_res ) {
					$post_url[] = $id;
				} else {
					$post_url[ $id ] = add_query_arg(
						array(
							'post'   => $id,
							'action' => 'edit',
						),
						admin_url( 'post.php' )
					);
				}
			}
			$comma_separated = array();
			if ( ! empty( $post_url ) ) {
				foreach ( $post_url as $key => $single ) {
					$single            = esc_url( $single );
					$key               = esc_html( $key );
					$comma_separated[] = "<a href='$single'>$key</a>";
				}
			}
			if ( 1 === $ajax_res ) {
				return $post_url;
			} else {
				return implode( ',', $comma_separated );
			}
		}
	}

	/**
	 * Function vitrasant_prevent_content_image_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function vitrasant_prevent_content_image_deletion( $post_ID, $ajax_res = 0 ) {
		$post_info = get_post( $post_ID );
		if ( ! empty( $post_info ) ) {
			$url      = $post_info->guid;
			$posts    = get_posts(
				array(
					'post_type'   => 'post',
					'post_status' => 'publish', // not using 'any' (trash not included).
					'numberposts' => -1, // All post.
				)
			);
			$post_url = array();
			foreach ( $posts as $post ) {
				$content = $post->post_content;
				if ( strpos( $content, $url ) !== false ) { // Below PHP 8.0 supported.
					$id = $post->ID;
					if ( 1 === $ajax_res ) {
						$post_url[] = $id;
					} else {
						$post_url[ $id ] = add_query_arg(
							array(
								'post'   => $id,
								'action' => 'edit',
							),
							admin_url( 'post.php' )
						);
					}
				}
			}
			return $this->generate_url_links( $post_url, $ajax_res );
		} else {
			return '';
		}
	}

	/**
	 * Function vitrasant_prevent_term_image_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function vitrasant_prevent_term_image_deletion( $post_ID, $ajax_res = 0 ) {
		$args     = array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'meta_key'   => 'vitrasant_upload_image_id', // phpcs:ignore
			'meta_value' => $post_ID, // phpcs:ignore
		);
		$terms    = get_terms( $args );
		$post_url = array();
		foreach ( $terms as $term ) {
			$id = $term->term_id;
			if ( 1 === $ajax_res ) {
				$post_url[] = $id;
			} else {
				$post_url[ $id ] = get_edit_term_link( $id );
			}
		}
		return $this->generate_url_links( $post_url, $ajax_res );
	}

	/**
	 * Function vitrasant_custom_media_columns Add media columns on libarary page.
	 *
	 * @param array $columns all the media library columns.
	 * @return array $columns
	 * @since    1.0.0
	 */
	public function vitrasant_custom_media_columns( $columns ) {
		unset( $columns['cb'] );
		unset( $columns['title'] );
		unset( $columns['author'] );
		unset( $columns['comments'] );
		unset( $columns['parent'] );
		unset( $columns['date'] );

		$columns['cb']               = __( 'cb', 'virtasant-safe-media' );
		$columns['title']            = __( 'Title', 'virtasant-safe-media' );
		$columns['author']           = __( 'Author', 'virtasant-safe-media' );
		$columns['parent']           = __( 'Uploaded To', 'virtasant-safe-media' );
		$columns['comments']         = __( '<i class="fa comment-grey-bubble" aria-hidden="true"></i>', 'virtasant-safe-media' );
		$columns['attached_objects'] = __( 'Attached Objects', 'virtasant-safe-media' );
		$columns['date']             = __( 'Date', 'virtasant-safe-media' );
		return $columns;

	}


	/**
	 * Function vitrasant_custom_media_columns Add media columns.
	 *
	 * @param array $column_name current column of the media library.
	 * @param int   $attachment_id of the post.
	 * @return void $columns
	 * @since    1.0.0
	 */
	public function vitrasant_custom_media_columns_content( $column_name, $attachment_id ) {
		if ( 'attached_objects' === $column_name ) {

			$post_id = intval( $attachment_id );
			$result  = $this->virtasant_linked_articles( $post_id ); // $result is already escaped during creation
			echo implode( ', ', $result ); // phpcs:ignore
		}
	}


	/**
	 * Function vitrasant_add_custom_attachment_action_field Add media columns.
	 *
	 * @param array  $form_fields current form fields column of the media library.
	 * @param object $post current post.
	 * @return array $form_fields
	 * @since    1.0.0
	 */
	public function vitrasant_add_custom_attachment_action_field( $form_fields, $post ) {
		$post_id           = $post->ID;
		$result            = $this->virtasant_linked_articles( $post_id );
		$string            = implode( ', ', $result );
		$form_fields['id'] = array(
			'label' => __( 'Linked Articles', 'virtasant-safe-media' ),
			'input' => 'html',
			'html'  => ' <label><span>' . $string . '</span></label>',
		);

		return $form_fields;
	}

	/**
	 * Add media columns Content.
	 *
	 * @return  string|void handler
	 * @since    1.0.0
	 */
	public function vitrasant_delete_handler() {
		check_ajax_referer( 'ajax-nonce', 'security' );
		if ( isset( $_POST['post_id'] ) ) {

			$post_ID = intval( $_POST['post_id'] );

			$msg = $this->vitrasant_get_response_message( $post_ID, 1 );

			if ( ! empty( $msg ) ) {
				$result = array(
					'code' => 0,
					'msg'  => $this->error_message . $msg,
				);
				wp_send_json( $result );
			}
		}

	}

	/**
	 * Function vitrasant_prevent_term_image_deletion Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param int $post_ID for fetching post data.
	 * @return array $result all the ids.
	 * @since 1.0.0
	 */
	public function virtasant_linked_articles( $post_ID ) {
		$final_array    = array();
		$featured_image = $this->vitrasant_prevent_featured_image_deletion( $post_ID, 0 );
		$final_array[]  = $featured_image;
		$content_image  = $this->vitrasant_prevent_content_image_deletion( $post_ID, 0 );
		$final_array[]  = $content_image;
		$term_image     = $this->vitrasant_prevent_term_image_deletion( $post_ID, 0 );
		$final_array[]  = $term_image;
		$remove_empty   = array_filter( $final_array );
		$comma_sep      = implode( ',', $remove_empty );
		$merge_all      = explode( ',', $comma_sep );
		$result         = array();
		foreach ( $merge_all as $key => $value ) {
			if ( ! in_array( $value, $result, true ) ) {
				$result[ $key ] = $value;
			}
		}
		return $result;
	}

	/**
	 * Function vitrasant_get_response_message Disable media deletion hook attached (delete_attachment) .
	 *
	 * @param string $post_ID id of the attachment.
	 * @param int    $ajax_res show where ajax request or not.
	 * @since 1.0.0
	 */
	public function vitrasant_get_response_message( $post_ID, $ajax_res = 0 ) {
		$msg            = '';
		$featured_image = $this->vitrasant_prevent_featured_image_deletion( $post_ID, $ajax_res );
		if ( ! empty( $featured_image ) ) {
			if ( 1 === $ajax_res ) {
				$featured_image = implode( ',', $featured_image );
			}
			$msg .= __( 'It is being used as a featured image. {id}  ', 'virtasant-safe-media' ) . $featured_image . ' ';
		}
		$content_image = $this->vitrasant_prevent_content_image_deletion( $post_ID, $ajax_res );

		if ( ! empty( $content_image ) ) {
			if ( 1 === $ajax_res ) {
				$content_image = implode( ',', $content_image );
			}
			$msg .= __( 'It is being used in the content of a post. {id} ', 'virtasant-safe-media' ) . $content_image . ' ';
		}

		$term_image = $this->vitrasant_prevent_term_image_deletion( $post_ID, $ajax_res );

		if ( ! empty( $term_image ) ) {
			if ( 1 === $ajax_res ) {
				$term_image = implode( ',', $term_image );
			}
			$msg .= __( 'It is being used in the Term Edit Page. {id} ', 'virtasant-safe-media' ) . $term_image . ' ';
		}
		return $msg;
	}

	/**
	 * Function generate_url_links escaped url links for admin panel (delete_attachment) .
	 *
	 * @param array $post_url containing all links.
	 * @param int   $ajax_res if its ajax request or not.
	 * @return array|string|void
	 */
	public function generate_url_links( array $post_url, int $ajax_res ) {
		$comma_separated = array();
		if ( ! empty( $post_url ) ) {
			foreach ( $post_url as $key => $single ) {
				$single            = esc_url( $single );
				$key               = esc_html( $key );
				$comma_separated[] = "<a href='$single'>$key</a>";
			}
			if ( 1 === $ajax_res ) {
				return $post_url;
			} else {
				return implode( ',', $comma_separated );
			}
		}
	}


}
