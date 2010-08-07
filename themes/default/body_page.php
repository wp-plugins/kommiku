<?php kommiku_header(); ?>

<div class="home" id="content">
	<div class="breadcrumb">
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?>"><?=$kommiku["title"]["series"]?></a></h2>
	</div>
	
	<table cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto;">
		<tr><td><?php kommiku_page_navigation(); ?></td></tr>
		
		<tr id="imageWrapper">
			<td><?php if($page["img"]) img(); else echo stripslashes($page['story']); ?></td>
		</tr>

		<tr><td><?php kommiku_page_navigation(); ?></td></tr>
	</table>   
	<?php //Story Information ?>
	<?php  if($page["img"] && ($page["title"] || $page["story"])){  ?>
		<div id="page-info">
			<?php if($page["title"]){ ?> <h2 id="page-title"><?=$page["title"]?></h2> <?php } ?>
			<?php if($page["story"]){ ?> <p id="page-story"><?=$page["story"]?></p> <?php } ?>
		</div>
	<?php } ?>
	
</div>

<?php kommiku_footer(); ?>

<?php //Keyboard Commands! No need to touch this. ?>
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





		



