	  <?php 
	  
	  if(($isPage)) {
	  		global $previousPage, $previousLink, $nextPage, $nextLink, $kommiku;
	  		if($series_chapter)
	  		foreach ($series_chapter as $chapterList) { $h++;
				$chapterLists[$h] = $chapterList->number;
				$chapterListID[$h] = $chapterList->id;
				if($select) {
					$nextChapter = $chapterList->number;
					$nextChapterID = $chapterList->id;
				}
				unset($select); 
				if($chapterList->number == $chapter["number"]) {
					$select = "selected=selected ";
					$chapterSelected = $h;
				}
				if ($chapterList->title) $chapterTitle = ' - '.$chapterList->title;
				$chapterOption = '<option '.$select.'value="'.$chapterList->number.'">'.$chapterList->number.$chapterTitle.'</option>'.$chapterOption;			
				if($select) { 
					$pass = $h-1;
					if(isset($chapterListID[$pass])) $previousChapter = $chapterLists[$pass];
					if(isset($chapterListID[$pass])) $previousChapterID = $chapterListID[$pass];
				}
			}  
				
			if($chapter_pages) {
			foreach ($chapter_pages as $pageList) { $i++;
				$pageLists[$i] = $pageList->number;
				if(isset($select)) $nextPage = $pageList->slug;
				unset($select); 
				if($pageList->number == $page["number"]) {
					$select = "selected=selected ";
					$pageSelected = $pageList->number;
				}
				$pageOption .= '<option '.$select.'value="'.$pageList->slug.'">'.$pageList->slug.'</option>';
				$lastPage = $pageList->number;
				if($select) $previousPage = $pageLists[$i-1];
				}
			}	
					
			if(isset($chapter["number"])){
				$chapter["next"] = $chapter["number"].'/';	
				$chapter["previous"] = $chapter["number"].'/';
			}
			
			if($lastPage == $pageSelected && $nextChapterID) {
				$number = $wpdb->get_var("SELECT min(number) FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$nextChapterID."'");
				$nextPage = $wpdb->get_var("SELECT min(slug) FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$nextChapterID."' AND number = '".$number."'");
				$chapter["next"] = $nextChapter.'/';
				} else if ($lastPage == $pageSelected) {
					unset($nextPage);	
				}
							
			if(is_null($previousPage) && $previousChapterID) {
				$number = $wpdb->get_var("SELECT max(number) FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$previousChapterID."'");	
				$previousPage = $wpdb->get_var("SELECT slug FROM `".$wpdb->prefix."comic_page` WHERE chapter_id = '".$previousChapterID."' AND number = '".$number."'");	
				$chapter["previous"] = $previousChapter.'/';
			}  
						
			if(KOMMIKU_URL_FORMAT)
				$komUrlDash = KOMMIKU_URL_FORMAT.'/';
				
			if(!$kommiku['one_comic'])
				$seriesUrl = $series["slug"].'/';
						
		if($chapter) {
			if(isset($previousPage)) $previousLink = HTTP_HOST.$komUrlDash.$seriesUrl.$chapter["previous"].$previousPage.'/';
			if(isset($nextPage)) $nextLink = HTTP_HOST.$komUrlDash.$seriesUrl.$chapter["next"].$nextPage.'/';
		} else {
			if(isset($previousPage)) $previousLink = HTTP_HOST.$komUrlDash.$seriesUrl.$previousPage.'/';
			if(isset($nextPage)) $nextLink = HTTP_HOST.$komUrlDash.$seriesUrl.$nextPage.'/';
		}
				
		function prevPage($link = false,$wrapper = '',$class = NULL,$title = NULL) {
			global $previousPage, $previousLink;
			
			if($link && isset($previousLink)) {
				if($class) $class = 'class="'.$class.'" '; 
				if($title) $title = 'title="'.$title.'" ';
					else $title =  'title="Read the previous part, Page '.$previousPage.'"';  
				echo '<a '.$class.$title.'href="'.$previousLink.'">'.$wrapper.'</a>';
			} else if($link) {
				echo $wrapper;
			} else {
				echo $previousLink;	
			}
				
		}
		
		function checkPrevPage() {
			global $previousPage, $previousLink;
						
			if(isset($previousPage) && isset($previousLink)) 
				return true;
				
			return false;
			
				
		}
		
		function checkNextPage() {
			global $nextPage, $nextLink;
			
			if(isset($nextLink) && isset($nextPage)) 
				return true;
				
			return false;
			
				
		}
				
		function nextPage($link = false,$wrapper = '',$class = NULL,$title = NULL) {
			global $nextPage, $nextLink;
			
			if($link && isset($nextLink)) {
				if($class) $class = 'class="'.$class.'" '; 
				if($title) $title = 'title="'.$title.'" '; 
					else $title =  'title="Read the Next part, Page '.$nextPage.'"';
				echo '<a '.$class.$title.'href="'.$nextLink.'">'.$wrapper.'</a>';
			} else if($link) {
				echo $wrapper;
			} else {
				echo $nextLink;	
			}
				
		}
		
		function img($class = NULL,$title = NULL) {
			global $nextPage, $nextLink, $series, $chapter, $page;
			
			if($page["chapter_id"]) {
				$urlC = $chapter["number"].'/';	
			}
			
			if($series && $page)
				$wrapper = '<img src="'.UPLOAD_URLPATH.'/'.$series["slug"].'/'.$urlC.$page["img"].'" />';
			else
				$wrapper = 'Page does not Exist';	
				
			if(isset($nextLink)) {
				if($class) $class = 'class="'.$class.'" '; 
				if($title) $title = 'title="'.$title.'" '; 
					else $title =  'title="Read the Next part, Page '.$nextPage.'"';
				echo '<a '.$class.$title.'href="'.$nextLink.'">'.$wrapper.'</a>';
			} else {
				echo $wrapper;
			} 
				
		}
}
		?>