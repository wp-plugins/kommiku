<table width="27%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 15px; float: right;">														
	<tbody>
	<tr><td style="width: 45%;" class="series">
			<form action="<?=HTTP_HOST?>find" method="get">	
				<input style="width:100px;" type="text" name="this" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}"/>
				<input type="submit" value="Search"/>
			</form>
	</td></tr>
	<?php if($category["list"]){ ?>
	<tr class="headline"><td style="width: 45%;" class="series">Categories</td></tr>
	<?php foreach ($category["list"] as $item)
		echo '<tr><td class="series" style="padding-left: 15px;"><a href="'.HTTP_HOST.'directory/'.$item->slug.'/">'.$item->title.'</a></td></tr>';
	}?>
	</tbody>
</table>