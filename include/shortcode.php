<?php 
/** 
 * Register custom post type to manage shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'categoryrichpostaccordionShortcode_Admin' ) ) {
	class categoryrichpostaccordionShortcode_Admin extends categoryrichpostaccordionLib {
	
		public $_shortcode_config = array();
		 
		/**
		 * constructor method.
		 *
		 * Register post type for accordion panel for category and posts shortcode
		 * 
		 * @access    public
		 * @since     1.0
		 *
		 * @return    void
		 */
		public function __construct() {
			
			parent::__construct();
			
	       /**
		    * Register hooks to manage custom post type for accordion panel for category and posts
		    */
			add_action( 'init', array( &$this, 'apcp_registerPostType' ) );  
			//add_action( 'admin_menu', array( &$this, 'apcp_addadminmenu' ) );  
			add_action( 'add_meta_boxes', array( &$this, 'add_richpostaccordion_metaboxes' ) );
			add_action( 'save_post', array(&$this, 'wp_save_richpostaccordion_meta' ), 1, 2 ); 
			add_action( 'admin_enqueue_scripts', array( $this, 'apcp_admin_enqueue' ) ); 
			
		   /* Register hooks for displaying shortcode column. */ 
			if( isset( $_REQUEST["post_type"] ) && !empty( $_REQUEST["post_type"] ) && trim($_REQUEST["post_type"]) == "apcp_accordion" ) {
				add_action( "manage_posts_custom_column", array( $this, 'richpostaccordionShortcodeColumns' ), 10, 2 );
				add_filter( 'manage_posts_columns', array( $this, 'apcp_shortcodeNewColumn' ) );
			}
			
			add_action( 'wp_ajax_apcp_getCategoriesOnTypes',array( &$this, 'apcp_getCategoriesOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_apcp_getCategoriesOnTypes', array( &$this, 'apcp_getCategoriesOnTypes' ) );
			add_action( 'wp_ajax_apcp_getCategoriesRadioOnTypes',array( &$this, 'apcp_getCategoriesRadioOnTypes' ) ); 
			add_action( 'wp_ajax_nopriv_apcp_getCategoriesRadioOnTypes', array( &$this, 'apcp_getCategoriesRadioOnTypes' ) ); 
			add_filter( 'wp_editor_settings', array( $this, 'apcp_postbodysettings' ), 10, 2 );
		}  
		
		/**
		* Set the post body type
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		public function apcp_postbodysettings( $settings, $editor_id ) { 
		
			global $post; 
			
			if( $post->post_type == "apcp_postaccordions" ) {
			
				$settings = array(
						'wpautop'             => false,
						'media_buttons'       => false,
						'default_editor'      => '',
						'drag_drop_upload'    => false,
						'textarea_name'       => $editor_id,
						'textarea_rows'       => 20,
						'accordionindex'            => '',
						'accordionfocus_elements'   => ':prev,:next',
						'editor_css'          => '',
						'editor_class'        => '',
						'teeny'               => false,
						'dfw'                 => false,
						'_content_editor_dfw' => false,
						'tinymce'             => true,
						'quicktags'           => true
					);
			
			}
			
			return $settings;
			
		}
		
		/**
		* Admin menu configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		public function apcp_addadminmenu() { 
		
		
			add_submenu_page('edit.php?post_type=apcp_accordion', __( 'All Accordion Posts', 'richpostaccordion' ), __( 'All Accordion Posts', 'richpostaccordion' ),  'manage_options', 'edit.php?post_type=apcp_postaccordions');
			
			add_submenu_page('edit.php?post_type=apcp_accordion', __( 'New Accordion Post', 'richpostaccordion' ), __( 'New Accordion Post', 'richpostaccordion' ),  'manage_options', 'post-new.php?post_type=apcp_postaccordions'); 
			
			add_submenu_page('edit.php?post_type=apcp_accordion', __( 'Accordion Categories', 'richpostaccordion' ), __( 'Accordion Categories', 'richpostaccordion' ),  'manage_options', 'edit-tags.php?taxonomy=apcp_accordion_categories&post_type=apcp_accordion'); 
						
		}
		
 	   /**
		* Register and load JS/CSS for admin widget configuration 
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool|void It returns false if not valid page or display HTML for JS/CSS
		*/  
		public function apcp_admin_enqueue() {
		 
			if ( ! $this->validate_page() )
				return FALSE;
			
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'admin-richpostaccordion.css', apcp_media."css/admin-richpostaccordion.css" );
			wp_enqueue_script( 'admin-richpostaccordion.js', apcp_media."js/admin-richpostaccordion.js" ); 
			
		}		
		 
	   /**
		* Add meta boxes to display shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/ 
		public function add_richpostaccordion_metaboxes() {
			
			/**
			 * Add custom fields for shortcode settings
		     */
			add_meta_box( 'wp_richpostaccordion_fields', __( 'Rich Accordion Shortcode and Plugin', 'richpostaccordion' ),
				array( &$this, 'wp_richpostaccordion_fields' ), 'apcp_accordion', 'normal', 'high' );
			
			/**
			 * Display shortcode of accordion panel for category and posts
		     */
			add_meta_box( 'wp_richpostaccordion_shortcode', __( 'Shortcode', 'richpostaccordion' ),
				array( &$this, 'shortcode_meta_box' ), 'apcp_accordion', 'side' );	
		
		}  
		
	   /**
		* Validate widget or shortcode post type page
		*
		* @access  private
		* @since   1.0
		*
		* @return  bool It returns true if page is post.php or widget otherwise returns false
		*/ 
		private function validate_page() {
 
			if ( ( isset( $_GET['post_type'] )  && $_GET['post_type'] == 'apcp_accordion' ) || strpos($_SERVER["REQUEST_URI"],"widgets.php") > 0  || strpos($_SERVER["REQUEST_URI"],"post.php" ) > 0 || strpos($_SERVER["REQUEST_URI"], "richpostaccordion_settings" ) > 0  )
				return TRUE;
		
		} 			
 
	   /**
		* Display richpostaccordion block configuration fields
		*
		* @access  private
		* @since   1.0
		*
		* @return  void Returns HTML for configuration fields 
		*/  
		public function wp_richpostaccordion_fields() { 
			
			global $post; 
			 
			foreach( $this->_config as $kw => $kw_val ) {
				$this->_shortcode_config[$kw] = get_post_meta( $post->ID, $kw, true ); 
			}
			  
			foreach ( $this->_shortcode_config as $sc_key => $sc_val ) {
				if( trim( $sc_val ) == "" )
					unset( $this->_shortcode_config[ $sc_key ] );
				else {
					if(!is_array($sc_val) && trim($sc_val) != "" ) 
						$this->_shortcode_config[ $sc_key ] = htmlspecialchars( $sc_val, ENT_QUOTES );
					else 
						$this->_shortcode_config[ $sc_key ] = $sc_val;
				}	
			}
			
			foreach( $this->_config as $kw => $kw_val ) {
				if( !is_array($this->_shortcode_config[$kw]) && trim($this->_shortcode_config[$kw]) == "" ) {
					$this->_shortcode_config[$kw] = $this->_config[$kw]["default"];
				} 
			}
			
			$this->_shortcode_config["vcode"] = get_post_meta( $post->ID, 'vcode', true );   
			 
			
			//$this->_shortcode_config = wp_parse_args( $this->_shortcode_config, $this->_config );
			require( $this->getcategoryPostsAccordionTemplate( "admin/admin_shortcode_post_type.php" ) );
			 
		}
		
	   /**
		* Display shortcode in edit mode
		*
		* @access  private
		* @since   1.0
		*
		* @param   object  $post Set of configuration data.
		* @return  void	   Displays HTML of shortcode
		*/
		public function shortcode_meta_box( $post ) {

			$richpostaccordion_id = $post->ID;

			if ( get_post_status( $richpostaccordion_id ) !== 'publish' ) {

				echo '<p>'.__( 'Please make the publish status to get the shortcode', 'richpostaccordion' ).'</p>';

				return;

			}

			$richpostaccordion_title = get_the_title( $richpostaccordion_id );

			$shortcode = sprintf( "[%s id='%s']", 'richpostaccordion', $richpostaccordion_id );
			
			echo "<p class='tpp-code'>".$shortcode."</p>";
		}
				  
	   /**
		* Save accordion panel for category and posts shortcode fields
		*
		* @access  private
		* @since   1.0 
		*
		* @param   int    	$post_id post id
		* @param   object   $post    post data object
		* @return  void
		*/ 
		function wp_save_richpostaccordion_meta( $post_id, $post ) {
			
			  /**if( !isset($_POST['richpostaccordion_nonce']) ) {
				return $post->ID;
			}
	
		 
			* Verify _nonce from request
			
			if( !wp_verify_nonce( $_POST['richpostaccordion_nonce'], plugin_basename(__FILE__) ) ) {
				return $post->ID;
			} */
			
		   /**
			* Check current user permission to edit post
			*/
			if(!current_user_can( 'edit_post', $post->ID ))
				return $post->ID; 
			 
		   /**
			* sanitize text fields 
			*/
			$richpostaccordion_meta = array(); 
			
			// Validate Meta Fields
			/*$validate_fields = array(); 
			foreach( $this->_config as $kw => $kw_val ) { 
				$_save_value =  $_POST["nm_".$kw];
				if($kw_val["type"]=="boolean"){
					$_save_value = $_POST["nm_".$kw][0];
				}
				if( $kw_val["type"]=="checkbox" && count($_POST["nm_".$kw]) > 0 ) {
					$_save_value = implode( ",", $_POST["nm_".$kw] );
				}
				if( $kw_val["is_required"] == "yes" && trim( $_save_value ) == "" ) {
						$validate_fields[$kw] =  $kw_val["field_title"]." field is required.";
				}
			
			}
			if(count($validate_fields) > 0) return $validate_fields;*/
			// End Validation
			
			
			foreach( $this->_config as $kw => $kw_val ) { 
				$_save_value =  $_POST["nm_".$kw];
				if($kw_val["type"]=="boolean"){
					$_save_value = $_POST["nm_".$kw][0];
				}
				if( $kw_val["type"]=="checkbox" && count($_POST["nm_".$kw]) > 0 ) {
					$_save_value = implode( ",", $_POST["nm_".$kw] );
				}
				$richpostaccordion_meta[$kw] =  sanitize_text_field( $_save_value );
			}     
			 
			foreach ( $richpostaccordion_meta as $key => $value ) {
			
			   if( $post->post_type == 'revision' ) return;
				$value = implode( ',', (array)$value );
				
				if( trim($value) == "Array" || is_array($value) )
					$value = "";
					
			   /**
				* Add or update posted data 
				*/
				if( get_post_meta( $post->ID, $key, FALSE ) ) { 
					update_post_meta( $post->ID, $key, $value );
				} else { 
					add_post_meta( $post->ID, $key, $value );
				}
				
				//if( ! $value ) delete_post_meta( $post->ID, $key );
			
			}	
			 
		}
		
			 
	   /**
		* Register post type for accordion panel for category and posts shortcode
		*
		* @access  private
		* @since   1.0
		*
		* @return  void
		*/  
		function apcp_registerPostType() { 
			
		   /**
			* Post type and menu labels 
			*/
			$labels = array(
				'name' => __('Accordion Category & Posts View Shortcode', 'richpostaccordion' ),
				'singular_name' => __( 'Accordion Category & Posts View Shortcode', 'richpostaccordion' ),
				'add_new' => __( 'Add New Shortcode', 'richpostaccordion' ),
				'add_new_item' => __( 'Add New Shortcode', 'richpostaccordion' ),
				'edit_item' => __( 'Edit', 'richpostaccordion'  ),
				'new_item' => __( 'New', 'richpostaccordion'  ),
				'all_items' => __( 'All', 'richpostaccordion'  ),
				'view_item' => __( 'View', 'richpostaccordion'  ),
				'search_items' => __( 'Search', 'richpostaccordion'  ),
				'not_found' =>  __( 'No item found', 'richpostaccordion'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'richpostaccordion'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'APCP', 'richpostaccordion'  ) 
			);
			
		   /**
			* Rich Accordion Shortcode and Plugin post type registration options
			*/
			$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title' )
			);
			
		   /**
			* Register new post type
			*/
			register_post_type( 'apcp_accordion', $args );
				
			
			
			/**
			* menu labels 
			*/
			/*$labels = array(
				'name' => __('Accordion Posts', 'richpostaccordion' ),
				'singular_name' => __( 'Accordion Posts', 'richpostaccordion' ),
				'add_new' => __( 'New Accordion Post', 'richpostaccordion' ),
				'add_new_item' => __( 'New Accordion Post', 'richpostaccordion' ),
				'edit_item' => __( 'Edit', 'richpostaccordion'  ),
				'new_item' => __( 'New', 'richpostaccordion'  ),
				'all_items' => __( 'All', 'richpostaccordion'  ),
				'view_item' => __( 'View', 'richpostaccordion'  ),
				'search_items' => __( 'Search', 'richpostaccordion'  ),
				'not_found' =>  __( 'No item found', 'richpostaccordion'  ),
				'not_found_in_trash' => __( 'No item found in Trash', 'richpostaccordion'  ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Accordion Posts', 'richpostaccordion'  ) 
			);*/
			
		   /**
			* post type registration options
			*/
			/*$args = array(
				'labels' => $labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => false,
				'rewrite' => false,
				'capability_type' => 'post',
				'menu_icon' => 'dashicons-list-view',
				'has_archive' => false,
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title','editor','thumbnail' )
			);*/
			
		   /**
			* Register post type
			*/
			//register_post_type( 'apcp_postaccordions', $args ); 	
				
		   /**
			* Register category for custom post type
			*/
			
			/*$labels = array(
					'name' => _x( 'Categories', 'taxonomy general name' ),
					'singular_name' => _x( 'Category', 'taxonomy singular name' ),
					'search_items' => __( 'Search Categories' ),
					'all_items' => __( 'All Categories' ),
					'parent_item' => array( null ),
					'parent_item_colon' => array( null ),
					'edit_item' => __( 'Edit Category' ),
					'view_item' => __( 'View Category' ),
					'update_item' => __( 'Update Category' ),
					'add_new_item' => __( 'Add New Category' ),
					'new_item_name' => __( 'New Category Name' ), 
					'not_found' => __( 'No categories found.' ),
					'no_terms' => __( 'No categories' ),
					'items_list_navigation' => __( 'Categories list navigation' ),
					'items_list' => __( 'Categories list' ),
			);

			register_taxonomy('apcp_accordion_categories',array('apcp_postaccordions'),array(
				'hierarchical'=>true,
				'labels' => $labels,
				'show_ui'=>true,
				'show_admin_column'=>true,
				'query_var'=>true,
				'rewrite'=>array('slug' => 'apcp_accordion_category'),
			)); */
	 			

		}
		
	   /**
		* Display shortcode column in accordion panel for category and posts list
		*
		* @access  private
		* @since   1.0
		*
		* @param   string  $column  Column name
		* @param   int     $post_id Post ID
		* @return  void	   Display shortcode in column	
		*/
		public function richpostaccordionShortcodeColumns( $column, $post_id ) { 
		
			if( $column == "shortcode" ) {
				 echo sprintf( "[%s id='%s']", 'richpostaccordion', $post_id ); 
			}  
		
		}
		
	   /**
		* Register shortcode column
		*
		* @access  private
		* @since   1.0
		*
		* @param   array  $columns  Column list 
		* @return  array  Returns column list
		*/
		public function apcp_shortcodeNewColumn( $columns ) {
			
			$_edit_column_list = array();	
			$_i = 0;
			
			foreach( $columns as $__key => $__value) {
					
					if($_i==2){
						$_edit_column_list['shortcode'] = __( 'Shortcode', 'richpostaccordion' );
					}
					$_edit_column_list[$__key] = $__value;
					
					$_i++;
			}
			
			return $_edit_column_list;
		
		}
		
	} 

}

new categoryrichpostaccordionShortcode_Admin();
 
?>