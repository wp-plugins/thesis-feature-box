<?php
/**
 * @package Thesis Feature Box
 * @author Melvin Ram
 * @version 1.0
 */
/*

Plugin Name: Thesis Feature Box
Plugin URI: http://www.webdesigncompany.net/wordpress/plugins/thesis-feature-box/
Author: Melvin Ram
Author URI: http://www.webdesigncompany.net
Description: Make your feature box useful.
Version: 1.0

*/

global $wp_version;

$exit_msg = 'Thesis Feature Box for WordPress requires Wordpress 2.8 or newer. <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';

if (version_compare($wp_version, "2.8","<")){ exit ($exit_msg); }

// require_once 'wdc/wdc.class.php';

//Avoid name collisions.
if ( !class_exists('WPTFeatureBox')) 
  : class WPTFeatureBox{

    //name for our options in the DB
    var $DB_option = 'WPTFeatureBox_options';
  
    // the plugin URL
    var $plugin_url;
  
    // initialize Wordpress hooks
    function WPTFeatureBox(){
      $this->plugin_url = trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
      
      // admin_menu hook
      add_action('admin_menu', array(&$this, 'admin_menu'));
    }
    
    // Set up everything
    function install() {
      
    }
    
    // Hook the admin menu
    function admin_menu(){
      // custom panel for edit post
      add_meta_box('WPTFeatureBox', 'Feature Box', array(&$this, 'draw_feature_box_panel'), 'post', 'normal', 'high');
      add_meta_box('WPTFeatureBox', 'Feature Box', array(&$this, 'draw_feature_box_panel'), 'page', 'normal', 'high');
      
      // hook into save_post action - save our data at the same time the post is saved
      add_action('save_post', array(&$this, 'feature_box_save_post'));
    }
    
    // draw the feature box panel
    function draw_feature_box_panel($post,$box){
      $feature_box_content = get_post_meta($post->ID,'_wdc_thesis_feature_box_content',true);
      
      echo '<label for="wdc_thesis_feature_box">Enter content for feature box</label><br />';
      echo '<textarea rows="3" style="width:99%;" id="wdc_thesis_feature_box_content" name="wdc_thesis_feature_box" autocomplete="off">';
      echo $feature_box_content;
      echo '</textarea>';

    }
        
    function feature_box_save_post($post_id){

      $post = get_post($post_id);

      // the save post function runs for active posts and for revisions - but we don't want to run on revisions
      if($post->post_type == 'revision') { return; }
      
      // proceed if content in $_POST
    	if(isset($_POST['wdc_thesis_feature_box'])) {
    	  
    		// save the meta key name with a prefixed underscore prevents it from showing up in the custom post-meta section of the post-edit admin page
    		update_post_meta($post_id,'_wdc_thesis_feature_box_content',$_POST['wdc_thesis_feature_box']);
    	}
    	
    }
  }
  endif;

function wdc_plugins($plugins) {
	if ( is_array($plugins) ) {
		$plugins[] = 'Thesis Feature Box';
	}
	return $plugins;
}

  
if (class_exists('WPTFeatureBox'))
  : $WPTFeatureBox = new WPTFeatureBox();
  if (isset($WPTFeatureBox)){
    register_activation_hook(__FILE__, array(&$WPTFeatureBox, 'install'));
  }
endif;


function wdc_thesis_feature_box_content(){
  global $post;
  if (is_singular()){
    echo get_post_meta($post->ID,'_wdc_thesis_feature_box_content',true);
  }
}

add_action('thesis_hook_feature_box', 'wdc_thesis_feature_box_content');

add_filter('wdc_plugins', array('cmAWB', 'wdc_plugins'));
?>