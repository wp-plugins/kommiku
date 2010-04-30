<?php 
	if(!$settings) {
		$settings['url'] = get_option( 'kommiku_url_format' );
		$settings['upload'] = get_option( 'kommiku_comic_upload' );
		$settings['theme'] = get_option( 'kommiku_skin_directory' );
		$settings['one_comic'] = get_option( 'kommiku_one_comic' );
	}
	
	$settings['list'] = getFileList(KOMMIKU_FOLDER.'/themes/');
	if($settings['url']) $settings_url = $settings['url'].'/';
	//For no Slug to happen, we need a Series!
	if($settings['one_comic'] == 'false') $settings['one_comic'] = '';

?>

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2>Kommiku Settings</h2>
	<?php if ($post['pass'] || $settings['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204); margin-bottom: 0;"><p><?php echo $post['pass'].$settings['error']; ?></p></div>
	<?php } ?>
	<form method="post" action="admin.php?page=kommiku_settings" name="post">
	<input name="what" value="settings" type="hidden"/>
	<input name="action" value="update" type="hidden"/>
	<div class="metabox-holder has-right-sidebar">
		<div id="post-body-content">			
		<div class="metabox-holder">	
		
			<div class="postbox">
				<h3 style="cursor: default;"><span>Upload Directory</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text">Upload Directory</label>
						<input style="width: 100%; <?php if($settings['fail']['upload']) echo 'background: #ffeeee;'; ?>" type="text" autocomplete="off"  value="<?php echo $settings['upload']; ?>" tabindex="1" size="30" name="upload"/>
						<p>The directory where your comics will be uploaded to.<br/>Your comics will be uploaded to: <strong><?php echo get_bloginfo('url'); ?>/<?php echo $settings['upload']; ?>/</strong><br/><span style="font-style: italic;">* Do not name your Upload Directory and Permalink the Same name.</span></p>
					</div>
				</div>
			</div>

			<div class="postbox">
				<h3 style="cursor: default;"><span>Permalink: Comic Base</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text">Permalink: Comic Base</label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $settings['url']; ?>" tabindex="1" size="30" name="url"/>
						<p>This is where you will view your comic.<br/>Your current Permalinks to your Comic is: <strong><?php echo get_bloginfo('url'); ?>/<?php echo $settings['url']; ?>/</strong><br/><span style="font-style: italic;">* Do not name your Upload Directory and Permalink the Same name.</span><br/><span style="font-style: italic;">* Blank  base-slug may be buggy.</span></p>
					</div>
				</div>
			</div>
					
			<div class="postbox">
				<h3 style="cursor: default;"><span>Skin</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
					<select name="skin" style="width: 250px;">
						<?php		
						foreach ($settings['list'] as $row) {
							$option = str_replace(KOMMIKU_FOLDER.'/themes/','',$row);
							$option = $db->trail($option); 
							if($option == $settings['skin'])
								echo '<option value="'.$option.'" selected=selected>'.ucwords($option).'</option>';
							else
								echo '<option value="'.$option.'">'.ucwords($option).'</option>';
							};	
						?>	
					</select>	
					<p>The Skin or Theme for the Comic Reader.</p>		
					</div>
				</div>
			</div>
		
			<div class="postbox">
				<h3 style="cursor: default;"><span>One Story Mode</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text">Main Story</label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?=$settings['one_comic']?>" tabindex="1" size="30" name="one_comic"/>
						<p>This will switch Kommiku into the "One Story" Mode.<br/>Type in the <strong>Main Story's slug</strong> to identify the Website's Main Story.<br/>The Main Story's slug will be replace by the "Comic Base" (See Above)<br/>All other stories will be hidden.<br/><br/>Example of Permalink:<br/>With Chapters: <strong><?php echo get_bloginfo('url'); ?>/1/1/</strong><br/>Chapterless: <strong><?php echo get_bloginfo('url'); ?>/1/</strong></p>
					</div>
				</div>
			</div>
		</div>
	</div>
		
	<p class="submit">
		<input type="submit" value="Save Changes" class="button-primary" name="submit"/>
	</p>

	</form>
	
	<form method="post" action="admin.php?page=kommiku_settings" name="post">
	<input name="what" value="settings" type="hidden"/>
	<input name="action" value="checktable" type="hidden"/>
	<p class="submit">
		<input type="submit" value="Check Tables" class="button-primary" name="submit"/>
	</p>
	</form>
</div>