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

function amty_lead_img($w='',$h='',$constrain='',$img='',$percent='',$zc='',$post_id = '',$img_url_only = 'y',$default_img = '') {
	
	$pid=-1;
	if($img == ''){
		if($post_id == ''){
			global $id;
			$pid=$id;
		}
		else{
			$pid=$post_id;
		}
		amty_putIntoImageCache($pid,0,$default_img);
		$img = get_post_meta($pid,'amtyThumb',true);
	}
	if($img != ''){
		$img_uri = WP_PLUGIN_DIR . "/amtythumb/cache/". $pid . "_" . $w . "_" . $h . ".jpg";
		$img_url = WP_PLUGIN_URL . "/amtythumb/cache/". $pid . "_" . $w . "_" . $h . ".jpg";
		if(!file_exists($img_uri)) {
			@resizeImg($img,$percent,$constrain,$w,$h,$zc,$img_uri);
		}
			
		if($img_url_only == "y"){
			$out = $img_url;
		}else{
			$out = '<img src="'.$img_url.'" />';
		}
	}
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

function resizeImg($img,$percent,$constrain,$w,$h,$zc,$imgPath){
	// get image size of img
	$x = @getimagesize($img);	
	// image width
	$sw = $x[0];
	// image height
	$sh = $x[1];
	if( $sh >0 AND $sw > 0){
		if ($percent > 0) {
			// calculate resized height and width if percent is defined
			$percent = $percent * 0.01;
			$w = $sw * $percent;
			$h = $sh * $percent;
		} else {
			if (isset ($w) AND !isset ($h)) {
				// autocompute height if only width is set
				$h = (100 / ($sw / $w)) * .01;
				$h = @round ($sh * $h);
			} elseif (isset ($h) AND !isset ($w)) {
				// autocompute width if only height is set
				$w = (100 / ($sh / $h)) * .01;
				$w = @round ($sw * $w);
			} elseif (isset ($h) AND isset ($w) AND $constrain > 0) {
				// get the smaller resulting image dimension if both height
				// and width are set and $constrain is also set
				$hx = (100 / ($sw / $w)) * .01;
				$hx = @round ($sh * $hx);

				$wx = (100 / ($sh / $h)) * .01;
				$wx = @round ($sw * $wx);

				if ($hx < $h) {
					$h = (100 / ($sw / $w)) * .01;
					$h = @round ($sh * $h);
				} else {
					$w = (100 / ($sh / $h)) * .01;
					$w = @round ($sw * $w);
				}
			}
		}
	}
	$im = @ImageCreateFromJPEG ($img) or // Read JPEG Image
	$im = @ImageCreateFromPNG ($img) or // or PNG Image
	$im = @ImageCreateFromGIF ($img) or // or GIF Image
	$im = false; // If image is not JPEG, PNG, or GIF

	if (!$im) {
		// We get errors from PHP's ImageCreate functions...
		// So let's echo back the contents of the actual image.
		readfile ($img);
	} else {
		// Create the resized image destination
		$thumb = @ImageCreateTrueColor ($w, $h);
		// Copy from image source, resize it, and paste to image destination
		
		if( $zc > 0) {
			echo "cropping image" . $zc;
			$new_width = $w;
			$new_height = $h;
			$width = imagesx($im);
			$height = imagesy($im);
			
			if( $new_width > $width ) {
				$new_width = $width;
			}
			if( $new_height > $height ) {
				$new_height = $height;
			}
		
			$src_x = $src_y = 0;
			$src_w = $width;
			$src_h = $height;

			$cmp_x = $width  / $new_width;
			$cmp_y = $height / $new_height;

			// calculate x or y coordinate and width or height of source

			if ( $cmp_x > $cmp_y ) {

				$src_w = round( ( $width / $cmp_x * $cmp_y ) );
				$src_x = round( ( $width - ( $width / $cmp_x * $cmp_y ) ) / 2 );

			} elseif ( $cmp_y > $cmp_x ) {

				$src_h = round( ( $height / $cmp_y * $cmp_x ) );
				$src_y = round( ( $height - ( $height / $cmp_y * $cmp_x ) ) / 2 );

			}

			@ImageCopyResampled( $thumb, $im, 0, 0, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h );

		} else {
			// copy and resize part of an image with resampling
			@ImageCopyResampled ($thumb, $im, 0, 0, 0, 0, $w, $h, $sw, $sh);
		}
		
		// Output resized image
		//@ImageJPEG ($thumb);
		$quality = 100;
		if($w < 100 || $h <100 ){
			$quality = 50;
		}elseif($w < 200 || $h <200 ){
			$quality = 80;
		}
		//saving to a file
		if($thumb != '')
			imagejpeg($thumb,$imgPath , $quality);
		// Free up memory
		imagedestroy($thumb);
	}
}

//empty image cache and all thumbnails from file system
function amty_clearImageCacheSoft(){
	$query = new WP_Query( 'posts_per_page=-1' );
	while ( $query->have_posts() ) : $query->the_post();
		delete_post_meta(get_the_ID(), 'amtyThumb');
	endwhile;
	wp_reset_postdata();
}

function amty_clearImageCacheHard(){
	$dir = WP_PLUGIN_DIR . "/amtythumb/cache";
	if($handle=opendir($dir)){
		while ( ($file = readdir($handle)) !==false) {
			@unlink($dir.'/'.$file);
		}
		closedir($handle);
	}
}

function amty_clearImageCacheFull(){
	amty_clearImageCacheSoft();
	amty_clearImageCacheHard();
}


//delete an image from cache and its all thumbnails from file system
function amty_deletePostFromCache($postId){
	if(get_post_meta($postId,'amtyThumb',true) != '' ){
		$dir = WP_PLUGIN_DIR . "/amtythumb/cache";
		if($handle=opendir($dir)){
			while ( ($file = readdir($handle)) !==false) {
				if(preg_match('/^'. $postId .'_.*\.jpg/', $file)){
					@unlink($dir.'/'.$file);
				}
			}
			closedir($handle);
		}
		delete_post_meta($postId, 'amtyThumb');
	}
}
//put 1st image of the post into cache if does not present.
//if force != 0 put 1st image of the post into cache even if presents.
function amty_putIntoImageCache($postId,$force=0,$default_img=''){
	$metaVal = get_post_meta($postId,'amtyThumb',true);
	if($force == 0 && $metaVal != ''){
	}else{
		$img = amty_take_first_img_by_id($postId);
		if($img ==''){
			if($default_img != ''){
				$img = $default_img;
			}
			else{
				$img = WP_PLUGIN_URL . "/amtythumb/amtytextthumb.gif";
			}
		}
		update_post_meta($postId,'amtyThumb',$img);
	}
}

//cache images for uncached posts
function amty_populateCache($force=0){
	$query = new WP_Query( 'posts_per_page=-1' );
	while ( $query->have_posts() ) : $query->the_post();
		amty_putIntoImageCache(get_the_ID(),$force);
	endwhile;
	wp_reset_postdata();
}

//empty current acche and repopulate it for all posts
function amty_repopulateImageCache(){
	amty_populateCache(1);
}

function amty_getImageCacheCount(){
	$cnt=0;
	$query = new WP_Query( 'posts_per_page=-1' );
	while ( $query->have_posts() ) : $query->the_post();
		$metaVal = get_post_meta(get_the_ID(),'amtyThumb',true);
		if($metaVal != ''){
			$cnt= $cnt + 1;
		}
	endwhile;
	wp_reset_postdata();
	return $cnt;
}

function amty_displayThumb($postid){
	$metaVal = get_post_meta($postid,'amtyThumb',true);
	echo '<div style="float:left;width:50%;">';
	if($metaVal != ''){
		echo "<br />First cached image from post";
		echo '<br /><a href="' . $metaVal . '" class="thickbox"><img src="'.$metaVal.'" width="300" alt="Cache the image before displaying the thumbnail" /></a><br />';
	}
	echo '</div>';
	echo '<div style="float:left;width:50%;">';
	$dir = WP_PLUGIN_DIR . "/amtythumb/cache";
	$url =  WP_PLUGIN_URL . "/amtythumb/cache";
	echo "<br />Image path on server : " . $dir;
	echo "<br />Image url : " . $url;
	echo "<br />Images cached on File system";
	if($handle=opendir($dir)){
		while ( ($file = readdir($handle)) !==false) {
			if(preg_match('/^'. $postid .'_.*\.jpg/', $file)){
				echo '<br /><center><a href="' . $url.'/'.$file . '" class="thickbox"><img src="'.$url.'/'.$file.'" width="300" alt="Cache the image before displaying the thumbnail" /></a><br />'.$file.'</center><br />';
			}
		}
		closedir($handle);
	}
	echo '</div><div style="clear:both;"></div>';
}

function amty_testPlugin($imgurl,$pid,$w,$h,$percent,$constrain,$zc){
	
	if($pid != ''){
		$starttime = time();
		$img = amty_take_first_img_by_id($pid);
		$endtime = time();
		echo "<br />Time to extract image from post: " . ($endtime - $starttime);
	}elseif($imgurl != ''){
		$img = $imgurl;
	}
	//echo $img;
	$img_uri = WP_PLUGIN_DIR . "/amtythumb/testimage.jpg";
	$img_url = WP_PLUGIN_URL . "/amtythumb/testimage.jpg";
	//echo $img_uri;
	$starttime = time();
	$endtime = time();
	@unlink($img_uri);
	@resizeImg($img,$percent,$constrain,$w,$h,$zc,$img_uri);
	echo "<br />Time to resize image: " . ($endtime - $starttime);
	
	echo '<br />Original Image<br />';
	echo '<img src="'.$img.'" />';
	echo '<br />Resized Image<br />';
	echo '<img src="'.$img_url.'" />';
}
function amtyThumb_admin() {
	include('amtyThumbAdminPg.php');
}

function amtyThumb_admin_actions() {
    add_options_page("amtyThumb Options", "amtyThumb Options", "activate_plugins", "amtyThumbOptions", "amtyThumb_admin");
}

add_action('admin_menu', 'amtyThumb_admin_actions');

?>