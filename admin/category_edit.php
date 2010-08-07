<?php	

$category = $db->category_detail($db->clean($_GET['category']));

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
		
	
?>	

<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><A href="<?php echo $url; ?>admin.php?page=kommiku_category">Category List</a> &raquo; <a href="admin.php?page=kommiku&sub=category_edit&category=<?=$category['slug']?>"><?=$category['name']?></a></h2>
	<?php if ($status['pass'] || $status['error']) { ?>
		<div class="updated fade" id="message" style="background-color: rgb(255, 251, 204);"><p><?php echo $status['pass'].$status['error']; ?></p></div>
	<?php } ?>
	<div class="metabox-holder has-right-sidebar">
		<input type="hidden" value="<?=$category['id']?>" name="id"/>
		<input type="hidden" value="category" name="what"/>	
		<input type="hidden" value="update" name="action"/>	
		<input type="hidden" value="category_edit" name="destination"/>	
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div class="postbox">
					<h3 style="cursor: default;"><span>Update?</span></h3>
					<div class="inside">
						<div class="submitbox" style="padding: 5px;">
							<div style="background: none; font-size: 11px;">
								<div style="width: 100%; float: right; text-align: right">
									<input style="margin-top:10px; width: 100px;" type="submit" value="Update" accesskey="p" tabindex="5" class="button-primary" name="publish"/>
								</div>
								<div class="clear"></div>
								<div class="misc-pub-section "></div>
							</div>								    
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div id="post-body-content">
			<div class="postbox" style="margin-bottom: 0px;">
				<h3 style="cursor: default;"><span>Category Name</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="name" class="screen-reader-text">Name</label>
						<input style="width: 100%;" type="text" autocomplete="off" value="<?=$category['name']?>" tabindex="1" size="30" name="name"/>
					</div>
				</div>
			</div>			
			
			<div style="margin-bottom: 10px;">
				<div class="inside">
					<div id="edit-slug-box">
						<strong>Permalink:</strong> <span id="sample-permalink"><?php echo 'http://thetosho.com/category/'; ?>
						<input type="text" value="<?=$category['slug']?>" name="slug" style="width: 10%; background: #FFFBCC;" /> /
						</span>
					</div>
				</div>
			</div>

			
		<div class="metabox-holder">
		
			<div class="postbox">
				<h3 style="cursor: default;"><span>Description</span></h3>
				<div class="inside">
					<div class="submitbox" style="padding: 5px;">
						<label for="summary" class="screen-reader-text">Description</label>
						<textarea tabindex="2" name="description" style="width: 99.5%;" rows="5"><?=stripslashes($category['description'])?></textarea>									
						<p>Add a description of the Category</p>
					</div>
				</div>
			</div>
			</form>
				<?php if ($category['id']) { ?>
				<div class="postbox">
					<h3 style="cursor: default;"><span><?php echo $deleteWord; ?></span></h3>
					<div class="inside">
						<div class="submitbox">
							<div style="background: none;">
								<div class="clear"></div>							
								<div style="padding: 10px 0; width: 100%; float: right; text-align: right;">		
								<form method="post" action="admin.php?page=kommiku_category" name="post" enctype="multipart/form-data">
									<input type="hidden" value="<?=$category['id']?>" name="id"/>
									<input type="hidden" value="category" name="what"/>	
									<input type="hidden" value="delete" name="action"/>	
									<input type="hidden" value="category" name="destination"/>	
									<input type="submit" name="category_create" class="button-primary" tabindex="5" value="Delete this Category">
								</form>
								</div>
								<div class="clear"></div>									
							</div>								    
						</div>
					</div>
				</div>
				<?php } ?>
		</div>
	</div>
		
	
</div>