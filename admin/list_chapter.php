<?php	
$chapter_list = $db->chapter_list();
$series = $db->series_detail();
if($chapter_list)
foreach ($chapter_list as $row) {
	if ($row->title) $title = ' - '.stripslashes($row->title);
	$listing[$row->number] = '<li>#'.$row->number.' - <a href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$row->id.'">'.$row->slug.$title.'</a></li>';
	unset($title);
	}	
if ($listing) ksort($listing,SORT_NUMERIC);
switch(rand(0,3)) {
	case 0:
		$deleteWord = __("Don't Do it!", 'kommiku');
		break;
	case 1:
		$deleteWord = __("It's a TRAP!", 'kommiku');
		break;
	case 2:
		$deleteWord = __("Why???!", 'kommiku');
		break;
	case 3:
		$deleteWord = __("But I am your friend :(", 'kommiku');
		break;
		
		}
$scanlator = get_option('kommiku_scanlator_enabled');
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><A href="<?php echo $url; ?>admin.php?page=kommiku"><?_e('Series Listing', 'kommiku')?></a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku&sub=listchapter&series='.$series['id'];?>"><?php echo $series['title']; ?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<form method="post" action="admin.php?page=kommiku&sub=listchapter&series=<?php echo $series['id'];?>" name="post">
				<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>					
				<input type="hidden" value="create" name="action"/>
				<input type="hidden" value="chapter" name="what"/>	
				<input type="hidden" value="chapter" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Create a Chapter', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div style="margin-bottom: 10px;">
									<div class="misc-pub-section ">
										<span <?php if($chapter['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php if($_GET['action'] != 'delete' && !$status['pass']) echo stripslashes($chapter['title']); ?>" style="width: 150px; float: right; text-align: left;" />
										<div class="clear"></div> 
									</div>
									<div class="misc-pub-section ">
										<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter #:', 'kommiku')?></span> <input name="number" type="text" value="<?php if($_GET['action'] != 'delete' && !$status['pass']) echo $chapter['number']; ?>" style="width: 150px; float: right; text-align: left;" />
										<div class="clear"></div> 
									</div>
									<div class="misc-pub-section ">
										<span <?php if($chapter['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php if($_GET['action'] != 'delete' && !$status['pass']) echo $chapter['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
										<div class="clear"></div> 
									</div>
									<div class="misc-pub-section ">
										<?_e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;"><?php if($_GET['action'] != 'delete' && !$status['pass']) echo stripslashes($chapter['summary']); ?></textarea>
										<div class="clear"></div> 
									</div>
								</div>
								<div class="clear"></div>
								<div style="width: 100%; float: right; text-align: right">
									<input type="submit" value="<?_e('Create Chapter', 'kommiku')?>" accesskey="p" tabindex="5" class="button-primary" name="chapter_create"/>
								</div>
								<div class="clear"></div>
							</div>					
						</div>
					</div>
				</div>
				</form>
				<?php if(file_exists(KOMMIKU_ABSPATH.'extension/dumper.php')){ ?>
				<form method="post" action="admin.php?page=kommiku&sub=listchapter&series=<?php echo $series['id'];?>" name="post" enctype="multipart/form-data">
				<input type="hidden" value="<?php echo $series['id'];?>" name="series_id"/>					
				<input type="hidden" value="dump" name="action"/>
				<input type="hidden" value="chapter" name="what"/>	
				<input type="hidden" value="chapter" name="destination"/>		
					<div class="postbox">
						<h3 style="cursor: default;"><span><?_e('Dump a Chapter', 'kommiku')?></span></h3>
						<div class="inside">
							<div class="submitbox">
								<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section ">
											<input type="file" name="zip" size="30" tabindex="1" value="" autocomplete="off" style="background: rgb(238, 238, 238) none repeat scroll 0% 0%; width: 100%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;"/>
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php echo $chapter['title']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter #:', 'kommiku')?></span> <input name="number" type="text" value="<?php echo $chapter['number'];?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?_e('Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php echo $chapter['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
									</div>
								</div>
								<div class="clear"></div>
								<div style="width: 100%; float: right; text-align: right">
									<input type="submit" value="Dump it!" accesskey="p" tabindex="5" class="button-primary" name="chapter_createchapter_create"/>
								</div>
								<div class="clear"></div>
							</div>					
						</div>
					</div>
				</form>
				<?php } ?>
				<form method="post" enctype="multipart/form-data" action="admin.php?page=kommiku&sub=listchapter&series=<?php echo $series['id'];?>" name="post">
				<input type="hidden" value="<?php echo $series['id'];?>" name="series_id"/>
				<input type="hidden" value="update" name="action"/>
				<input type="hidden" value="series" name="what"/>	
				<input type="hidden" value="chapter" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Series Detail', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section">
											<span <?php if($series['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?_e('Series Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php echo $series['title']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?_e('Series Slug:', 'kommiku')?></span> <input name="slug" type="text" value="<?php echo $series['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<?_e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;" /><?php echo stripslashes($series['summary']); ?></textarea>
											<div class="clear"></div> 
										</div>
										<?php if($scanlator){ ?>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['author'])echo 'style="color: #ff0000;"'; ?>><?_e('Author:', 'kommiku')?></span> <input name="author" type="text" value="<?php echo $series['author']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['illustrator'])echo 'style="color: #ff0000;"'; ?>><?_e('Illustrator:', 'kommiku')?></span> <input name="illustrator" type="text" value="<?php echo $series['illustrator']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<?php } ?>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['alternate'])echo 'style="color: #ff0000;"'; ?>><?_e('Date Created:', 'kommiku')?></span> <input name="creation" type="text" value="<?php echo $series['creation']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['alternate'])echo 'style="color: #ff0000;"'; ?>><?_e('Other Names:', 'kommiku')?></span> <input name="alt_name" type="text" value="<?php echo $series['alt_name']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['alternate'])echo 'style="color: #ff0000;"'; ?>><?_e('Categories:', 'kommiku')?></span> <input name="categories" type="text" value="<?php echo $series['categories']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section" style="text-align: right;"> 
											<?_e('Read Direction:', 'kommiku')?>  
											<select name="read">
												<option <?php  if($series['read'] == '0') echo 'selected="selected"'; ?>value="0"><?_e('Left to Right', 'kommiku')?></option>
												<option <?php  if($series['read'] == '1') echo 'selected="selected"'; ?>value="1"><?_e('Right to Left', 'kommiku')?></option>
												<option <?php  if($series['read'] == '2') echo 'selected="selected"'; ?>value="2"><?_e('Top to Bottom', 'kommiku')?></option>
											</select>
										</div>
										<div class="misc-pub-section" style="text-align: right;">
											<?_e('Status: ', 'kommiku')?>
											<select name="status">
												<option <?php if($series['status'] == '0') echo 'selected="selected"'; ?>value="0"><?_e('Unknown', 'kommiku')?></option>
												<option <?php if($series['status'] == '1') echo 'selected="selected"'; ?>value="1"><?_e('Ongoing', 'kommiku')?></option>
												<option <?php if($series['status'] == '2') echo 'selected="selected"'; ?>value="2"><?_e('On-Hold', 'kommiku')?></option>
												<option <?php if($series['status'] == '3') echo 'selected="selected"'; ?>value="3"><?_e('Dropped', 'kommiku')?></option>
												<option <?php if($series['status'] == '4') echo 'selected="selected"'; ?>value="4"><?_e('Complete', 'kommiku')?></option>
											</select>
										</div>	
										<div class="misc-pub-section" style="text-align: right;">
											<?_e('Story Type:', 'kommiku')?> 
											<select name="type">
												<option <?php if($series['type'] == '0') echo 'selected="selected"'; ?>value="0"><?_e('(_blank)', 'kommiku')?></option>
												<option <?php if($series['type'] == '1') echo 'selected="selected"'; ?>value="1"><?_e('Manga', 'kommiku')?></option>
												<option <?php if($series['type'] == '2') echo 'selected="selected"'; ?>value="2"><?_e('Manhwa', 'kommiku')?></option>
												<option <?php if($series['type'] == '3') echo 'selected="selected"'; ?>value="3"><?_e('Manhua', 'kommiku')?></option>
												<option <?php if($series['type'] == '4') echo 'selected="selected"'; ?>value="4"><?_e('Comic', 'kommiku')?></option>
												<option <?php if($series['type'] == '5') echo 'selected="selected"'; ?>value="5"><?_e('Unknown', 'kommiku')?></option>
												<option <?php if($series['type'] == '6') echo 'selected="selected"'; ?>value="6"><?_e('Novel', 'kommiku')?></option>
											</select>
										</div>	
										<?php //For Mature Series?
											if($matureEnable) { ?>
										<div class="misc-pub-section">
											<span <?php if($series['fail']['rating'])echo 'style="color: #ff0000;"'; ?>><?_e('Age Rating:', 'kommiku')?></span> <input name="rating" type="text" value="<?php echo $series['rating']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<?php } ?>
										<div class="misc-pub-section "><?_e('Series Book Cover:', 'kommiku')?> 
										<?php if($series['img']) echo '<strong>[Exist]</strong>'; ?>
										<br/><br/>
											<input type="file" style="background: none repeat scroll 0% 0% rgb(238, 238, 238); width: 100%; -moz-background-inline-policy: continuous;" autocomplete="off" value="" tabindex="1" size="30" name="img">
										</div>
									</div>
									<div class="clear"></div>
									<div style="width: 100%; float: right; text-align: right">
											<input type="submit" value="Update Series" accesskey="p" tabindex="5" class="button-primary" name="series_update"/>
									</div>
									<div class="clear"></div>
							</div>							
						</div>
					</div>
				</div>
				</form>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id']; ?>"><?_e('Delete Series!', 'kommiku')?></a>
								</div>
								<div class="clear"></div>									
							</div>								    
						</div>
					</div>
				</div>
			</div>
		</div>
	
	
	
		<div id="post-body-content">
			<div>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Chapter Listings', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div id="titlediv" style="margin: 0;">
								<div id="titlewrap">
									<ul>
										<?php
										if($listing)								
											foreach ($listing as $list) {
												echo $list;
											}
										else
											_e('There are no chapters in this series.', 'kommiku');?>
									</ul>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>										
		</div>
		
	</div>
</div>