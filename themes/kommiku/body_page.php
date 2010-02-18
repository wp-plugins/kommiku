<?php kommiku_header(); ?>
	
<div class="narrowcolumn home" id="content">

	<?php if($kommiku['one_page']){ ?>
	<div class="breadcrumb>"
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=KOMMIKU_URL_FORMAT?>/">Story Listings</a></h2> &raquo; 
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?>"><?=$kommiku["title"]["series"]?></a></h2> &raquo; 
	
		<?php if($kommiku["number"]["chapter"]) { ?>
			<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?><?=$kommiku['number']['chapter']?>/">Chapter <?=$kommiku["number"]["chapter"]?></a></h2> &raquo; 
			<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?><?=$kommiku['number']['chapter']?>/<?=$kommiku['url']['page']?>">Page <?=$kommiku["number"]["page"]?></a></h2>
		<?php } else { ?>
			<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?><?=$kommiku['number']['page']?>">Page <?=$kommiku["number"]["page"]?></a></h2>
		<?php } ?>
	</div>
	<?php } ?>
	
	<div id="page-img">	
		<div class="manga-navi">
			<span class="previousLink"><?php prevPage(true,'[Previous]'); ?></span><span class="nextLink"><?php nextPage(true,'[Next]'); ?></span>
			<?php if($chapterOption){ ?> Chapter <select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.KOMMIKU_URL_FORMAT; echo "/".$series['slug']; ?>/'+this.value+'/';" name="Chapters" class="viewerChapter"><?php echo $chapterOption; ?></select><?php } ?>
			Page <select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.KOMMIKU_URL_FORMAT; echo "/".$series['slug']."/".$chapter['next']; ?>'+this.value+'/';" name="Pages" class="viewerPage"><?php echo $pageOption; ?></select>
		</div>
		<div id="imageWrapper">
			<?php img(); ?>
		</div>
		<div class="manga-navi">
			<span class="previousLink"><?php prevPage(true,'[Previous]'); ?></span><span class="nextLink"><?php nextPage(true,'[Next]'); ?></span>
			<?php if($chapterOption){ ?> Chapter <select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.KOMMIKU_URL_FORMAT; echo "/".$series['slug']; ?>/'+this.value+'/';" name="Chapters" class="viewerChapter"><?php echo $chapterOption; ?></select><?php } ?>
			Page <select onchange="javascript:window.location='<?php if(KOMMIKU_URL_FORMAT) echo '/'.KOMMIKU_URL_FORMAT; echo "/".$series['slug']."/".$chapter['next']; ?>'+this.value+'/';" name="Pages" class="viewerPage"><?php echo $pageOption; ?></select>
		</div>
	</div>   
	<div>
		<?php if($page["title"]){ ?> <h2><?=$page["title"]?></h2> <?php } ?>
		<?php if($page["story"]){ ?> <p><?=$page["story"]?></p> <?php } ?>
	</div>
</div>
					
<script type="text/javascript">
			document.onkeyup = KeyCheck;       
			function KeyCheck(e)
			{
			   var KeyID = (window.event) ? event.keyCode : e.keyCode;
			   switch(KeyID)
			   {
			      case 37:
			   		<?php if (checkPrevPage()) { ?>  
			   			window.location = '<?php prevPage(); ?>';
			   		 <?php } else { ?> 
			   			alert('You are already on the First Page.'); <?php } ?>
			      break;
			  	  
			      case 39:
			      	 <?php if (checkNextPage()) { ?>  
			      	 	window.location = '<?php nextPage(); ?>'; 
			      	 <?php } else { ?> 
			      	 	alert('This is the latest page.'); <?php } ?>
				  break;
			   }
			}
</script> 

			
<?php kommiku_footer(); ?>

		

