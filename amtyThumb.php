<?php
/*
Plugin Name: amtyThumb
Plugin URI: http://article-stack.com/
Description: This plugin fetch first image from your post even if it is vedio.

Author: Amit Gupta
Version: 3.0.0
Author URI: http://article-stack.com/
*/


add_shortcode( 'amtyThumbOnly', 'amtyThumbOnly_shortcode' );

include ("lead-img.php");
include ("amtyThumbAdminFunction.php");

function amtyThumbOnly_shortcode( $attr, $content = null ) {
    extract( shortcode_atts( array(
					 'percent' => '100',
					 'width' => '',
					 'height' => '',
					 'constrain' => '1',
					 'resize' => 'zoom',	//crop
					 'image_url' => '',
					 'post_id' => ''
					 ), $attr ) );
if($resize == 'zoom')
	$resize = '' ;
else
	$resize = '1';
echo amty_lead_img($width,$height,$constrain,$image_url,$percent,$resize,$post_id);

}

function amtyThumb_admin() {
	include('amtyThumbAdminPg.php');
}

function amtyThumb_admin_actions() {
    add_options_page("amtyThumb Options", "amtyThumb Options", "activate_plugins", "amtyThumbOptions", "amtyThumb_admin");
}

add_action('admin_menu', 'amtyThumb_admin_actions');

//Fetch thumbnail when post get published
function push_notification($post_id) 
{ 
  amty_putIntoImageCache($post_id); 
}

add_action('publish_post','update_thumb');

?>