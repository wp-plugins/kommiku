<?php kommiku_header(); ?>

<div class="home" id="content">
	<div class="breadcrumb">
		<h2 class="kommiku-bread"><a href="<?=HTTP_HOST?><?=$kommiku['url']['series']?>"><?=$kommiku["title"]["series"]?></a></h2>
	</div>
	
	<div id="page-img">	
		<?php kommiku_page_navigation(); ?>
		
		<div id="imageWrapper">
			<?php img(); ?>
		</div>

		<?php kommiku_page_navigation(); ?>
	</div>   

	<div id="page-info">
		<?php if($page["title"]){ ?> <h2 id="page-title"><?=$page["title"]?></h2> <?php } ?>
		<?php if($page["story"]){ ?> <p id="page-story"><?=$page["story"]?></p> <?php } ?>
	</div>
</div>

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

<?php kommiku_footer(); ?>



		



