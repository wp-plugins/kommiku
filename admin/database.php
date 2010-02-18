<?php

//Forgot who I got this function from. But thanks!
function getFileList($dir, $recurse=false, $depth=false) { # array to hold return value 
 $retval = array(); 
 
 # add trailing slash if missing 
 if(substr($dir, -1) != "/") 
 	$dir .= "/"; 
 	
 # open pointer to directory and read list of files 
 	$d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading"); 
 	while(false !== ($entry = $d->read())) { 
	 	
	# skip hidden files 
 	if($entry[0] == ".") continue; if(is_dir("$dir$entry")) { 
	 	
	 	$retval[] = "$dir$entry/"; 
	 		
			if($recurse && is_readable("$dir$entry/")) {  
				if($depth === false) {  
					$retval = array_merge($retval, getFileList("$dir$entry/", true));  
					} 
				elseif($depth > 0) { 
					$retval = array_merge($retval, getFileList("$dir$entry/", true, $depth-1)); }  
				}
				
			 }
			  
			 elseif(is_readable("$dir$entry")) { 
				 $retval[] =  "$dir$entry"; 
				 } 	} 	$d->close(); 	return $retval; 
}

function delTree($dir) { // bcairns@gmail.com :3 Thanks!
	global $wpdb,$series,$page,$chapter,$db,$status,$settings;		

	if ($dir == UPLOAD_FOLDER || 
	   ($dir == UPLOAD_FOLDER.$series['folder'] && $_GET['chapter']) ||  
	   ($dir == UPLOAD_FOLDER.$series['folder'].$chapter['folder'] && $_GET['pg'])) //Don't Delete the Comic foldeR D:
		return;
		 
		//var_dump($dir);
		//echo '<br/>';
		
	    $files = glob( $dir . '*', GLOB_MARK );
	    foreach( $files as $file ){
	        if( substr( $file, -1 ) == '/' )
	            delTree( $file );
	        else
	            unlink( $file );
	    }
	    rmdir( $dir );
	}

Class kommiku_database {
		
	function trailingslash($str) {
		
		if (substr($str, -1, 1) != "/" && $str != '')
			$str .= "/";
			
		return $str;
	}
	
	function trail($str) {
		
		if(!$str)
			return;	
		
		if ($str[0] == "/")
			$str = substr($str, 1);
			
		if (substr($str, -1, 1) == "/")
			$str = substr($str, 0, -1);
			
		return $str;
	}
	
	function slug($str) {
		
		$str = str_replace("'","",$str);
		$str = str_replace(" ","_",$str);
		return strtolower($str);
		
	}
	
	function clean($str) {
		if(!get_magic_quotes_gpc()) {
			$str = addslashes($str);
		}
		$str = strip_tags(htmlspecialchars($str));
		return $str;
	}

	function page_create(
		$title = "",
		$slug,
		$img,
		$pubdate,
		$story      	 = '',
		$number,
		$series_id,
		$chapter_id	
	) {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_page";
	
	  	$wpdb->insert( $table , 
	  	
	  	array( 'title' => $title, 
	  		   'slug' => $slug, 
	  		   'img' => $img,
	  		   'pubdate' => $pubdate,
	  		   'story' => $story,
	  		   'number' => $number,
	  		   'series_id' => $series_id,
	  		   'chapter_id' => $chapter_id
	  		 ), 
	  	
			array( '%s', '%s','%s','%s','%s','%d','%d','%d' )
	  	       
	  	    );
	
	  		
	}
	
	function chapter_create($title = '', $number, $summary = '', $series_id, $returner = false) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_chapter";
	  	$wpdb->insert( $table , 
	  	
	  	array( 'title' => $title, 
	  		   'number' => $number, 
	  		   'summary' => $summary,
	  		   'series_id' => $series_id
	  		 ), 
	  	
	  	array( '%s', '%s', '%s', '%d' )  
	  	       
	  	    );
	  	 if($returner == true)
	  	 	return $wpdb->insert_id;
	
	}
	
	function historyu($what,$action,$pubdate,$series_name,$series_slug,$chapter_name,$chapter_number,$page_name,$page_slug) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_history";
	  	$wpdb->insert( $table , 
	  	
	  	array( 'what' => $what, 
	  		   'action' => $action, 
	  		   'pubdate' => $pubdate,
	  		   'series_name' => $series_name,
	  		   'series_slug' => $series_slug,
	  		   'chapter_name' => $chapter_name,
	  		   'chapter_number' => $chapter_number,
	  		   'page_name' => $page_name,
	  		   'page_slug' => $page_slug
	  		 ), 
	  	
	  	array( '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )  
	  	       
	  	    );
	  	  
	
	}
	
	function series_create($title, $slug, $summary = '', $chapterless = 0) {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_series";
	  	$wpdb->insert( $table , 
	 
	  	array( 'title' => $title, 
	  		   'slug' => $slug, 
	  		   'summary' => $summary,
	  		   'chapterless' => $chapterless
	  		 ), 
	  	
	  	array( '%s', 
	  	       '%s',
	  	       '%s',
	  	       '%d'
	  	       )  
	  	       
	  	    );
	
	}
	
	//Read
	function chapterless($series_id = NULL) {
	    global $wpdb;
	    
	    if(!$series_id)
			$series_id      = $_GET['series'];
	    
		if(is_numeric($series_id)) {
		$chapterless = $wpdb->get_var("SELECT chapterless FROM `".$wpdb->prefix."comic_series` WHERE id = '".$series_id."'");
		}
		
		return $chapterless;
    }
	
	function page_number($series_id = NULL, $chapter_id = NULL) {
	    global $wpdb;
	
	    if(!is_numeric($series_id))
			$series_id      = $_GET['series'];
		
		if(!is_numeric($chapter_id))
			$chapter_id     = $_GET['chapter'];
			
		if(is_numeric($series_id) && is_numeric($chapter_id)){
			$table = $wpdb->prefix."comic_page";
		  		$select = "SELECT max(number) as max_number FROM ".$table." WHERE chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
	  			$results = $wpdb->get_row( $select , ARRAY_A );
				return $results["max_number"]+1;
		}
	}	
	
	function page_list($series_id = NULL, $chapter_id = NULL) {
	    global $wpdb;
		$table = $wpdb->prefix."comic_page";

	    if(!$series_id)
			$series_id   = $_GET['series'];
		
		if(!$chapter_id)
			$chapter_id  = $_GET['chapter'];
		
		if(!$chapter_id && $series_id) {
			$chapterless = $wpdb->get_var("SELECT chapterless FROM `".$wpdb->prefix."comic_series` WHERE id = '".$series_id."'");
		}
		
		if(is_numeric($series_id) && is_numeric($chapter_id)){
		  	$select = "SELECT * FROM ".$table." WHERE chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
		} else if($chapterless == 1) {
		  	$select = "SELECT * FROM ".$table." WHERE chapter_id = '0' AND series_id = '".$series_id."'";
		}
		
		$results = $wpdb->get_results( $select );
		return $results;
	}
	
	function chapter_list() {
	    global $wpdb;
	    
		$series_id      = $_GET['series'];
		
	    if(is_numeric($series_id)){
			$table = $wpdb->prefix."comic_chapter";
		  		$select = "SELECT * FROM ".$table." WHERE series_id = '".$series_id."'";
		  		$results = $wpdb->get_results( $select );
				return $results;
		}
	}
	
	function series_list() {
		global $wpdb;
		$table = $wpdb->prefix."comic_series";
	  		$select = "SELECT * FROM ".$table;
	  		$results = $wpdb->get_results( $select );
			return $results;
	}
	
	function series_chapter($series_id) {
	    global $wpdb;
	    if (!is_numeric($series_id)) return;
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "SELECT * FROM ".$table." WHERE series_id = '".$series_id."' ORDER BY number DESC";
	  		$results = $wpdb->get_results( $select );
	  		sort($results,SORT_NUMERIC);
			return $results;
	}
	
	function chapter_pages($series_id,$chapter_id) {
	    global $wpdb;
	    if (!is_numeric($series_id) || !is_numeric($chapter_id)) return;
		$table = $wpdb->prefix."comic_page";
	  		$select = "SELECT * FROM ".$table." WHERE series_id = '".$series_id."' AND chapter_id = '".$chapter_id."' ORDER BY number DESC";
	  		$results = $wpdb->get_results( $select );
	  		sort($results);
			return $results;
	}
	
	function series_pages ($series_id) {
	    global $wpdb, $series;
	    if (!is_numeric($series_id)) return;
		$tableA = $wpdb->prefix."comic_page";
		$tableB = $wpdb->prefix."comic_chapter";
			
			if(!$series['chapterless'])
	  			$select = "SELECT `".$tableA."`.slug as pageSlug, `".$tableA."`.number as pageNumber, `".$tableA."`.pubdate as pubdate ,`".$tableB."`.number as chapterNumber FROM `".$tableA."` JOIN `".$tableB."` ON `".$tableA."`.`chapter_id` = `".$tableB."`.`id` WHERE `".$tableA."`.`series_id` = '".$series['id']."' ORDER BY `".$tableA."`.`pubdate` DESC LIMIT 0 , 10";
	  		else
	  			$select = "SELECT pubdate, slug as pageSlug, number as pageNumber FROM `".$tableA."` WHERE `series_id` = '".$series['id']."' ORDER BY `pubdate` DESC LIMIT 0 , 10";
	  			
	  		$results = $wpdb->get_results( $select );
	  		sort($results,SORT_NUMERIC);
			return $results;
	}
	
	function page_detail($id = NULL) {
	    global $wpdb;
	
	   if(!is_numeric($id))
		$id        = $_GET['pg'];

	    if(is_numeric($id)){
		$table = $wpdb->prefix."comic_page";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function page_read($pageNumber,$chapterNumber,$series) {
	    global $wpdb;
	    
	    if($pageNumber && is_numeric($chapterNumber) && $series){
			$table = $wpdb->prefix."comic_series";
	  		$series_id_query = "SELECT * FROM ".$table." WHERE slug = '".$series."'";
	  		$series = $wpdb->get_row( $series_id_query , ARRAY_A );
	  		$series_id = $series['id'];

			$table = $wpdb->prefix."comic_chapter";
	  		$chapter_id_query = "SELECT * FROM ".$table." WHERE number = '".$chapterNumber."' AND series_id = '".$series_id."'";
	  		$chapter = $wpdb->get_results( $chapter_id_query );
	  		$chapter_id = $wpdb->get_row( $chapter_id_query , ARRAY_A );
		    
			$table = $wpdb->prefix."comic_page";
	  		$select = "SELECT * FROM ".$table." WHERE number = '".$pageNumber."' AND chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function chapter_read($chapterNumber,$series) {
	    global $wpdb;
	    
	    if(is_numeric($pageNumber) && is_numeric($chapterNumber) && $series){
			$table = $wpdb->prefix."comic_series";
	  		$series_id_query = "SELECT * FROM ".$table." WHERE slug = '".$series."'";
	  		$series = $wpdb->get_row( $series_id_query , ARRAY_A );
	  		$series_id = $series['id'];

			$table = $wpdb->prefix."comic_chapter";
	  		$chapter_id_query = "SELECT * FROM ".$table." WHERE number = '".$chapterNumber."' AND series_id = '".$series_id."'";
	  		$chapter = $wpdb->get_results( $chapter_id_query );
	  		$results = $wpdb->get_row( $chapter_id_query , ARRAY_A );
			return $results;
		}
	}
	
	function series_read($series) {
	    global $wpdb;
	    
	    if(is_numeric($pageNumber) && is_numeric($chapterNumber) && $series){
			$table = $wpdb->prefix."comic_series";
	  		$series_id_query = "SELECT * FROM ".$table." WHERE slug = '".$series."'";
	  		$series = $wpdb->get_row( $series_id_query , ARRAY_A );
			return $series;
		}
	}
		
	function history_read($place = NULL,$value = NULL) {
	    global $wpdb;
	    
	 	$tableA = $wpdb->prefix."comic_history";
	    $select = "SELECT * FROM ".$tableA." LIMIT 0,10";
	    $result = $wpdb->get_results( $select );
	    return $result;
		
	}
	
	function chapter_detail($chapter_id = NULL) {
	    global $wpdb;
	    
		if (!is_numeric($chapter_id))
			$chapter_id = $_GET['chapter'];
					
	    if(is_numeric($chapter_id)){
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$chapter_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function series_detail($series_id = NULL) {
	    global $wpdb;
	    
		if (!$series_id)
			$series_id = $_GET['series'];
		
	    if(is_numeric($series_id)){
		$table = $wpdb->prefix."comic_series";
	  		$select = "SELECT * FROM ".$table." WHERE id = '".$series_id."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function option_detail($type,$type_id,$option) {
	    global $wpdb;
	    		
	    if(is_numeric($type_id)){
		$table = $wpdb->prefix."comic_options";
	  		$select = "SELECT value FROM ".$table." WHERE type = '".$type."' AND type_id = '".$type_id."' AND option_name = '".$option."'";
	  		$results = $wpdb->get_row( $select , ARRAY_A );
			return $results;
		}
	}
	
	function option_create($type,$type_id,$option,$value) {
	    global $wpdb;
			
		$table = $wpdb->prefix."comic_options";
	  	$wpdb->insert( $table , 
	 
	  	array( 'type' => $type, 
	  		   'type_id' => $type_id, 
	  		   'option_name' => $option,
	  		   'value' => $value
	  		 ), 
	  	
	  	array( '%s', 
	  	       '%d',
	  	       '%s',
	  	       '%s'
	  	       )  
	  	       
	  	    );
	
	}
	
	function option_update($type,$type_id,$option,$value) {
	    global $wpdb;
	    
	    if(is_numeric($type_id)) {
			$table = $wpdb->prefix."comic_options";
		  	$wpdb->update( $table , 
		  	
			  	array( 'value' => $value
			  		 ), 
			  	array( 'type_id' => $type_id,
			  		   'type' => $type,
			  		   'option_name' => $option ),
			  	array( '%s' ),  
				array( '%d','%s','%s' ) );
		}
	}
	
	//Update
	function page_update(
		$id,
		$title = '',
		$slug,
		$img,
		$pubdate,
		$story      	 = '',
		$number,
		$series_id,
		$chapter_id	
	) {
	    global $wpdb;
		
		$table = $wpdb->prefix."comic_page";
		
		$wpdb->update( $table, 
			array( 'title' => $title, 
	  		   'slug' => $slug, 
	  		   'img' => $img,
	  		   'pubdate' => $pubdate,
	  		   'story' => $story,
	  		   'number' => $number,
	  		   'series_id' => $series_id,
	  		   'chapter_id' => $chapter_id
	  		 ), 
			array( 'id' => $id ),
			array( '%s', '%s','%s','%s','%s','%d','%d','%d' ), 
			array( '%d' ) );
	
	}
	
	function chapter_update($id = NULL,$title = '',$number,$summary,$series_id) {
	    global $wpdb;
	    
	    if(!$id)
	    	$id = $_GET['chapter'];
	    
	    if(is_numeric($id)) {
			$table = $wpdb->prefix."comic_chapter";
		  	$wpdb->update( $table , 
		  	
			  	array( 'title' => $title, 
			  		   'number' => $number, 
			  		   'summary' => $summary,
			  		   'series_id' => $series_id
			  		 ), 
			  	array( 'id' => $id ),
			  	array( '%s', '%s', '%s', '%d' ),  
				array( '%d' ) );
		}
	}
	
	function series_update($id = NULL,$title,$slug,$summary,$chapterless) {
	    global $wpdb;
	    
	    if(!$id)
	    	$id = $_GET['series'];
	    
	    if(is_numeric($id)) {
			$table = $wpdb->prefix."comic_series";
		  	$wpdb->update( $table , 
			  	array( 'title' => $title, 
			  		   'slug' => $slug, 
			  		   'summary' => $summary,
			  		   'chapterless' => $chapterless
			  		 ), 
			  	array( 'id' => $id ),
			  	array( '%s', '%s', '%s', '%d' ),  
				array( '%d' ) );
		}
	}
	
	//Delete
		
	function page_delete($id = NULL, $chapter = NULL, $series = NULL) {
	    global $wpdb;
	
	   if(!is_numeric($series))
		$series      = $_GET['series'];
	   if(!is_numeric($chapter))
		$chapter     = $_GET['chapter'];
	   if(!is_numeric($id))
		$id        = $_GET['pg'];
	    
	    if(is_numeric($id) && is_numeric($chapter) && is_numeric($series)) {
		$table = $wpdb->prefix."comic_page";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$id."' AND chapter_id = '".$chapter."' AND series_id = '".$series."'";
		    $wpdb->query($select);
		}
	}
	
	function chapter_delete($chapter_id = NULL,$series_id = NULL) {
	    global $wpdb;

	   if(!is_numeric($series_id))
		$series_id      = $_GET['series'];
	   if(!is_numeric($chapter_id))
		$chapter_id     = $_GET['chapter'];
	    
		if(is_numeric($chapter_id) && is_numeric($series_id)) {
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$chapter_id."' AND series_id = '".$series_id."'";
		    $wpdb->query($select);
	
		$table = $wpdb->prefix."comic_page";
	  		$select = "DELETE FROM ".$table." WHERE chapter_id = '".$chapter_id."' AND series_id = '".$series_id."'";
		    $wpdb->query($select);
	    }
	}
	
	function series_delete($series_id = NULL) {
	    global $wpdb;
	    
	   if(!is_numeric($series_id))
		$series_id      = $_GET['series'];
		
		if(is_numeric($series_id)) {
		$table = $wpdb->prefix."comic_series";
	  		$select = "DELETE FROM ".$table." WHERE id = '".$series_id."'";
		    $wpdb->query($select);
		    
		$table = $wpdb->prefix."comic_chapter";
	  		$select = "DELETE FROM ".$table." WHERE series_id = '".$series_id."'";
		    $wpdb->query($select);
		    
		$table = $wpdb->prefix."comic_page";
	  		$select = "DELETE FROM ".$table." WHERE series_id = '".$series_id."'";
		    $wpdb->query($select);
		}
	
	}

}

?>