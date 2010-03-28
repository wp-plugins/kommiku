<?php	
$page_list = $db->page_list($page['series_id'],$page['chapter_id']);
if(!$series) $series = $db->series_detail();
if(!$chapter) $chapter = $db->chapter_detail();
if($page_list)
foreach ($page_list as $row) {
	if ($row->title) $title = " - ".$row->title;
	$listing[$row->number] = '<li><A href="'.$url.'admin.php?page=kommiku&sub=createpage&series='.$series["id"].'&chapter='.$chapter["id"].'&pg='.$row->id.'">'.$row->number.$title.'</a></li>';
	unset($title);
	}	
$chapter_number = str_replace('.0','',$chapter['number']);
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
		

		
if (is_numeric($_GET["series"])) $chapterless = $db->chapterless();
if ($chapterless == 0) { 
	$chapterTitle = ' &raquo; <a href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$chapter["id"].'">Chapter '.$chapter_number.'</a>';	
	$chapterURL = '&chapter='.$chapter["id"]; 
	$chapterWord = 'Chapter';
	$sub = '&sub=listchapter';
} else {
	$chapterWord = 'Series';
	$sub = '&sub=listpage';
}
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><A href="<?=$url?>admin.php?page=kommiku">Series Listing</a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku'.$sub.'&series='.$series['id'];?>"><?php echo $series['title']; ?></a><?php echo $chapterTitle; ?></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['error'].$status['pass']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">	
				<div class="postbox">
					<h3 style="cursor: default;"><span>Create a Page</span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
									<div class="clear"></div>
									<div style="padding: 10px 0; width: 100%; float: right; text-align: right">
										<a href="<?php echo $url; ?>admin.php?page=kommiku&sub=createpage&series=<?php echo $series['id']; ?><?php echo $chapterURL; ?>" class="button-primary">Create a Page</a>
									</div>
									<div class="clear"></div>
							</div>								    
						</div>
					</div>
				</div>
				<?php if($chapter){ ?>
				<form method="post" action="admin.php?page=kommiku&sub=listpage&series=<?php echo $series['id']; ?>&chapter=<?=$chapter['id']?>" name="post">
				<input type="hidden" value="<?=$series['id']?>" name="series_id"/>
				<input type="hidden" value="<?=$chapter['id']?>" name="chapter_id"/>
				<input type="hidden" value="update" name="action"/>
				<input type="hidden" value="chapter" name="what"/>
				<input type="hidden" value="page" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span>Chapter Detail</span></h3>
					<div class="inside">
						<div class="submitbox">
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
							<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['title'])echo 'style="color: #ff0000;"'; ?>>Chapter Name:</span> <input name="title" type="text" value="<?php echo $chapter['title']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>>Chapter #:</span> <input name="number" type="text" value="<?php echo $chapter_number; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											Summary: <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;"><?php echo stripslashes($chapter['summary']); ?></textarea>
											<div class="clear"></div> 
										</div>
										<?php if(!$series['chapterless']){ ?>
										<div class="misc-pub-section ">
											Show Page Updates <input type="checkbox" value="1" <?php if($series['show_page_update']) echo 'checked="checked"'; ?>name="show_page_update" style="min-width: 0pt;"/> 
										</div>
										<?php } ?>
									</div>
									<div class="clear"></div>
									<div style="width: 100%; float: right; text-align: right;">
											<input type="submit" value="Update Chapter" accesskey="c" tabindex="5" class="button-primary" name="series_update"/>
									</div>
									<div class="clear"></div>
							</div>								    
						</div>
					</div>
				</div>
				</form>
				<?php } ?>
				<form method="post" action="admin.php?page=kommiku&sub=listpage&series=<?php echo $series['id']; ?>&chapter=<?php echo $chapter['id']; ?>" name="post">
				<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
				<input type="hidden" value="<?php echo $chapter['id']; ?>" name="chapter_id"/>
				<input type="hidden" value="update" name="action"/>
				<input type="hidden" value="series" name="what"/>
				<input type="hidden" value="page" name="destination"/>		
				<div class="postbox">
					<h3 style="cursor: default;"><span>Series Detail</span></h3>
					<div class="inside">
						<div class="submitbox">
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
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
									</div>
									<div class="clear"></div>
									<div style="width: 100%; float: right; text-align: right">
											<input type="submit" value="Update Series" accesskey="c" tabindex="5" class="button-primary" name="series_update"/>
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
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id']; ?>&amp;chapter=<?php echo $chapter['id']; ?>">Delete <?php echo $chapterWord; ?>!</a>
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
					<h3 style="cursor: default;"><span>Page Listing</span></h3>
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
								echo 'There are no page in this Chapter.';
							
								?>
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