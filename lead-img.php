<?php

/*
Usage examples:

Resize an image to 25 x 25
imgsize.php?w=25&h=25&img=path/to/image.jpg

Resize an image to 50% the size
imgsize.php?percent=50&img=path/to/image.jpg

Resize an image to 50 pixels wide and autocompute the height
imgsize.php?w=50&img=path/to/image.jpg

Resize an image to 100 pixels tall and autocompute the width
imgsize.php?h=100&img=path/to/image.jpg

Resize to 50 pixels width OR 100 pixels tall, whichever resulting image is smaller
imgsize.php?w=50&h=100&constrain=1&img=path/to/image.jpg
*/

include ("videothumb.php");

function amty_lead_img($w='',$h='',$constrain='',$img='',$percent='',$zc='',$post_id = '',
						$img_url_only = 'y',$show_default = 'y',$default_img = '') {	
	if($img == ''){
		if($post_id == ''){
			global $id;
			$img = amty_take_first_img_by_id($id);
		}
		else
			$img = amty_take_first_img_by_id($post_id);
	}
		
	if($img =='' && $show_default != 'y'){
		$img_url = $img;
	}else{
		if($img =='' && $show_default == 'y'){
			if($default_img != '')
				$img = $default_img;
			else
				$img = WP_PLUGIN_URL . "/amtythumb/amtytextthumb.gif";
		}
		if($constrain != '')
			$constrain='constrain='. $constrain . '&';
		if($h != '')
			$h='h='. $h . '&';
		if($w != '')
			$w='w='. $w . '&';
		if($zc != '')
			$zc='zc='. $zc . '&';
		if($percent != '')
			$percent='percent='. $percent . '&';
		$img_url = WP_PLUGIN_URL . "/amtythumb/scripts/imgsize.php?".$zc."". $percent."".$constrain."" . $w ."" . $h ."&img=" . $img ;
	}
		
	if($img_url_only == "y"){
		$out = $img_url;
	}else{
		$out = '<img src="'.$img_url.'" />';
	}
	//echo $out;
	return $out;
}//function end

function amty_take_first_img_by_id($id) {
	$temp = $wp_query;  // assign orginal query to temp variable for later use
	$wp_query = null;
        global $wpdb;
	  $img='';
	  $attach_img='';
	  $uploaded_img = '';

      $image_data = $wpdb->get_results("SELECT guid, post_content, post_mime_type, post_title FROM wp_posts WHERE id = $id");
	  $match_count = preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?\/?>/", $image_data[0]->post_content, $match_array, PREG_PATTERN_ORDER);
	  if($match_count == 0){

		  /*$match_count = preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?>/", $image_data[1]->post_content, $match_array, PREG_PATTERN_ORDER);
		  if($match_count == 0){
			  $match_count = preg_match_all("/<img[^>]+>/i", $image_data[1]->post_content, $match_array, PREG_PATTERN_ORDER);
	  		  if($match_count == 0)
					$match_count = preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?\/>/", $image_data[0]->post_content, $match_array, PREG_PATTERN_ORDER);
					if($match_count == 0){*/
					  $img = thumb($image_data[0]->post_content);
					/*}
	  	}*/
	  }


	  if( $img == '') $img = $match_array[1][0];

	  $attach_img = amty_get_firstimage($output->guid);

	  $first_image_data = array ($image_data[0]);
	  foreach($first_image_data as $output) {
	  if (substr($output->post_mime_type, 0, 5) == 'image'){
	  		$uploaded_img = $output->guid;
	  		break;
	  	}
	  }

$wp_query = $temp;
if( $img != '')	return $img;
if( $attach_img != '')	return $attach_img;
if( $uploaded_img != '')	return $uploaded_img;
return '';
}

//get First attached image
function amty_get_firstimage($post_id='', $size='thumbnail') {
	 $id = (int) $post_id;
	 $args = array(
	  'post_type' => 'attachment',
	  'post_mime_type' => 'image',
	  'numberposts' => 1,
	  'order' => 'ASC',
	  'orderby' => 'menu_order ID',
	  'post_status' => null,
	  'post_parent' => $id
	 );
	 $attachments = get_posts($args);
	 if ($attachments) {
	   $img = wp_get_attachment_image_src($attachments[0]->ID, $size);
	   return $img[0];
	 }else{
	   return '';
	 }
}

?>