<?php

include ("videothumb.php");
include ("supportingFunction.php");
include ("cacheFunction.php");

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
			//resize and save it with $img_uri name
			if(isImage($img)){//to avoid invalid path or 404 errors
				@resizeImg($img,$percent,$constrain,$w,$h,$zc,$img_uri);
			}else{
				$img_url = $default_img;
			}
		}
			
		if($img_url_only == "y"){
			$out = $img_url;
		}else{
			$out = '<img src="'.$img_url.'" />';
		}
	}
	return $out;
}//function end


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

?>