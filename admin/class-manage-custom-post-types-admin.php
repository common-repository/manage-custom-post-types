<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.elsner.com/
 * @since      1.0.0
 *
 * @package    Manage_Custom_Post_Types
 * @subpackage Manage_Custom_Post_Types/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Manage_Custom_Post_Types
 * @subpackage Manage_Custom_Post_Types/admin
 * @author     Aakif Kadiwala <aakifkadiwala1995@gmail.com>
 */
class Manage_Custom_Post_Types_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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
		 * defined in Manage_Custom_Post_Types_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Manage_Custom_Post_Types_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/manage-custom-post-types-admin.css', array(), $this->version, 'all' );

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
		 * defined in Manage_Custom_Post_Types_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Manage_Custom_Post_Types_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/manage-custom-post-types-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function mcpt_admin_menu(){
		$capability = apply_filters( 'mcpt_required_capabilities', 'manage_options' );
		$parent_slug = 'mcpt_main_menu';

		add_menu_page( __( 'Manage Custom Post Types', 'manage-custom-post-type-ui' ), __( 'Manage CPT', 'manage-custom-post-type' ), $capability, $parent_slug, 'mcpt_main_menu' );
		add_submenu_page( $parent_slug, __( 'Manage Custom Post Types', 'manage-custom-post-type' ), __( 'Manage Post Types', 'manage-custom-post-type' ), $capability, 'mcpt_manage_post_types', 'mcpt_manage_post_types' );
		add_submenu_page( $parent_slug, __( 'Add New Custom Post Types', 'manage-custom-post-type' ), __( 'Add New Post Types', 'manage-custom-post-type' ), $capability, 'mcpt_add_new_post_types', 'mcpt_add_new_post_types' );
		//add_submenu_page( $parent_slug, __( 'Add/Edit Taxonomies', 'manage-custom-post-type' ), __( 'Add/Edit Taxonomies', 'manage-custom-post-type' ), $capability, 'mcpt_manage_taxonomies', 'mcpt_manage_taxonomies' );
		//add_submenu_page( $parent_slug, __( 'Help/Support', 'manage-custom-post-type' ), __( 'Help/Support', 'manage-custom-post-type' ), $capability, 'mcpt_support', 'mcpt_support' );
		remove_submenu_page( $parent_slug, 'mcpt_main_menu');
	}


	public function mcpt_creation(){
		global $wpdb;

		$result=$wpdb->get_results("SELECT option_name FROM ".$wpdb->prefix."options WHERE option_name LIKE 'mcpt%' ORDER BY option_name ASC");
		$total_rec = $wpdb->num_rows;

		for($i=0; $i<$total_rec; $i++){
			$mcpt_temp[$i] = maybe_unserialize( get_option($result[$i]->option_name) );
			if($mcpt_temp[$i]['mcpt_status']==1){
				$mcpt_label[$i] = esc_html("label_".$mcpt_temp[$i]['mcpt_slug'], "manage-custom-post-types");
				$mcpt_args[$i] = esc_html("args_".$mcpt_temp[$i]['mcpt_slug'], "manage-custom-post-types");
				$mcpt_slug = esc_html($mcpt_temp[$i]['mcpt_slug'], "manage-custom-post-types");
				$mcpt_name = esc_html($mcpt_temp[$i]['mcpt_name'], "manage-custom-post-types");
				$mcpt_icon = esc_html($mcpt_temp[$i]['mcpt_icon'], "manage-custom-post-types");

				$mcpt_label[$i] = array(
					'name'                => _x( $mcpt_name, 'Post Type General Name', 'manage-custom-post-type' ),
					'singular_name'       => _x( $mcpt_name, 'Post Type Singular Name', 'manage-custom-post-type' ),
					'menu_name'           => __( $mcpt_name, 'manage-custom-post-type' ),
					'parent_item_colon'   => __( 'Parent Item:', 'manage-custom-post-type' ),
					'all_items'           => __( 'All Items', 'manage-custom-post-type' ),
					'view_item'           => __( 'View Item', 'manage-custom-post-type' ),
					'add_new_item'        => __( 'Add New Item', 'manage-custom-post-type' ),
					'add_new'             => __( 'Add New', 'manage-custom-post-type' ),
					'edit_item'           => __( 'Edit Item', 'manage-custom-post-type' ),
					'update_item'         => __( 'Update Item', 'manage-custom-post-type' ),
					'search_items'        => __( 'Search Item', 'manage-custom-post-type' ),
					'not_found'           => __( 'Not found', 'manage-custom-post-type' ),
					'not_found_in_trash'  => __( 'Not found in Trash', 'manage-custom-post-type' ),
				);
				
				
				
				$mcpt_args[$i] = array(
					'label'               => __( $mcpt_name, 'manage-custom-post-type' ),
					'description'         => __( 'Post Type Description', 'manage-custom-post-type' ),
					'labels'              => $mcpt_label[$i],
					'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
					'taxonomies'          => array( 'category', 'post_tag' ),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => 5,
					'menu_icon'           => $mcpt_icon,
					'can_export'          => true,
					'has_archive'         => true,
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'capability_type'     => 'page',
				);
				register_post_type( $mcpt_slug, $mcpt_args[$i] );
			}
		}
	}
}
