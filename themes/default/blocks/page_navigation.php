<?php global $kommiku; ?>
<div class="manga-navi">
	<span class="previousLink"><?php prevPage(true,'[Previous]'); ?></span>
	<span class="nextLink"><?php nextPage(true,'[Next]'); ?></span>
	<?php if($kommiku['chapterOption']){ ?> 
		Chapter 
			<select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.WORDPRESS_URL_ROOT.KOMMIKU_URL_FORMAT; echo "/".$series['slug']; ?>/'+this.value+'/';" name="Chapters" class="viewerChapter">
				<?=$kommiku['chapterOption']?>
			</select>
	<?php } ?>
		Page 
			<select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.WORDPRESS_URL_ROOT.KOMMIKU_URL_FORMAT; echo "/".$series['slug']."/".$chapter['next']; ?>'+this.value+'/';" name="Pages" class="viewerPage">
				<?=$kommiku['pageOption']?>
			</select>
</div>