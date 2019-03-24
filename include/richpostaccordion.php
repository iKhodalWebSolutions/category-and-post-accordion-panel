<?php  
/**
 * Register shortcode and render post data as per shortcode configuration. 
 */ 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   
if ( ! class_exists( 'categoryrichpostaccordionWidget' ) ) { 
	class categoryrichpostaccordionWidget extends categoryrichpostaccordionLib {
	 
	   /**
		* constructor method.
		*
		* Run the following methods when this class is loaded
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function __construct() {
		
			add_action( 'init', array( &$this, 'init' ) ); 
			parent::__construct();
			
		}  
		
	   /**
		* Load required methods on wordpress init action 
		*
		* @access  public
		* @since   1.0
		*
		* @return  void
		*/ 
		public function init() {
		
			add_action( 'wp_ajax_apcp_getTotalPosts',array( &$this, 'apcp_getTotalPosts' ) );
			add_action( 'wp_ajax_apcp_getPosts',array( &$this, 'apcp_getPosts' ) ); 
			add_action( 'wp_ajax_apcp_getMorePosts',array( &$this, 'apcp_getMorePosts' ) );
			
			add_action( 'wp_ajax_nopriv_apcp_getTotalPosts', array( &$this, 'apcp_getTotalPosts' ) );
			add_action( 'wp_ajax_nopriv_apcp_getPosts', array( &$this, 'apcp_getPosts' ) ); 
			add_action( 'wp_ajax_nopriv_apcp_getMorePosts', array( &$this, 'apcp_getMorePosts' ) ); 
			
			add_shortcode( 'richpostaccordion', array( &$this, 'categoryrichpostaccordion' ) ); 
			
		} 
		
	   /**
		* Get the total numbers of posts
		*
		* @access  public
		* @since   1.0
		* 
		* @param   int    $category_id  		Category ID 
		* @param   string $post_search_text  Post name or any search keyword to filter posts
		* @param   int    $c_flg  				Whether to fetch whether posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @return  int	  Total number of posts  	
		*/  
		public function apcp_getTotalPosts( $category_id, $post_search_text, $c_flg, $is_default_category_with_hidden ) { 
		
			global $wpdb;   
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["apcp_security_key"]["security_key"], 'security' );

		   /**
			* Fetch posts as per search filter
			*/	
			$_res_total = $this->getSqlResult( $category_id, $post_search_text, 0, 0, $c_flg, $is_default_category_with_hidden, 1 );
			
			return $_res_total[0]->total_val;
			 
		}	
		
		/**
		* Get the total numbers of posts on default page load
		*
		* @access  public
		* @since   1.0
		* 
		* @param   int    $category_id  		Category ID 
		* @param   string $post_search_text  Post name or any search keyword to filter posts
		* @param   int    $c_flg  				Whether to fetch whether posts by category id or prevent for searching
		* @param   int    $is_default_category_with_hidden  To check settings of default category If it's value is '1'. Default value is '0'
		* @return  int	  Total number of posts  	
		*/  
		public function apcp_getTotalPostsDefaultPageLoad( $category_id, $post_search_text, $c_flg, $is_default_category_with_hidden ) { 
		
			global $wpdb;   
			 
		   /**
			* Fetch posts as per search filter
			*/	
			$_res_total = $this->getSqlResult( $category_id, $post_search_text, 0, 0, $c_flg, $is_default_category_with_hidden, 1 );
			
			return $_res_total[0]->total_val;
			 
		}

		 
	   /**
		* Render accordion panel for category and posts shortcode
		*
		* @access  public
		* @since   1.0
		*
		* @param   array   $params  Shortcode configuration options from admin settings
		* @return  string  Render accordion panel for category and posts HTML
		*/
		public function categoryrichpostaccordion( $params = array() ) { 			
			
			if(isset($params["id"]) && trim($params["id"]) != "" && intval($params["id"]) > 0) {
			
				$richpostaccordion_id = $params["id"]; 
				$apcp_shortcode = get_post_meta( $richpostaccordion_id ); 
				
				foreach ( $apcp_shortcode as $sc_key => $sc_val ) {			
					$apcp_shortcode[$sc_key] = $sc_val[0];			
				} 
				
				if(!isset($apcp_shortcode["apcp_number_of_post_display"]))	
					$apcp_shortcode["apcp_number_of_post_display"] = 0;
				if(!isset($apcp_shortcode["category_id"]))	
					$apcp_shortcode["category_id"] = 0;
					
				$this->_config = shortcode_atts( $this->_config, $apcp_shortcode ); 
				$this->_config["vcode"] =  "uid_".md5(md5(json_encode($this->_config)).$this->getUCode());	
				
			} else {
			
				$this->init_settings();
				
				// default option settings
				foreach($this->_config as $default_options => $default_option_value ){
				  if(!isset($params[$default_options]))
					$params[$default_options] = $default_option_value["default"];
				}
				
				if(count($params)>0) {
					$this->_config = shortcode_atts( $this->_config, $params ); 
				}
				
				if(!isset($this->_config["category_id"]))	
					$this->_config["category_id"] = 0;
					
				$this->_config["vcode"] =  "uid_".md5(md5(json_encode($this->_config)).$this->getUCode());				
			}
			 
		   /**
			* Load template according to admin settings
			*/
			ob_start();
			
			require( $this->getcategoryPostsAccordionTemplate( "fronted/front_template.php" ) );
			
			return ob_get_clean();
		
		}   
		
	   /**
		* Load more post via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  void Displays searched posts HTML to load more pagination
		*/	
		public function apcp_getMorePosts() {
		
			global $wpdb, $wp_query; 
			
		   /**
			* Check security token from ajax request
			*/
			check_ajax_referer( $this->_config["apcp_security_key"]["security_key"], 'security' );
			
			$_total = ( isset( $_REQUEST["total"] )?esc_attr( $_REQUEST["total"] ):0 ); 
			$category_id =( ( isset( $_REQUEST["category_id"] ) && trim( $_REQUEST["category_id"] ) != ""  ) ? ( $_REQUEST["category_id"] ) : "" );
			$post_search_text = ( isset( $_REQUEST["post_search_text"] )?esc_attr( $_REQUEST["post_search_text"] ):"" );  
			$_limit_start = ( isset( $_REQUEST["limit_start"])?esc_attr( $_REQUEST["limit_start"] ):0 );
			$_limit_end = ( isset( $_REQUEST["apcp_number_of_post_display"])?esc_attr( $_REQUEST["apcp_number_of_post_display"] ):apcp_number_of_post_display ); 
			
		   /**
			* Fetch posts as per search filter
			*/	
			$_result_items = $this->getSqlResult( $category_id, $post_search_text, $_limit_start, $_limit_end );
		  
			require( $this->getcategoryPostsAccordionTemplate( 'fronted/ajax_load_more_posts.php' ) );	
			
			wp_die();
		}    
		
	   /**
		* Load more posts via ajax request
		*
		* @access  public
		* @since   1.0
		* 
		* @return  object Displays searched posts HTML
		*/
		public function apcp_getPosts() {
		
		   global $wpdb; 
			
		   /**
			* Check security token from ajax request
			*/	
		   check_ajax_referer( $this->_config["apcp_security_key"]["security_key"], 'security' );
		   
		   require( $this->getcategoryPostsAccordionTemplate( 'fronted/ajax_load_posts.php' ) );	
		   
  		   wp_die();
		
		}
		 
	   /**
		* Get post list with specified limit and filtered by category and search text
		*
		* @access  public
		* @since   1.0 
		*
		* @param   int     $category_id 		 Selected category ID 
		* @param   string  $post_search_text  Post name or any search keyword to filter posts
		* @param   int     $_limit_end			 Limit to fetch post ending to given position
		* @return  object  Set of searched post data
		*/
		public function getPostList( $category_id, $post_search_text, $_limit_end ) {
			
		   /**
			* Check security token from ajax request
			*/	
			check_ajax_referer( $this->_config["apcp_security_key"]["security_key"], 'security' );
			
		   /**
			* Fetch data from database
			*/
			return $this->getSqlResult( $category_id, $post_search_text, 0, $_limit_end );
			 
		}
		 
		
	}
	
}
new categoryrichpostaccordionWidget();