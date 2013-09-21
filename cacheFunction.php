<?php

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
		//do nothing
	}else{
		$img = amty_take_first_img_by_id($postId);
		if($img ==''){//image no present
			if($default_img != ''){//custom default image
				$img = $default_img;
			}
			else{
				$img = WP_PLUGIN_URL . "/amtythumb/amtytextthumb.gif";
			}
		}elseif(isImage($img)){//image is not valid
			$img = WP_PLUGIN_URL . "/amtythumb/invalid.gif";
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

function reportBrokenImage(){
		$cnt=0;
	$query = new WP_Query( 'posts_per_page=-1' );
	while ( $query->have_posts() ) : $query->the_post();
		$pid = get_the_ID();
		$metaVal = get_post_meta($pid,'amtyThumb',true);
		if(!isImage($metaVal)){
			echo "PostID :" . $pid . ". Broken imahe URL : " + $metaVal;
		}
	endwhile;
	wp_reset_postdata();
	return $cnt;
}
?>