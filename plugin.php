<?php 
/*
  Plugin Name: Responsive accordions and vertical tabs widget shortcode wordpress plugin
  Description: An accordion panel posts view for the widget and content block
  Author: iKhodal Web Solution
  Plugin URI: https://www.ikhodal.com/category-and-post-accordion-panel
  Author URI: https://www.ikhodal.com
  Version: 2.1  
  Text Domain: richpostaccordion
*/ 
  
  
//////////////////////////////////////////////////////
// Defines the constants for use within the plugin. //
////////////////////////////////////////////////////// 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly  
 
/**
*  Assets of the plugin
*/
$apcp_plugins_url = plugins_url( "/assets/", __FILE__ );

define( 'apcp_media', $apcp_plugins_url ); 

/**
*  Plugin DIR
*/
$apcp_plugin_dir = plugin_basename(dirname(__FILE__));

define( 'apcp_plugin_dir', $apcp_plugin_dir ); 
 
///////////////////////////////////////////////////////
// Include files for widget and shortcode management //
///////////////////////////////////////////////////////

/**
 * Include abstract class for common methods
 */
require_once 'include/abstract.php';
 
/**
 * Register custom post type for shortcode
 */ 
require_once 'include/shortcode.php';

/**
 * Admin panel widget configuration
 */ 
require_once 'include/admin.php';

/**
 * Load Rich Accordion Shortcode and Plugin on frontent pages
 */
require_once 'include/richpostaccordion.php'; 

/**
 * Clean data on activation / deactivation
 */
require_once 'include/activation_deactivation.php';  
 