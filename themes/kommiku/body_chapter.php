<?php kommiku_header(); 

foreach ($series_chapter as $item) {
	$thedate = $wpdb->get_var("SELECT pubdate FROM ".$wpdb->prefix."comic_history WHERE type = 'chapter' AND type_id = '".$item->id."'");
	if ($item->title) $chapterTitle = ' - '.$item->title;
	$chapterListing = '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->number.'/">Chapter '.$item->number.$chapterTitle.'</a><span style="float: right;">'.strftime('%D',strtotime($thedate)).'</span></li>'.$chapterListing;
}


function cmp( $a, $b )
{ 
  if(  $a->pubdate ==  $b->pubdate ){ return 0 ; } 
  return ($a->pubdate < $b->pubdate) ? -1 : 1;
} 


if ($wpdb->get_var("SELECT value FROM `".$wpdb->prefix."comic_options"."` WHERE option_name = 'show_page_update' AND type = 'series' AND type_id = '".$series['id']."'") || $series['chapterless']) {
	$series_pages = $db->series_pages($series['id']);
	if($series['chapterless']) sort($series_pages); else usort($series_pages,'cmp');
	foreach ($series_pages as $item) { 
		if (isset($item->chapterNumber)) {
			$pageUpdate = '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->chapterNumber.'/'.$item->pageSlug.'/">Chapter '.$item->chapterNumber.' - Page '.$item->pageNumber.'</a><span style="float: right;">'.strftime('%D',strtotime($item->pubdate)).'</span></li>'.$pageUpdate;
		} else {
			$pageUpdate = '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->pageSlug.'/">Page '.$item->pageNumber.'</a><span style="float: right;">'.strftime('%D',strtotime($item->pubdate)).'</span></li>'.$pageUpdate;
		} 
	}
}
?>

<div id="content" class="narrowcolumn home">
	<div class="breadcrumb">
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=KOMMIKU_URL_FORMAT?>/">Story Listings</a></h2> &raquo; 
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?>"><?=$kommiku["title"]["series"]?></a></h2> 
	</div>
    <div class="column">
    	<p><?php echo stripslashes($series["summary"]); ?></p>
    	<?php if(!$series["chapterless"]) { ?> 
	    	<h2>Chapter Updates</h2>
	    	<ul>
				<?php echo $chapterListing; ?>
			</ul>
		<?php } ?>
	</div>
	
	<?php if ($pageUpdate) { ?>
	<div class="column">
		<h2 style="margin:0;">Page Updates</h2>
		<ul>
			<?php echo $pageUpdate; ?>
		</ul>
	</div><?php } ?>
</div>

<?php kommiku_footer(); ?>
