<?php	
#if(!is_numeric($_GET['series']) && !is_numeric($_GET['chapter']) && !$_GET['pg'] )
#die('The EF?!');

$action = '&action=create';
$chapter_id = $_GET['chapter'];
if(($page['id'] || $_GET['pg']) && $_GET['series']) {
	$page = $db->page_detail($page['id']);
	$action = '&action=update&pg='.$page['id'];
}

if (is_numeric($_GET["series"])) {
	$chapterless = $db->chapterless();
	if ($chapterless) $chapter_id = 0;
}

$series = $db->series_detail($page['series_id']);
$chapter = $db->chapter_detail($page['chapter_id']);
$folder = "/images/comic/".$series['slug'].'/'.$chapter['slug'];
if ($listing) ksort($listing,SORT_NUMERIC);

$pageNumber = $db->page_number($_GET['series'],$chapter_id);

if(!isset($page['slug'])) $page['slug'] = $pageNumber;
if(!is_numeric($page['number'])) $page['number'] = $pageNumber;
if($page['id']) $pageTitle  = ' &raquo; Page '.$page['number'];
if($series['chapterless'] == 0) $chapter_number = $chapter['slug'];
if($chapter['id']) $chapterTitle = '&raquo; <a href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$chapter['id'].'">Chapter '.$chapter_number.'</a>';
if($chapter['id']) $chapterURL = '&amp;chapter='.$chapter['id'];
$publishWord = __("Publish", 'kommiku');
if ($chapterless == 0) { 
	$chapterURL = '&chapter='.$chapter["id"]; 
	$chapterWord = 'Chapter';
	$sub = '&sub=listchapter';
} else {
	$chapterWord = 'Series';	
	$sub = '&sub=listpage';
}


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
		
if(KOMMIKU_URL_FORMAT) $seriesUrl = KOMMIKU_URL_FORMAT.'/';
$seriesSlug = strtolower($series['slug']).'/';
if(get_option('kommiku_no_slug') == 'true') unset($series['slug']);
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><A href="<?php echo $url; ?>admin.php?page=kommiku"><?_e('Series Listing', 'kommiku')?></a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku'.$sub.'&series='.$series['id'];?>"><?php echo $series['title']; ?></a> <?php echo $chapterTitle.$pageTitle; ?></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<form method="post" action="admin.php?page=kommiku&sub=createpage<?php echo $action; ?>&series=<?php echo $series['id']; ?><?php echo $chapterURL; ?>" name="post" enctype="multipart/form-data">
	<div class="metabox-holder has-right-sidebar">
		<?php if ($page['id'] || $_GET['pg']) { ?>
		<input type="hidden" value="update" name="action"/>
		<input type="hidden" value="<?php echo $page['id']; ?>" name="page_id"/>
		<?php $publishWord = __("Update", 'kommiku'); } else { ?>
		<input type="hidden" value="create" name="action"/>
		<?php } ?>
		<input type="hidden" value="page" name="what"/>	
		<input type="hidden" value="page_create" name="destination"/>	
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div class="postbox">
					<h3 style="cursor: default;"><span><?_e('Publishing', 'kommiku')?></span></h3>
					<div class="inside">
						<div class="submitbox" style="padding: 5px;">
							<div style="background: none; font-size: 11px;">
								<div class="misc-pub-section ">
									<input type="file" name="img" size="30" tabindex="1" value="" autocomplete="off" style="background: rgb(238, 238, 238) none repeat scroll 0% 0%; width: 100%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;"/>
								</div>
								<div class="misc-pub-section ">
									<?_e('Series:', 'kommiku')?> <strong><?php echo $series['title']; ?></strong>
									<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
									<div class="clear"></div> 
								</div>
								<?php if(is_numeric($chapter['number'])){ ?>
								<div class="misc-pub-section ">
									<?_e('Chapter:', 'kommiku')?> <strong><?php echo $chapter['number']; ?></strong>
									<input type="hidden" value="<?php echo $chapter['id']; ?>" name="chapter_id"/>
									<div class="clear"></div> 
								</div>
								<?php } ?>
								<div class="misc-pub-section ">
									<?_e('Page #:', 'kommiku')?> <input type="text" name="number" value="<?php echo $page['number']; ?>" style="width: 180px; float: right; text-align: left;" />
									<div class="clear"></div> 
								</div>
								<div style="width: 100%; float: right; text-align: right">
									<input style="margin-top:10px; width: 100px;" type="submit" value="<?php echo $publishWord; ?>" accesskey="p" tabindex="5" class="button-primary" name="publish"/>
								</div>
								<div class="clear"></div>
								<div class="misc-pub-section "></div>
							</div>								    
						</div>
					</div>
				</div>
				
				<?php if ($page['id'] || $_GET['pg']) { ?>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id'] ?>&amp;pg=<?php echo $page['id'].$chapterURL; ?>"><?_e('Delete Page!', 'kommiku')?></a>
								</div>
								<div class="clear"></div>									
							</div>								    
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	
		<div id="post-body-content">
			<div class="postbox" style="margin-bottom: 0px;">
				<h3 style="cursor: default;"><span><?_e('Title', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text"><?_e('Title', 'kommiku')?></label>
						<input style="width: 100%;" type="text" autocomplete="off" id="title" value="<?php echo $page['title']; ?>" tabindex="1" size="30" name="title"/>
					</div>
				</div>
			</div>			
			
			<div style="margin-bottom: 10px;">
				<div class="inside">
					<div id="edit-slug-box">
						<strong><?_e('Permalink:', 'kommiku')?></strong> <span id="sample-permalink"><?php echo HTTP_HOST.$seriesUrl.$seriesSlug.$db->trailingslash($chapter_number); ?>
						<input type="text" value="<?php echo $page['slug']; ?>" name="slug" style="width: 10%; background: #FFFBCC;" />
						</span>
					</div>
				</div>
			</div>

			
		<div class="metabox-holder">
				
		<?php if($page['img'] && $series['slug']){ ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('The Page', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px; overflow-x: scroll; text-align: center;">
						<?php echo '<img src="'.UPLOAD_URLPATH.'/'.strtolower($series['slug']).'/'.$db->trailingslash($chapter_number).$page['img'].'" />'; ?>
					</div>
				</div>
			</div>
		<?php } ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Story', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="story" class="screen-reader-text"><?_e('story', 'kommiku')?></label>
						<textarea tabindex="2" name="story" style="width: 99.5%;" rows="5"><?php echo stripslashes($page['story']); ?></textarea>									
						<p><?_e('Add a comment, description, or summary.', 'kommiku')?></p>
					</div>
				</div>
			</div>
						
			<div class="postbox">
				<h3 style="cursor: default;"><span><?_e('Wordpress Post Connect', 'kommiku')?></span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="seodescription" class="screen-reader-text">wp_post_slug</label>
						<input style="width: 100%;" type="text" autocomplete="off" id="title" value="<?php echo $page['wp_post_slug']; ?>" tabindex="1" size="30" name="wp_post_slug"/>
						<p><?_e('Connect a Wordpress Post to this Page.', 'kommiku')?></p>
					</div>
				</div>
			</div>
			
		</div>
	</div>
		
	</form>
</div>