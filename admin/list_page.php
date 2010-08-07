<?php	
$series = $db->series_detail();
$page_list = $db->page_list($page['series_id'],$page['chapter_id']);
if(!$chapter) $chapter = $db->chapter_detail();
if($page_list)
foreach ($page_list as $row) {
	if ($row->title) $title = " - ".stripslashes($row->title);
	$listing[$row->number] = '<li>#'.$row->number.' - <a href="'.$url.'admin.php?page=kommiku&sub=createpage&series='.$series["id"].'&chapter='.$chapter["id"].'&pg='.$row->id.'">'.$row->slug.'</a></li>';
	unset($title);
	}	
$chapter_number = str_replace('.0','',$chapter['number']);
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
		
if (is_numeric($_GET["series"])) $chapterless = $db->chapterless();
if ($chapterless == 0) { 
	$chapterTitle = ' &raquo; <a href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$chapter["id"].'">'.__("Chapter",'kommiku').' '.$chapter_number.'</a>';	
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
	<h2><a href="<?=$url?>admin.php?page=kommiku"><?_e('Series Listing', 'kommiku')?></a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku'.$sub.'&series='.$series['id'];?>"><?php echo $series['title']; ?></a><?php echo $chapterTitle; ?></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['error'].$status['pass']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">	
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Create a Page', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
									<div class="clear"></div>
									<div style="padding: 10px 0; width: 100%; float: right; text-align: right">
										<a href="<?php echo $url; ?>admin.php?page=kommiku&sub=createpage&series=<?php echo $series['id']; ?><?php echo $chapterURL; ?>" class="button-primary"><?_e('Create a Page', 'kommiku')?></a>
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
					<h3 style="cursor: default;"><span><?_e('Chapter Detail', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
							<div style="background: none;">
									<div style="margin-bottom: 10px;">
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['title'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter Name:', 'kommiku')?></span> <input name="title" type="text" value="<?php echo stripslashes($chapter['title']); ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter #:', 'kommiku')?></span> <input name="number" type="text" value="<?php echo $chapter['number']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['slug'])echo 'style="color: #ff0000;"'; ?>><?_e('Chapter Slug', 'kommiku')?>:</span> <input name="slug" type="text" value="<?php echo $chapter['slug']; ?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<?_e('Summary:', 'kommiku')?> <textarea name="summary" type="text" style="width: 150px; float: right; text-align: left;"><?php echo stripslashes($chapter['summary']); ?></textarea>
											<div class="clear"></div> 
										</div>
										<?php if($scanlator){ ?>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['scanlator'])echo 'style="color: #ff0000;"'; ?>><?_e('Scanlator:', 'kommiku')?></span> <input name="scanlator" type="text" value="<?=$chapter['scanlator']?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['scanlator_slug'])echo 'style="color: #ff0000;"'; ?>><?_e('Scanlator Slug:', 'kommiku')?></span> <input name="scanlator_slug" type="text" value="<?=$chapter['scanlator_slug']?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
										<?php } else {?>
											<input type="hidden" value="" name="scanlator"/>
											<input type="hidden" value="" name="scanlator_slug"/>
										<?php } ?>
										<div class="misc-pub-section ">
											<span <?php if($chapter['fail']['number'])echo 'style="color: #ff0000;"'; ?>><?_e('Volume:', 'kommiku')?></span> <input name="volume" type="text" value="<?=$chapter['volume']?>" style="width: 150px; float: right; text-align: left;" />
											<div class="clear"></div> 
										</div>
									</div>
									<div class="clear"></div>
									<div style="width: 100%; float: right; text-align: right;">
											<input type="submit" value="<?_e('Update Chapter', 'kommiku')?>" accesskey="c" tabindex="5" class="button-primary" name="series_update"/>
									</div>
									<div class="clear"></div>
							</div>								    
						</div>
					</div>
				</div>
				</form>
				<?php } ?>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id']; ?>&amp;chapter=<?php echo $chapter['id']; ?>"><?_e('Delete', 'kommiku')?> <?php echo $chapterWord; ?>!</a>
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
					<h3 style="cursor: default;"><span><?_e('Page Listing', 'kommiku')?></span></h3>
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
								_e("There are no page in this Chapter.", 'kommiku');
							
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