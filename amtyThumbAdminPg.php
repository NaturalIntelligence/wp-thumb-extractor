<?php 
	if($_POST['amty_hidden'] == 'Y') {
		//Form data sent
		$bulkVar = $_POST['bulk_action'];
		if($bulkVar == 1){
			amty_clearImageCacheSoft();
		}elseif($bulkVar == 2){
			amty_clearImageCacheHard();
		}elseif($bulkVar == 3){
			amty_clearImageCacheFull();
		}elseif($bulkVar == 4){
			amty_populateCache();
		}elseif($bulkVar == 5){
			amty_repopulateImageCache();
		}
		
		$singleVar = $_POST['single_action'];
		$p = $_POST['post_id'];
		if($singleVar == 1){
			amty_putIntoImageCache($p,0);
		}elseif($singleVar == 2){
			amty_putIntoImageCache($p,1);
		}elseif($singleVar == 3){
			amty_deletePostFromCache($p);
		}
	}
?>

<div class="wrap">
<center><h2>amtyThumb Admin page</h2></center>
<br>
<div>
<form name="amtyThumb_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="amty_hidden" value="Y" />
	<strong>Bulk Action</strong>
	<select name="bulk_action">
		<option value="0">No action</option>
		<option value="1">Clear Image cache [soft]</option>
		<option value="2">Clear Image cache [hard]</option>
		<option value="3">Clear Image cache [full]</option>
		<option value="4">Populate Image cache for rest posts</option>
		<option value="5">Repopulate Image cache.</option>
	</select>
	<br />
	<br />
	<strong>Single Action</strong>
	<select name="single_action">
		<option value="0">No action</option>
		<option value="1">Put into cache if absent</option>
		<option value="2">Put into cache even if present</option>
		<option value="3">Delete from cache</option>
	</select>
	Post ID : <input type="text" name="post_id" />
	<br />
	<br />
	Total images cached : <?php echo amty_getImageCacheCount(); ?>
	<p class="submit">
	<input type="submit" name="Submit" value="submit" />
	</p>
</form>
</div>
<hr />
<div>
<strong>Test Image cache</strong>
<form name="amtyThumbShow_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="showthumb" value="Y" />
	Post ID : <input type="text" name="pid" value="<?php echo $_POST['pid'];?>"/>
	<input type="submit" name="Submit" value="submit" />
	<br />
	<?php 
		if($_POST['showthumb'] == 'Y') {
			
			amty_displayThumb($_POST['pid']);
		}
	?>
</form>
</div>
<hr />
<div>
<strong>Test Plugin </strong>
<br />
<div style="float:left">
<form name="amtyThumbTest_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="testplugin" value="Y" />
	Mode : <select name="mode"><option value="0">Image URL </option><option value="1">Post Id </option></select>
	Image URL/Post ID : <input type="text" name="post_id" value="<?php echo $_POST['post_id'];?>" style="width:250px"/>
	<br />
	Percent (only numeric) : <input type="text" name="percent" value="<?php echo $_POST['percent'];?>" style="width:50px"/> |
	Width : <input type="text" name="width" value="<?php echo $_POST['width'];?>" style="width:50px"/>
	Height : <input type="text" name="height" value="<?php echo $_POST['height'];?>" style="width:50px"/>
	<br />
	Zoom/Crop :  <select name="zc"><option value="0">0</option><option value="1">1</option></select>
	Constrain : <select name="cons"><option value="0">0</option><option value="1">1</option></select>
	<center><p><input type="submit" name="Submit" value="submit" /></p></center>
</form>
</div>
<div>
	<?php 
		if($_POST['testplugin'] == 'Y') {
			if($_POST['mode'] == 0){
				amty_testPlugin($_POST['post_id'],'',$_POST['width'],$_POST['height'],$_POST['percent'],$_POST['cons'],$_POST['zc']);
			}
			else{
				amty_testPlugin('',$_POST['post_id'],$_POST['width'],$_POST['height'],$_POST['percent'],$_POST['cons'],$_POST['zc']);
			}
		}
	?>
</div>
</div>
<div style="clear:both;"></div>
<hr />
</div>