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
	
$chapter_number = str_replace('.0','',$chapter['number']);
$folder = "/images/comic/".strtolower($series['title']).'/'.$chapter_number;
if ($listing) ksort($listing,SORT_NUMERIC);

$pageNumber = $db->page_number($_GET['series'],$chapter_id);

if(!isset($page['slug'])) $page['slug'] = $pageNumber;
if(!is_numeric($page['number'])) $page['number'] = $pageNumber;
if($page['id']) $pageTitle  = ' &raquo; Page '.$page['number'];
if($chapter['id']) $chapterTitle = '&raquo; <a href="'.$url.'admin.php?page=kommiku&sub=listpage&series='.$series['id'].'&chapter='.$chapter['id'].'">Chapter '.$chapter_number.'</a>';
if($chapter['id']) $chapterURL = '&amp;chapter='.$chapter['id'];
$publishWord = "Publish";
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
		
if(KOMMIKU_URL_FORMAT) $seriesUrl = KOMMIKU_URL_FORMAT.'/';
if(kommiku_no_slug) unset($series["slug"]);
if($series["slug"]) $seriesSlug = strtolower($series['slug']).'/';
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><A href="<?php echo $url; ?>admin.php?page=kommiku">Series Listing</a> &raquo; <a href="<?php echo $url.'admin.php?page=kommiku'.$sub.'&series='.$series['id'];?>"><?php echo $series['title']; ?></a> <?php echo $chapterTitle.$pageTitle; ?></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<form method="post" action="admin.php?page=kommiku&sub=createpage<?php echo $action; ?>&series=<?php echo $series['id']; ?><?php echo $chapterURL; ?>" name="post" enctype="multipart/form-data">
	<div class="metabox-holder has-right-sidebar">
		<?php if ($page['id'] || $_GET['pg']) { ?>
		<input type="hidden" value="update" name="action"/>
		<input type="hidden" value="<?php echo $page['id']; ?>" name="page_id"/>
		<?php $publishWord = 'Update'; } else { ?>
		<input type="hidden" value="create" name="action"/>
		<?php } ?>
		<input type="hidden" value="page" name="what"/>	
		<input type="hidden" value="page_create" name="destination"/>	
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div class="postbox">
					<h3 style="cursor: default;"><span>Publishing</span></h3>
					<div class="inside">
						<div class="submitbox" style="padding: 5px;">
							<div style="background: none; font-size: 11px;">
								<div class="misc-pub-section ">
									<input type="file" name="img" size="30" tabindex="1" value="" autocomplete="off" style="background: rgb(238, 238, 238) none repeat scroll 0% 0%; width: 100%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;"/>
								</div>
								<div class="misc-pub-section ">
									Series: <strong><?php echo $series['title']; ?></strong>
									<input type="hidden" value="<?php echo $series['id']; ?>" name="series_id"/>
									<div class="clear"></div> 
								</div>
								<?php if(is_numeric($chapter['number'])){ ?>
								<div class="misc-pub-section ">
									Chapter: <strong><?php echo $chapter['number']; ?></strong>
									<input type="hidden" value="<?php echo $chapter['id']; ?>" name="chapter_id"/>
									<div class="clear"></div> 
								</div>
								<?php } ?>
								<div class="misc-pub-section ">
									Page #: <input type="text" name="number" value="<?php echo $page['number']; ?>" style="width: 180px; float: right; text-align: left;" />
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
				
				<div class="postbox">
					<h3 style="cursor: default;"><span>Options</span></h3>
					<div class="inside">
						<div class="submitbox" style="padding: 5px;">
							<div style="background: none; font-size: 11px;">
								<div class="clear"></div>
								<div class="misc-pub-section ">
									<input style="min-width: 0;" type="checkbox" name="show_date" <?php if ($page['show_date']) echo 'checked="checked" '; ?>value="1"/> Show Date
								</div>
								<div class="misc-pub-section ">																															
									<input style="min-width: 0;" type="checkbox" name="show_title" <?php if ($page['show_title']) echo 'checked="checked" '; ?>value="1"/> Show Title of the Page	
								</div>
								<div class="misc-pub-section ">
									<input style="min-width: 0;" type="checkbox" name="show_first" <?php if ($page['show_first']) echo 'checked="checked" '; ?>value="1"/> Link First Page of Chapter
								</div>
								<div class="misc-pub-section ">											
									<input style="min-width: 0;" type="checkbox" name="show_one" <?php if ($page['show_one']) echo 'checked="checked" '; ?>value="1"/> Link to First Page of Series
								</div>
								<div class="misc-pub-section ">											
									<input style="min-width: 0;" type="checkbox" name="show_last" <?php if ($page['show_last']) echo 'checked="checked" '; ?>value="1"/> Link to the Latest Page of the Series
								</div>
								<div class="misc-pub-section ">
									<input style="min-width: 0;" type="checkbox" name="show_comment" <?php if ($page['show_comment']) echo 'checked="checked" '; ?>value="1"/> Link to Discussion on Chapter
								</div>
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
									<a class="button-primary" href="admin.php?page=kommiku&amp;sub=delete&amp;series=<?php echo $series['id'] ?>&amp;pg=<?php echo $page['id'].$chapterURL; ?>">Delete Page!</a>
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
				<h3 style="cursor: default;"><span>Title</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="title" class="screen-reader-text">Title</label>
						<input style="width: 100%;" type="text" autocomplete="off" id="title" value="<?php echo $page['title']; ?>" tabindex="1" size="30" name="title"/>
					</div>
				</div>
			</div>			
			
			<div style="margin-bottom: 10px;">
				<div class="inside">
					<div id="edit-slug-box">
						<strong>Permalink:</strong> <span id="sample-permalink"><?php echo HTTP_HOST.$seriesUrl.$seriesSlug.$db->trailingslash($chapter_number); ?>
						<input type="text" value="<?php echo $page['slug']; ?>" name="slug" style="width: 10%; background: #FFFBCC;" />
						</span>
					</div>
				</div>
			</div>

			
		<div class="metabox-holder">
				
		<?php if($page['img']){ ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span>The Page</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px; overflow-x: scroll; text-align: center;">
						<?php echo '<img src="'.UPLOAD_URLPATH.'/'.strtolower($series['slug']).'/'.$db->trailingslash($chapter_number).$page['img'].'" />'; ?>
					</div>
				</div>
			</div>
		<?php } ?>
			<div class="postbox">
				<h3 style="cursor: default;"><span>Story</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="story" class="screen-reader-text">story</label>
						<textarea tabindex="2" name="story" style="width: 99.5%;" rows="5"><?php echo $page['story']; ?></textarea>									
						<p>Add a comment, description, or summary.</p>
					</div>
				</div>
			</div>
			
			<div class="postbox">
				<h3 style="cursor: default;"><span>SEO Keywords</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="seokeyword" class="screen-reader-text">seokeyword</label>
						<textarea tabindex="2" name="seokeyword" style="width: 99.5%;" rows="1"><?php echo $page['seokeyword']; ?></textarea>					
						<p>These are like tags on your Wordpress post.</p>
					</div>
				</div>
			</div>

			<div class="postbox">
				<h3 style="cursor: default;"><span>SEO Description</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="seodescription" class="screen-reader-text">seodescription</label>
						<textarea tabindex="2" name="seodescription" style="width: 99.5%;" rows="1"><?php echo $page['seodescription']; ?></textarea>					
					</div>
				</div>
			</div>
		</div>
	</div>
		
	</form>
</div>