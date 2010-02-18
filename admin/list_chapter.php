<?php	
$chapter_list = $db->chapter_list();
$series = $db->series_detail();
if($chapter_list)
foreach ($chapter_list as $row) {
	if ($row->title) $title = ' - '.$row->title;
	$listing[$row->number] = '<li><A href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$row->id.'">'.$row->number.$title.'</a></li>';
	unset($title);
	}	
if ($listing) ksort($listing,SORT_NUMERIC);
switch(rand(0,3)) {
	case 0:
		$deleteWord = 'Don\'t Do it!';
		break;
	case 1:
		$deleteWord = 'It\'s a TRAP!';
		break;
	case 2:
		$deleteWord = 'Why???!';
		break;
	case 3:
		$deleteWord = 'But I am your friend :(';
		break;
		
		}
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><A href="<?php echo $url; ?>admin.php?page=kommiku">Series Listing</a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku&sub=listchapter&series='.$series['id'];?>"><?php echo $series['title']; ?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<form method="post" action="admin.php?page=kommiku&sub=listchapter&series=<?=$series['id']?>" name="post">
				<input type="hidden" value="<?=$series['id']?>" name="series_id"/>					
				<input type="hidden" value="create" name="action"/>
				<input type="hidden" value="chapter" name="what"/>	
				<input type="hidden" value="chapter" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span>Create a Chapter</span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div style="margin-bottom: 10px;">
									<div class="misc-pub-section ">
										<span <?php if($chapter['fail']['title'])echo 'style="color: #ff0000;"'; ?>>Chapter Name:</span> <input name="title" type="text" value="<?php if($_GET['action'] != 'delete' && !$status['pass']) echo $chapter['title']; ?>" style="width: 150px; float: right; text-align: left;" />
										<div class="clear"></div> 
									</div>
									<div class="misc-pub-section ">
										<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>>Chapter #:</span> <input name="number" type="text" value="<?php if($_GET['action'] != 'delete' && !$status['pass']) echo $chapter['number']; ?>" style="width: 150px; float: right; text-align: left;" />
										<div class="clear"></div> 
									</div>
									<div class="misc-pub-section ">
										Summary: <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;"><?php if($_GET['action'] != 'delete' && !$status['pass']) echo stripslashes($chapter['summary']); ?></textarea>
										<div class="clear"></div> 
									</div>
								</div>
								<div class="clear"></div>
								<div style="width: 100%; float: right; text-align: right">
									<input type="submit" value="Create Chapter" accesskey="p" tabindex="5" class="button-primary" name="chapter_create"/>
								</div>
								<div class="clear"></div>
							</div>					
						</div>
					</div>
				</div>
				</form>
				<?php if(file_exists(KOMMIKU_ABSPATH.'extension/dumper.php')){ ?>
				<form method="post" action="admin.php?page=kommiku&sub=listchapter&series=<?=$series['id']?>" name="post" enctype="multipart/form-data">
				<input type="hidden" value="<?=$series['id']?>" name="series_id"/>					
				<input type="hidden" value="dump" name="action"/>
				<input type="hidden" value="chapter" name="what"/>	
				<input type="hidden" value="chapter" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span>Dump a Chapter</span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div style="margin-bottom: 10px;">
								<div class="misc-pub-section ">
									<input type="file" name="zip" size="30" tabindex="1" value="" autocomplete="off" style="background: rgb(238, 238, 238) none repeat scroll 0% 0%; width: 100%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;"/>
								</div>
								</div>
								<div class="clear"></div>
								<div style="width: 100%; float: right; text-align: right">
									<input type="submit" value="Dump it!" accesskey="p" tabindex="5" class="button-primary" name="chapter_create"/>
								</div>
								<div class="clear"></div>
							</div>					
						</div>
					</div>
				</div>
				</form>
				<?php } ?>
				<form method="post" action="admin.php?page=kommiku&sub=listchapter&series=<?=$series['id']?>" name="post">
				<input type="hidden" value="<?=$series['id']?>" name="series_id"/>
				<input type="hidden" value="update" name="action"/>
				<input type="hidden" value="series" name="what"/>	
				<input type="hidden" value="chapter" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span>Series Detail</span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section ">
											<span <?php if($series['fail']['title'])echo 'style="color: #ff0000;"'; ?>>Series Name:</span> <input name="title" type="text" value="<?php echo $series['title']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($series['fail']['slug'])echo 'style="color: #ff0000;"'; ?>>Series Slug:</span> <input name="slug" type="text" value="<?php echo $series['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											Summary: <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;" /><?php echo stripslashes($series['summary']); ?></textarea>
											<div class="clear"></div> 
										</div>
										<?php if(!$series['chapterless']){ ?>
										<div class="misc-pub-section ">
											Show Page Updates <input type="checkbox" value="1" <?php if($series['show_page_update']) echo 'checked="checked"'; ?>name="show_page_update" style="min-width: 0pt;"/> 
										</div>
										<?php } ?>
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
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id']; ?>">Delete Series!</a>
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
					<h3 style="cursor: default;"><span>Chapter Listings</span></h3>
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
											echo 'There are no chapters in this series.'; ?>
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