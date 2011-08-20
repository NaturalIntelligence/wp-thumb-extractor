<?php
/*
Plugin Name: amtyThumb
Plugin URI: http://article-stack.com/
Description: This plugin fetch first image from your post even if it is vedio.

Author: Amit Gupta
Version: 1.2
Author URI: http://article-stack.com/
*/


add_shortcode( 'amtyThumbOnly', 'amtyThumbOnly_shortcode' );

include ("lead-img.php");

function amtyThumbOnly_shortcode( $attr, $content = null ) {
    extract( shortcode_atts( array(
					 'percent' => '100',
					 'width' => '',
					 'height' => '',
					 'resize' => 'zoom',	//crop
					 'image_url' => '',
					 'post_id' => ''
					 ), $attr ) );
if($resize == 'zoom')
	$resize = '' ;
else
	$resize = '1';
echo amty_lead_img($width,$height,1,$image_url,$percent,$resize,$post_id);

}


?>