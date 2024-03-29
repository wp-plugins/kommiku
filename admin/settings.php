<?php 

	if(!$kommiku_settings) {
		$kommiku_settings['url'] = get_option( 'kommiku_url_format' );
		$kommiku_settings['upload'] = get_option( 'kommiku_comic_upload' );
		$kommiku_settings['theme'] = get_option( 'kommiku_skin_directory' );
		$kommiku_settings['one_comic'] = get_option( 'kommiku_one_comic' );
		$kommiku_settings['skin'] = $kommiku_settings['theme'];
		$kommiku_settings['key'] = get_option( 'K_A_K' );
		$kommiku_settings['scanlator_url'] = get_option( 'kommiku_scanlator' );
		$kommiku_settings['scanlator_enable'] = get_option( 'kommiku_scanlator_enabled' );
		$kommiku_settings['kommiku_override_index'] = get_option( 'kommiku_override_index' );
		$kommiku_settings['directory'] = get_option( 'kommiku_url_index' );
		$kommiku_settings['feed'] = get_option( 'kommiku_url_feed' );
		$kommiku_settings['search'] = get_option( 'kommiku_url_search' );
		$kommiku_settings['counter_enable'] = get_option( 'kommiku_counter' );
		$kommiku_settings['rating_enable'] = get_option( 'kommiku_rating' );
		$kommiku_settings['feed_enable'] = get_option( 'kommiku_feed_enable' );
		$kommiku_settings['search_enable'] = get_option( 'kommiku_search_enable' );
	}
	if($kommiku_settings['kommiku_override_index']) $checkboxOver = ' checked=checked';
	if($kommiku_settings['scanlator_enable']) $checkboxOverTwo = ' checked=checked';
	if($kommiku_settings['counter_enable']) $checkboxOverThree = ' checked=checked';
	if($kommiku_settings['rating_enable']) $checkboxOverFour = ' checked=checked';
	if($kommiku_settings['feed_enable']) $checkboxOverFive = ' checked=checked';
	if($kommiku_settings['search_enable']) $checkboxOverSix = ' checked=checked';
	
	$kommiku_settings['list'] = getFileList(WP_LOAD_PATH.'_kommiku/themes/');
	if($kommiku_settings['url']) $kommiku_settings_url = $kommiku_settings['url'].'/';
	//For no Slug to happen, we need a Series!
	if($kommiku_settings['one_comic'] == 'false') $kommiku_settings['one_comic'] = '';
	if(!$kommiku_settings['scanlator_url']) {
		add_option('kommiku_scanlator', 'author');
		$kommiku_settings['scanlator_url'] = 'author';
		}
		
?>

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><?_e('Kommiku Settings', 'kommiku')?></h2>
	<?php if ($post['pass'] || $kommiku_settings['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204); margin-bottom: 0;"><p><?php echo $post['pass'].$kommiku_settings['error']; ?></p></div>
	<?php } ?>
	<form method="post" action="admin.php?page=kommiku_settings" name="post">
	<input name="what" value="settings" type="hidden"/>
	<input name="action" value="update" type="hidden"/>
	<div class="metabox-holder has-right-sidebar">
		<div id="post-body-content">			
		<div class="metabox-holder">	
		
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Skin', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
					<select name="skin" style="width: 250px;">
						<?php		
						
						if($kommiku_settings['skin'] == "default") {
							echo '<option value="default">Default</option>';
						} else {
							echo '<option value="default">Default</option>';
						};		
							
						foreach ($kommiku_settings['list'] as $row) {
							$option = str_replace(WP_LOAD_PATH.'_kommiku/themes/','',$row);
							$option = $db->trail($option); 
							if($option == $kommiku_settings['skin'])
								echo '<option value="'.$option.'" selected=selected>'.ucwords($option).'</option>';
							else
								echo '<option value="'.$option.'">'.ucwords($option).'</option>';
							};	
						?>	
					</select>	
					<p><?_e('The Skin or Theme for the Comic Reader.', 'kommiku')?></p>		
					</div>
				</div>
			</div>
		
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Upload Directory', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text"><?_e('Upload Directory', 'kommiku')?></label>
						<input style="width: 100%; <?php if($kommiku_settings['fail']['upload']) echo 'background: #ffeeee;'; ?>" type="text" autocomplete="off"  value="<?php echo $kommiku_settings['upload']; ?>" tabindex="1" size="30" name="upload"/>
						<p><?_e('The directory where your comics will be uploaded to.', 'kommiku')?><br/><?_e('Your comics will be uploaded to: ', 'kommiku')?><strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['upload']; ?>/</strong><br/><span style="font-style: italic;"><?_e('* Do not name your Upload Directory and Permalink the Same name.', 'kommiku')?></span></p>
					</div>
				</div>
			</div>

			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Permalink: Comic Base', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="url" class="screen-reader-text"><?_e('Permalink: Comic Base', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['url']; ?>" tabindex="1" size="30" name="url"/>
						<p><?_e('This is where you will view your comic.', 'kommiku')?><br/><?_e('Your current Permalinks to your Comic is:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['url']; ?>/[<?_e('Slug', 'kommiku')?>]</strong><br/><span style="font-style: italic;"><?_e('* Do not name your Upload Directory and Permalink the Same name.', 'kommiku')?></span><br/><span style="font-style: italic;"><?_e('* Blank  base-slug may be buggy.', 'kommiku')?></span></p>
					</div>
				</div>
			</div>
				
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Directory Index URL', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="url" class="screen-reader-text"><?_e('Url to Directory:', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['directory']; ?>" tabindex="1" size="30" name="directory"/>
						<p><?_e('Your current Permalinks to your Directory is:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['directory']; ?>/</strong>
						<br/><?_e('And:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['url']; ?>/</strong>
						<br/><span style="font-style: italic;"><?_e("* This is your Directory Listing.", 'kommiku')?> <?_e("Make sure it does not conflict with any other names or folders in your website.", 'kommiku')?></span></p>
					</div>
				</div>
			</div>
				
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Feed URL', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="url" class="screen-reader-text"><?_e('Url to your Feeds:', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['feed']; ?>" tabindex="1" size="30" name="feed"/>
						<p><?_e('Your current Permalinks to your Directory is:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['feed']; ?>/</strong>
						<br/><span style="font-style: italic;"><?_e("* This is your Feed Listing.", 'kommiku')?> <?_e("Make sure it does not conflict with any other names or folders in your website.", 'kommiku')?></span></p>
						<?_e('Enable this feature:', 'kommiku')?> <input type="checkbox"<?php echo $checkboxOverFive?>  value="1" name="feed_enable"/>
					</div>
				</div>
			</div>
			
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Search URL', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="url" class="screen-reader-text"><?_e('Url to Search:', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['search']; ?>" tabindex="1" size="30" name="search"/>
						<p><?_e('Your current Permalinks to Search is:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['search']; ?>/</strong>
						<br/><span style="font-style: italic;"><?_e("* This is your Search Function.", 'kommiku')?> <?_e("Make sure it does not conflict with any other names or folders in your website.", 'kommiku')?></span></p>
						<?_e('Enable this feature:', 'kommiku')?> <input type="checkbox"<?php echo $checkboxOverSix?>  value="1" name="search_enable"/>
					</div>
				</div>
			</div>
		
		<?php if(file_exists(KOMMIKU_FOLDER.'/extension/scanlator/scanlator.php')){ ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Scanlator and Authors', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="one_comic" class="screen-reader-text"><?_e('Scanlator Features:', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['scanlator_url']; ?>" tabindex="1" size="30" name="scanlator_url"/>
						<p><?_e('Have multiple authors on your Kommiku?', 'kommiku')?><br/><?_e('This feature allows you to give proper credit to your authors.', 'kommiku')?><br/><?_e('Your authors will also get a unique page about them.', 'kommiku')?> <br/><br/><?_e('Permalink:', 'kommiku')?><br/><strong><?php echo get_bloginfo('url'); ?>/<?php echo $kommiku_settings['scanlator_url']; ?>/</strong></p>
						<?_e('Enable this feature:', 'kommiku')?> <input type="checkbox"<?php echo $checkboxOverTwo?>  value="1" name="scanlator_enable"/>

					</div>
				</div>
			</div>
		<?php } ?>

			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('One Story Mode', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="one_comic" class="screen-reader-text"><?_e('Main Story', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['one_comic']; ?>" tabindex="1" size="30" name="one_comic"/>
						<p><?_e('This will switch Kommiku into the "One Story" Mode.', 'kommiku')?><br/><?_e("Type in the <strong>Main Story's slug</strong> to identify the Website's Main Story.", 'kommiku')?><br/><?_e('The Main Story\'s slug will be replace by the "Comic Base" (See Above)', 'kommiku')?><br/><?_e('All other stories will be hidden.', 'kommiku')?><br/><br/><?_e('Example of Permalink:', 'kommiku')?><br/><?_e('With Chapters:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/1/1/</strong><br/><?_e('Chapterless:', 'kommiku')?> <strong><?php echo get_bloginfo('url'); ?>/1/</strong></p>
						<?_e('Override the Index:', 'kommiku')?> <input type="checkbox"<?php echo $checkboxOver?>  value="1" name="override_index"/>

					</div>
				</div>
			</div>
			
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('View Counter System', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<p style="margin-top: 0;"><?_e('Record each visit for each Page and Series read. Enabling this feature may slow down page load.', 'kommiku')?><br/><?_e('* Enabling this feature may slow down page load.', 'kommiku')?></p>
						<span style="font-style: italic;"><?_e('Enable this feature:', 'kommiku')?></span> <input type="checkbox"<?php echo $checkboxOverThree?>  value="1" name="counter_enable"/>
					</div>
				</div>
			</div>
			
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('5 Star Rating System', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<p style="margin-top: 0;"><?_e('Allow the viewers or readers to rate the Page, Series, or Chapters', 'kommiku')?><br/><?_e('* Enabling this feature may slow down page load.', 'kommiku')?></p>
						<span style="font-style: italic;"><?_e('Enable this feature:', 'kommiku')?></span> <input type="checkbox"<?php echo $checkboxOverFour?>  value="1" name="rating_enable"/>
					</div>
				</div>
			</div>
			
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Kommiku Api Key', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="apikey" class="screen-reader-text"><?_e('Key', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?php echo $kommiku_settings['key']; ?>" tabindex="1" size="30" name="apikey"/>
						<p><?_e('Give yourself the special features with the Kommiku Api Key!', 'kommiku')?><br/><?_e('Only given to special people... however.', 'kommiku')?></p>
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