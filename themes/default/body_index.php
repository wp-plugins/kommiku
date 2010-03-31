<?php kommiku_header(); ?>

<div id="content" class="narrowcolumn home">
    <div class="column">
    	<h2>Series Listing</h2>
		<ul>
        	<?php echo $kommiku['series_list']; ?>
    	</ul>
	</div>
	<div class="column">
		<h2>Chapter Updates</h2>
		<ul>
		<?php if (is_array($chapterUpdates)) { ?>
			<?php foreach ($chapterUpdates as $item) {?>
				<li>
					<a href="<?php echo HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$item->series_slug.'/'.$item->chapter_slug.'/'; ?>"><?php echo $item->series_name." - Chapter ".$item->chapter_slug; ?></a>
					<span style="float: right;"><?php echo strftime('%D',strtotime($item->pubdate)); ?></span>
				</li>
			<?php } }?>
		</ul>
	</div>
	<div class="column">
		<h2>Page Updates</h2>
			<ul>
			<?php if (is_array($pageUpdates)) { ?>
				<?php foreach ($pageUpdates as $item) {?>
					<?php if ($item->chapter_slug == '0') { ?>
					<li>
						<a href="<?php echo HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$item->series_slug.'/'.$item->page_slug.'/'; ?>"><?php echo $item->series_name." - Page ".$item->page_slug; ?></a>
						<span style="float: right;"><?php echo strftime('%D',strtotime($item->pubdate)); ?></span>
					</li>
				<?php } else if (isset($item->chapter_slug)) { ?>
					<li>
						<a href="<?php echo HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$item->series_slug.'/'.$item->chapter_slug.'/'.$item->page_slug.'/'; ?>"><?php echo $item->series_name." - Ch ".$item->chapter_slug." - Page ".$item->page_slug; ?></a>
						<span style="float: right;"><?php echo strftime('%D',strtotime($item->pubdate)); ?></span>
					</li>
				<?php } } }?>
			</ul>
	</div>
</div>

<?php kommiku_footer(); ?>
