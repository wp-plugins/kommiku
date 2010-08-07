<?php
$allowFiles = array("gif","png","jpg","jpeg");

if(is_numeric($_POST['series_id']))
	$series = $db->series_detail($_POST['series_id']);
else
	die('Something went wrong!');
	
$table = $wpdb->prefix."comic_chapter";
if($wpdb->get_var("SELECT number FROM ".$table." WHERE number = '".$_CLEAN['number']."' AND series_id = '".$_CLEAN['series_id']."'") == $_CLEAN['number'])  
	if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['number'] != $_CLEAN['number']))
		$chapter['fail']['number']['duplicate'] = true;

if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
	if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $oldSeries['slug'] != $_CLEAN['slug']))
		$chapter['fail']['slug'] = true;
		
if (!is_numeric($_POST['number'])) 
	$chapter['fail']['number']['character'] = true;
		
$seriesFolder .= '/'.strtolower($series["slug"]).'/';

//Chapter Number and Slug
$chapterNumber = $_POST['number'];

$chapterFolder = $_POST['slug'].'/';

if (!$chapterFolder && $chapterNumber)
    die($chapterFolder.' not Being set');
    
//Check that we have a file
if((!empty($_FILES["zip"])) && ($_FILES['zip']['error'] == 0)) {
    if(class_exist('ZipArcher'))
		$zipclass = new ZipArchive();
	else
		die("The ZipArchive Library (zlib) is not enable. Can not extract without the library.");
    $ext = substr($_FILES['zip']['name'], strrpos($_FILES['zip']['name'], '.') + 1);
    
    //Get Max File Size from Option
    $var = 30;
    $maxFileSize = 1048576 * $var; //(1mb times $var)
  	$newname = UPLOAD_FOLDER.$seriesFolder.'uploads/'.$chapterSlug.'-'.$_FILES['zip']['name'];	
		
    if ((($ext == "zip") )) {        
        $zipclass->open($newname);
        for ($i=0; $i<$zipclass->numFiles;$i++) {
            $zipArray = $zipclass->statIndex($i);
            $zipExt = substr($zipArray['name'], strrpos($zipArray['name'], '.') + 1);
            if(!in_array(strtolower($zipExt),$allowFiles))
                $zip['error']['invalid_files'] = true; 
        }
		$zipFile = true;
    } else if(($ext == "rar") && ($_FILES['zip']['error'] == 0)) {
        if(function_exists('rar_open')) {
		$rar_file = rar_open($newname);
		$files = rar_list($rar_file);

		foreach ($files as $file) {
            $zipExt = substr($file->getName(), strrpos($file->getName(), '.') + 1);
            if(!in_array(strtolower($zipExt),$allowFiles))
                $zip['error']['invalid_files'] = true; 
		}
		$rarFile = true;
		rar_close($rar_file);
		} else {
		die('Rar function does not exist on this server. You won\'t be able to Extract Rar Files without the Rar Module.');
		}
	}
    
	if($_FILES["zip"]["size"] < $maxFileSize && !$zip['error']) {

		//Check if the Upload Directory Exist
        if(!is_dir(UPLOAD_FOLDER.$seriesFolder.'uploads/')) 
	        mkdir(UPLOAD_FOLDER.$seriesFolder.'uploads/');
        	
		//Check if the Chapter Directory Exist
        if(!is_dir(UPLOAD_FOLDER.$seriesFolder.$chapterSlug.'/'))
	        mkdir(UPLOAD_FOLDER.$seriesFolder.$chapterSlug.'/');
			
		//Move the Zip File Location to the Temporary Upload Directory
        if(is_dir(UPLOAD_FOLDER.$seriesFolder.$chapterSlug.'/'))
	        move_uploaded_file($_FILES['zip']['tmp_name'],$newname);
        
	} else {    
		$zip['error']['toolarge'] = true; 
        $status['error'] = "The zip/rar file is larger than ". $var ."mb";
    }
	
} else {
    //No File Uploaded
    $zip['fail']['nofile'] = true;
    $status['error'] = "There were no files to upload";
}

//Preset Vars
$extractFolder = UPLOAD_FOLDER.$seriesFolder.$chapterFolder;
$pubdate = date("Y-m-d H:i:s O");
$uploader = $current_user->ID;
$language = $_POST['language'];
$title = $_POST['title'];
$slug = $_POST['slug'];

//Extract
if(file_exists($newname) && !$chapter['fail'] && $zipFile == true) {
	$aZip = new ZipArchive();
	$aZip->open($newname);
	$aZip->extractTo($extractFolder);
	unlink($newname);
	
	$chapterID = $db->chapter_create($title,$chapterNumber,$summary,$_POST['series_id'],$pubdate,$slug,$scanlator,$scanlator_slug,0,true);
	$fileExtracted = getFileList($extractFolder);
	if(count($fileExtracted) == 1 || is_dir($fileExtracted[0]))
		$fileExtracted = getFileList($fileExtracted[0]);
		
	sort($fileExtracted);
		for ($count = 0 ; $count < count($fileExtracted); $count++) {
	        $fileArray = $fileExtracted[$count];
	        
	        list($width, $height, $type, $attr) = getimagesize($fileArray); 

	        if (!is_dir($fileArray) && $fileArray != "." && $fileArray != "..") {
	            $baseFileName = basename($fileArray);
				
	          $db->page_create('',$count,$baseFileName,$pubdate,'',$count,$_POST['series_id'],$chapterID,'');
	        } 
		}
	$status['pass'] = "The zipped chapter has been dumped.";
} else if (file_exists($newname) && !$chapter['fail'] && $rarFile == true) {
		$rar_file = rar_open($newname) or die("Can't open Rar archive");
		$files = rar_list($rar_file);
		foreach ($files as $file) {
			$file->extract($extractFolder);
		}
		$chapterID = $db->chapter_create($title,$chapterNumber,$summary,$_POST['series_id'],$pubdate,$slug,$scanlator,$scanlator_slug,0,$folder,true);
	$fileExtracted = getFileList($extractFolder);
	if(count($fileExtracted) == 1 || is_dir($fileExtracted[0]))
		$fileExtracted = getFileList($fileExtracted[0]);
		
	sort($fileExtracted);
		for ($count = 0 ; $count < count($fileExtracted); $count++) {
	        $fileArray = $fileExtracted[$count];
	        
	        list($width, $height, $type, $attr) = getimagesize($fileArray); 

	        if (!is_dir($fileArray) && $fileArray != "." && $fileArray != "..") {
	            $baseFileName = basename($fileArray);
				
	          $db->page_create('',$count,$baseFileName,$pubdate,'',$count,$_POST['series_id'],$chapterID,'');
	        } 
		}
		$status['pass'] = "The rarred chapter has been dumped.";
		rar_close($rar_file);
} else {
		if ($chapter['fail']['number']['duplicate']) $status['error'] .= 'The Chapter number has already been taken.<br/>';
		if ($chapter['fail']['number']['character']) $status['error'] .= 'The Chapter number has to be in numbers.<br/>';
		if ($chapter['fail']['scanlator']) $status['error'] .= 'The Scanlator does not exist<br/>';
		if ($chapter['fail']['slug']) $status['error'] .= 'The slug already exist<br/>';
}
?>