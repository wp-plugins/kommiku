<?php 
	if(!$settings) {
		$settings['url'] = get_option( 'kommiku_url_format' );
		$settings['upload'] = get_option( 'kommiku_comic_upload' );
		$settings['theme'] = get_option( 'kommiku_skin_directory' );
		$settings['one_comic'] = get_option( 'kommiku_one_comic' );
		$settings['skin'] = $settings['theme'];
		$settings['key'] = get_option( 'K_A_K' );
		$settings['scanlator_url'] = get_option( 'kommiku_scanlator' );
		$settings['scanlator_enable'] = get_option( 'kommiku_scanlator_enabled' );
		$settings['kommiku_override_index'] = get_option( 'kommiku_override_index' );
		$settings['directory'] = get_option( 'kommiku_url_index' );
	}
	if($settings['kommiku_override_index']) $checkboxOver = ' checked=checked';
	if($settings['scanlator_enable']) $checkboxOverTwo = ' checked=checked';
	
	$settings['list'] = getFileList(KOMMIKU_FOLDER.'/themes/');
	if($settings['url']) $settings_url = $settings['url'].'/';
	//For no Slug to happen, we need a Series!
	if($settings['one_comic'] == 'false') $settings['one_comic'] = '';
	if(!$settings['scanlator_url']) {
		add_option('kommiku_scanlator', 'author');
		$settings['scanlator_url'] = 'author';
		}
?>

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><?_e('Kommiku Settings')?></h2>
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
				<h3 style="cursor: default;"><span><?_e('Upload Directory')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text"><?_e('Upload Directory')?></label>
						<input style="width: 100%; <?php if($settings['fail']['upload']) echo 'background: #ffeeee;'; ?>" type="text" autocomplete="off"  value="<?php echo $settings['upload']; ?>" tabindex="1" size="30" name="upload"/>
						<p><?_e('The directory where your comics will be uploaded to.')?><br/><?_e('Your comics will be uploaded to: ')?><strong><?php echo get_bloginfo('url'); ?>/<?php echo $settings['upload']; ?>/</strong><br/><span style="font-style: italic;"><?_e('* Do not name your Upload Directory and Permalink the Same name.')?></span></p>
					</div>
				</div>
			</div>

			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Permalink: Comic Base')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="url" class="screen-reader-text"><?_e('Permalink: Comic Base')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $settings['url']; ?>" tabindex="1" size="30" name="url"/>
						<p><?_e('This is where you will view your comic.')?><br/><?_e('Your current Permalinks to your Comic is:')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $settings['url']; ?>/[<?_e('Slug')?>]</strong><br/><span style="font-style: italic;"><?_e('* Do not name your Upload Directory and Permalink the Same name.')?></span><br/><span style="font-style: italic;"><?_e('* Blank  base-slug may be buggy.')?></span></p>
					</div>
				</div>
			</div>
				
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Directory Index URL')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="url" class="screen-reader-text"><?_e('Url to Directory:')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?=$settings['directory']?>" tabindex="1" size="30" name="directory"/>
						<p><?_e('Your current Permalinks to your Directory is:')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $settings['directory']; ?>/</strong>
						<br/><?_e('And:')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $settings['url']; ?>/</strong>
						<br/><span style="font-style: italic;"><?_e("* This is your Directory Listing. Make sure it isn't the same as the Permalink/Upload Directory.")?></span></p>
					</div>
				</div>
			</div>
				
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Skin')?></span></h3>
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
					<p><?_e('The Skin or Theme for the Comic Reader.')?></p>		
					</div>
				</div>
			</div>
		
		<?php if(file_exists(KOMMIKU_FOLDER.'/extension/scanlator/scanlator.php')){ ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Scanlator and Authors')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="one_comic" class="screen-reader-text"><?_e('Scanlator Features:')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?=$settings['scanlator_url']?>" tabindex="1" size="30" name="scanlator_url"/>
						<p><?_e('Have multiple authors on your Kommiku?')?><br/><?_e('This feature allows you to give proper credit to your authors.')?><br/><?_e('Your authors will also get a unique page about them.')?> <br/><br/><?_e('Permalink:')?><br/><strong><?php echo get_bloginfo('url'); ?>/<?=$settings['scanlator_url']?>/</strong></p>
						<?_e('Enable this feature:')?> <input type="checkbox"<?=$checkboxOverTwo?>  value="1" name="scanlator_enable"/>

					</div>
				</div>
			</div>
		<?php } ?>

			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('One Story Mode')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="one_comic" class="screen-reader-text"><?_e('Main Story')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?=$settings['one_comic']?>" tabindex="1" size="30" name="one_comic"/>
						<p><?_e('This will switch Kommiku into the "One Story" Mode.')?><br/><?_e("Type in the <strong>Main Story's slug</strong> to identify the Website's Main Story.")?><br/><?_e('The Main Story\'s slug will be replace by the "Comic Base" (See Above)')?><br/><?_e('All other stories will be hidden.')?><br/><br/><?_e('Example of Permalink:')?><br/><?_e('With Chapters:')?> <strong><?php echo get_bloginfo('url'); ?>/1/1/</strong><br/><?_e('Chapterless:')?> <strong><?php echo get_bloginfo('url'); ?>/1/</strong></p>
						<?_e('Override the Index:')?> <input type="checkbox"<?=$checkboxOver?>  value="1" name="override_index"/>

					</div>
				</div>
			</div>
			
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Kommiku Api Key')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="apikey" class="screen-reader-text"><?_e('Key')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $settings['key']; ?>" tabindex="1" size="30" name="apikey"/>
						<p><?_e('Give yourself the special features with the Kommiku Api Key!')?><br/><?_e('Only given to special people... however.')?></p>
					</div>
				</div>
			</div>
			
		</div>
	</div>
		
	<p class="submit">
		<input type="submit" value="Save Changes" class="button-primary" name="submit"/>
	</p>

	</form>

</div>