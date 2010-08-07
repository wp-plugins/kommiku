<?php 
if($kommiku['series_chapter'])
foreach ($kommiku['series_chapter'] as $item) {
	//Unset Vars that may be set
	unset($chapterTitle); unset($scanperson);

	//Format the Date Y-m-d
	$thedate = date( 'Y-m-d', strtotime($item->pubdate) );
	
	//Make sure the Title does not overwrite non-existing titles.
	//No need to touch this
	if ($item->title) $chapterTitle = ' - '.stripslashes($item->title);
	
	//Ignore this ine of Code:
	if (!$item->scanlator) { $scanperson = 'n/a'; } else { $scanlators = explode(',',$item->scanlator); $scanlators_slug = explode(',',$item->scanlator_slug); for($i=0; $i < count($scanlators); $i++) { if($scanperson) $scanperson .= ' & '; if($scanlators_slug[$i]) {	$scanperson .= '<a href="'.HTTP_HOST.K_SCANLATOR_URL.'/'.$scanlators_slug[$i].'/">'.$scanlators[$i].'</a>'; $theScanlator[trim($scanlators[$i])] = '<a href="'.HTTP_HOST.K_SCANLATOR_URL.'/'.$scanlators_slug[$i].'/">'.$scanlators[$i].'</a>'; } else { $scanperson .= $scanlators[$i];$theScanlator[trim($scanlators[$i])] = $scanlators[$i]; } } }
	//Hello Again!
	
	//Chapter Formatting - Wrap in a TD
	$chapterListing[$item->number] = 
	'<td class="series" style="padding-left: 15px;">'.
		//Grab the URL to the Chapter
		'<a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$series["slug"].'/'.$item->slug.'/">'.
		//Echo the Chapter title
		'Chapter '.$item->slug.$chapterTitle.'</a>
	</td>';
	//End of Wrap
	
	//Scanlator and Author Wrap
	if ($scanperson  != 'n/a') 
		$chapterListing[$item->number] .= '<td class="chapter" style="padding-left: 15px; text-align: left;">'.$scanperson.'</td>'; 
	else 
		$chapterListing[$item->number] .= '<td class="chapter" style="padding-left: 15px;">n/a</td>';
	
	//Date Wrap in a TD
	$chapterListing[$item->number] .= '<td class="updated" style="padding-left: 15px;">'.$thedate.'</td>';
	
}

//Sort the Chapters

if(!$chapterListing) unset($chapterListing);

//Remove the next line if you want to start the sorting from "Oldest to Newest" instead of "Newest to Oldest"
else krsort($chapterListing);

//Start the Output
if(!$series["chapterless"] || !$chapterListing) { ?> 
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr class="headline">
			<td class="series" style="width:40%;">Chapters</td>
			<td class="chapter" style="width:35%;">Scanlator</td>
			<td class="updated" style="width:15%;">Date Uploaded</td>
		</tr>
		<?php 
		if($chapterListing)
			foreach ($chapterListing as $list) {
				$a++;
				unset($odd); unset($even);
				if(count($chapterListing)%2)
					$odd = ' class="alt"';
				else
				   $even = ' class="alt"';
					
				if ($a % 2) {
					echo '<tr'.$even.'>'.$list.'</tr>';
				} else {
					echo '<tr'.$odd.'>'.$list.'</tr>';
				}
			
			}
		else
			echo "<tr><td class='series'>There are no chapters for this series yet.</td><td></td><td></td></tr>"
		?>
	</table>
<?php } ?>