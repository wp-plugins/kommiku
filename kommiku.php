<?php
/*
Plugin Name: Kommiku Viewer
Version: 2.0.6
Plugin URI: http://dotspiral.com/kommiku/
Description: Kommiku is a Online Manga Viewer.
Author: Henry Tran
Author URI: http://dotspiral.com/
*/ 

if ( !defined('WP_LOAD_PATH') ) {

	/** classic root path if wp-content and plugins is below wp-config.php */
	$classic_root = dirname(dirname(dirname(dirname(__FILE__)))) . '/' ;
	
	if (file_exists( $classic_root . 'wp-load.php') )
		define( 'WP_LOAD_PATH', $classic_root);
	else
		if (file_exists( $path . 'wp-load.php') )
			define( 'WP_LOAD_PATH', $path);
		else
			exit("Could not find wp-load.php");
}

$comic_upload_directory = get_option( 'kommiku_comic_upload' );
if(!$comic_upload_directory) $comic_upload_directory = 'comics';

define('KOMMIKU_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('KOMMIKU_PLUGIN_PATH', plugin_basename( dirname(__FILE__) ) . '/' );
define('KOMMIKU_FOLDER', dirname(__FILE__) );
define('UPLOAD_FOLDER',WP_LOAD_PATH.$comic_upload_directory  );
define('UPLOAD_URLPATH','http://'.$_SERVER['HTTP_HOST'].'/'.$comic_upload_directory );
define('KOMMIKU_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/' ));
define('KOMMIKU_URL_FORMAT', get_option( 'kommiku_url_format' ));
define('KOMMIKU_SKIN', get_option( 'kommiku_skin_directory' ));
define('HTTP_HOST', 'http://'.$_SERVER['HTTP_HOST'].'/' );

add_action('admin_menu', 'kommiku_menu');

function kommiku_fancy_url($var='REQUEST_URI')
{
	global $kommiku;
	if (!in_array($var, array('REQUEST_URI', 'PATH_INFO'))) $var = 'REQUEST_URI';
	$req = $_SERVER[$var];
			
	if (($var != 'PATH_INFO') && isset($_SERVER['PATH_INFO'])) {
		kommiku_fancy_url('PATH_INFO');
	}
	
	$explodeURL = array_slice(explode('/',$req),1,5);
				
	if($explodeURL[0] == KOMMIKU_URL_FORMAT && $explodeURL[0] != '') {
		if(get_option('kommiku_one_comic') != 0 && get_option('kommiku_one_comic') != false) {
			$kommiku['manga'] = true;
			$kommiku['series'] = get_option( 'kommiku_one_comic' );
			$kommiku['one_comic'] = true;
			$kommiku['chapter'] = $explodeURL[1];
			$kommiku['pages'] = $explodeURL[2];
		} else if($explodeURL[1] != '') {
			global $wpdb;
			$kommiku['series'] = strtolower($explodeURL[1]);
			if($kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$kommiku['series']."'"))
				$kommiku['manga'] = true;
			$kommiku['chapter'] = $explodeURL[2];
			$kommiku['pages'] = $explodeURL[3];
		} else {
			$kommiku['manga'] = true;
		}
	} else if((count($explodeURL) <= 4) && (count($explodeURL) >= 1) && ($explodeURL[0] != '')) {
		if(get_option('kommiku_no_slug')) {
			if(get_option('kommiku_one_comic') != 'false' && is_numeric($explodeURL[0]))  {
				$kommiku['manga'] = true;
				$kommiku['series'] = get_option( 'kommiku_one_comic' );
				$kommiku['one_comic'] = true;
				$kommiku['chapter'] = $explodeURL[0];
				$kommiku['pages'] = $explodeURL[1];
			} else {
				global $wpdb;
				$kommiku['series'] = $explodeURL[0];
				if($kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$kommiku['series']."'"))
					$kommiku['manga'] = true;
				$kommiku['chapter'] = $explodeURL[1];
				$kommiku['pages'] = $explodeURL[2];	
			}
		}	
	}
	
}

add_action('init', 'kommiku_fancy_url');
add_action('init', 'kommiku_source');

function kommiku_header() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/header.php';
		return;
}

function kommiku_css() {
		echo KOMMIKU_URLPATH.'/themes/'.KOMMIKU_SKIN.'/style.css';
		return;
}

function kommiku_footer() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/footer.php';
		return;
}

function kommiku_source()
{
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter;	
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
				
	if($kommiku['manga'])	{
						
		if(!empty($kommiku['series'])) {
			if(!$kommiku['series_id'])
				$kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$kommiku['series']."'"); 
			$series = $db->series_detail($kommiku['series_id']);
			$kommiku['seotitle'] = $series['title'];
			$kommiku['slug']['series'] = $series['slug'];	
			$kommiku['title']['series'] = $series['title'];
			$kommiku['url']['series'] = KOMMIKU_URL_FORMAT.'/'.$series['slug'].'/';
		}
		
		if(!empty($series['chapterless'])) {
			$kommiku['pages'] = $kommiku['chapter'];
			$kommiku['chapter'] = 0; //or False?
		} 
		
		if(isset($kommiku['chapter'])) {
			$kommiku['chapter_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_chapter` WHERE series_id = '".$kommiku['series_id']."' AND number = '".$kommiku['chapter']."'"); 
			$chapter = $db->chapter_detail($kommiku['chapter_id']);
			$series_chapter = $db->series_chapter($kommiku['series_id']);
			$kommiku['seotitle'] .= " : Chapter ".$chapter['number'];
			$kommiku['slug']['chapter'] = $chapter['slug'];	
			$kommiku['number']['chapter'] = $chapter['number'];
			$kommiku['title']['chapter'] = $chapter['title'];
			$kommiku['url']['chapter'] = $series['url'].$chapter['slug']."/";
		}
				
		if(empty($kommiku['chapter_id'])) {
			$kommiku['chapter_id'] = 0;
		} 	
		
		if($kommiku['pages'] && ($chapter || $series['chapterless'])) {
			$kommiku['page_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND slug = '".$kommiku['pages']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
			$page = $db->page_detail($kommiku['page_id']);
			$chapter_pages = $db->chapter_pages($kommiku['series_id'],$kommiku['chapter_id']);
			$kommiku['seotitle'] .= " page ".$page['slug'];
			$kommiku['slug']['page'] = $page['slug'];	
			$kommiku['number']['page'] = $page['number'];
			$kommiku['title']['page'] = $page['title'];
			$kommiku['url']['page'] = $page['slug']."/";
		} else if($chapter) {
			$kommiku['pages'] = $wpdb->get_var("SELECT min(number) FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
			$kommiku['page_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND number = '".$kommiku['pages']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
			$page = $db->page_detail($kommiku['page_id']);
			$chapter_pages = $db->chapter_pages($kommiku['series_id'],$kommiku['chapter_id']);
			$kommiku['seotitle'] .= " : Page ".$page['slug'];
			$kommiku['slug']['page'] = $page['slug'];	
			$kommiku['number']['page'] = $page['number'];
			$kommiku['title']['page'] = $page['title'];
			$kommiku['url']['page'] = $series['url'].$chapter['url'].$page['slug']."/";
		}
		
		$series_list = $db->series_list();
		if($series_list)
			foreach ($db->series_list() as $row) {
				$kommiku['series_listing'] .= '<option value="'.$row->slug.'">'.$row->title.'</option>';
				$kommiku['series_list'] .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a></li>';
			};	
								
		//Page, Chapter, Series		
		if(!empty($kommiku['series_id']) && isset($kommiku['chapter_id']) && !empty($kommiku['page_id'])){
			$isPage = true; include KOMMIKU_FOLDER.'/reader.php';			
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_page.php';			
		//Series, Chapter
		} else if(!empty($kommiku['series']) && isset($kommiku['chapter'])){
			if(get_option('kommiku_no_chapter')) {
				include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_warning.php';
			} else if($kommiku['page']) {
				$isPage = true; include KOMMIKU_FOLDER.'/reader.php';			
				include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_page.php';
			} else {
				$isChapter = true;
				include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_chapter.php';
			}
		//Series
		} else if(!empty($kommiku['series'])) {
			$isChapter = true; 
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_chapter.php';
		//Main Page with no Series Selected
		} else {
			$chapterUquery = "SELECT `".$wpdb->prefix."comic_series`.`slug` as series_slug, 
			`".$wpdb->prefix."comic_chapter`.`slug` as chapter_slug, 
			`".$wpdb->prefix."comic_chapter`.`pubdate` as pubdate,
			`".$wpdb->prefix."comic_series`.`title` as series_name
			FROM `".$wpdb->prefix."comic_chapter`,`".$wpdb->prefix."comic_series` 
			WHERE `".$wpdb->prefix."comic_chapter`.`series_id` = `".$wpdb->prefix."comic_series`.`id`
			ORDER BY `".$wpdb->prefix."comic_chapter`.`pubdate` DESC";
			$pageUquery = "SELECT `".$wpdb->prefix."comic_series`.`slug` as series_slug, 
			`".$wpdb->prefix."comic_chapter`.`slug` as chapter_slug, 
			`".$wpdb->prefix."comic_page`.`slug` as page_slug, 
			`".$wpdb->prefix."comic_page`.`pubdate` as pubdate,
			`".$wpdb->prefix."comic_series`.`title` as series_name,
			`".$wpdb->prefix."comic_series`.`chapterless` as chapterless
			FROM `".$wpdb->prefix."comic_page`,`".$wpdb->prefix."comic_chapter`,`".$wpdb->prefix."comic_series` 
			WHERE `".$wpdb->prefix."comic_page`.`series_id` = `".$wpdb->prefix."comic_series`.`id`
			AND `".$wpdb->prefix."comic_page`.`chapter_id` = `".$wpdb->prefix."comic_chapter`.`id`
			ORDER BY `".$wpdb->prefix."comic_page`.`pubdate` DESC";
			$chapterUpdates = $wpdb->get_results($chapterUquery);
			$pageUpdates = $wpdb->get_results($pageUquery);
			$kommiku['seotitle'] .= "Story Listing";
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_index.php';
		}
		
		exit;
	}
	
	unset($db);
	
}

function kommiku_menu() {
	add_menu_page('Kommiku', 'Comic', 8, KOMMIKU_FOLDER, 'kommiku', KOMMIKU_URLPATH.'comic.png'); //Thanks Lokis :)
	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', 'List', 8, 'kommiku', 'kommiku'); 
	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', 'Settings', 8, 'kommiku_settings', 'kommiku_settings'); 
	}
	
function kommiku() {
		global $wpdb,$series,$page,$chapter,$db,$status,$settings;	
		
		if(!is_dir(UPLOAD_FOLDER))
			mkdir(UPLOAD_FOLDER, 0755);
			error_reporting(E_ALL ^ E_NOTICE);
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		$wpdb->show_errors();
		$phpdate = date("Y-m-d H:i:s O");
		
		if($_POST['delete'] == "Delete It!") {
						
			if($_POST['pg']) {
				$page = $db->page_detail($_POST['pg']);	
				$series = $db->series_detail($page['series_id']);
				$chapter = $db->chapter_detail($page['chapter_id']); 
				$series['folder'] = '/'.strtolower($series['slug']).'/';
				$chapterFolder = $chapter['number'].'/';
				error_reporting(0); 
				if(!unlink(UPLOAD_FOLDER.$series['folder'].$chapterFolder.$page['img']))
					$status['error'] = 'The Image could not be deleted (or it doesn\'t exist) but the record was deleted (or maybe it was already gone?)';
				if($chapterFolder) $chapterHistory = ' - Chapter '.$chapter['number']; 
				$db->page_delete($_POST['pg'],$page['chapter_id'],$page['series_id']);
				
				//Remove the Record! Imo. **************************** Maybe fix?
				
				error_reporting(E_ALL ^ E_NOTICE);
				unset($page);
				if($status['error'])
					$status['error'] .= '<br/>';
					$status['pass'] = 'The Page has been deleted';
				kommiku_model_page();

					
			} else if($_POST['chapter']) {
				
				$chapter = $db->chapter_detail($_POST['chapter']); 
				$series = $db->series_detail($chapter['series_id']);
				$series['folder'] = '/'.strtolower($series['slug']).'/';
				$chapterFolder = $chapter['number'].'/';
				$chapterHistory = ' - Chapter '.$chapter['number']; 
				error_reporting(0); 
				delTree(UPLOAD_FOLDER.$series['folder'].$chapterFolder);
				$db->chapter_delete($chapter['id'],$chapter['series_id']);
				error_reporting(E_ALL ^ E_NOTICE);
				unset($chapter);
				if(!$status['error']) {
					$status['pass'] = 'The Chapter has been deleted';
					kommiku_model_chapter();
				} else				
					kommiku_model_page();
					
			} else if($_POST['series']) {
				
				$series = $db->series_detail($_POST['series']);
				$series['folder'] = '/'.strtolower($series['slug']).'/';
				//error_reporting(0); 
				delTree(UPLOAD_FOLDER.$series['folder']);
				$db->series_delete($series['id']);
				//error_reporting(E_ALL ^ E_NOTICE);
				unset($series);
				if(!$status['error']) {
					$status['pass'] = 'The Series has been deleted';
					kommiku_model_series();
				} else				
					kommiku_model_chapter();
			}	
			
		} else if($_POST['action']) {
			$_CLEAN['title']          = $db->clean($_POST['title']);
			$_CLEAN['slug']           = $db->clean($_POST['slug']);
			$_CLEAN['summary']        = $db->clean($_POST['summary']);
			$_CLEAN['number']         = $db->clean($_POST['number']);
			$_CLEAN['series_id']      = $db->clean($_POST['series_id']);
			$_CLEAN['chapter_id']     = $db->clean($_POST['chapter_id']);
			$_CLEAN['show_date']      = $db->clean($_POST['show_date']);
			$_CLEAN['show_title']     = $db->clean($_POST['show_title']);
			$_CLEAN['show_first']     = $db->clean($_POST['show_first']);
			$_CLEAN['show_one']       = $db->clean($_POST['show_one']);
			$_CLEAN['show_last']      = $db->clean($_POST['show_last']);
			$_CLEAN['show_comment']   = $db->clean($_POST['show_comment']);
			$_CLEAN['seodescription'] = $db->clean($_POST['seodescription']);
			$_CLEAN['seokeyword']     = $db->clean($_POST['seokeyword']);
			$_CLEAN['story']          = $db->clean($_POST['story']);
			
			//
			//Create a New Series
			//
			
			if($_POST['what'] == "series") { 
				$table = $wpdb->prefix."comic_series";
				
				$oldSeries = $db->series_detail($_POST['series_id']);
				
				if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $oldSeries['title'] != $_CLEAN['title']))
						$series['fail']['title'] = true;
					
				//Checks for Slug which Omit the creation and renaming of Folders :D
				if($_POST['action'] == "update" && is_numeric($_POST['series_id'])) {
					$_OLD['slug'] = $wpdb->get_var("SELECT slug FROM `".$table."` WHERE id = '".$_POST['series_id']."'"); 
					$chapterless = $wpdb->get_var("SELECT chapterless FROM `".$table."` WHERE id = '".$_POST['series_id']."'"); 
					if($_OLD['slug'] == $_CLEAN['slug'])
						$noRename = true;
				}
					
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $oldSeries['slug'] != $_CLEAN['slug']))
						$series['fail']['slug'] = true;
											
				if(!$series['fail']['slug'] && !$series['fail']['title']) {
					
					if($_POST['action'] == "create") {
						$db->series_create($_CLEAN['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']),$_POST['chapterless']);
						mkdir(UPLOAD_FOLDER.'/'.$_POST['slug'], 0755);
						$status['pass'] = 'The Series has been successfully created';
						$seriesID =	$wpdb->get_var("SELECT id FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'");
						$db->historyu('series','create',$phpdate,$_CLEAN['title'],$_CLEAN['slug'],'0','0','0','0');
						unset($series);
						kommiku_model_series();
					} else if($_POST['action'] == "update" && is_numeric($_POST['series_id'])) {
						$db->series_update($_POST['series_id'],$_CLEAN['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']),$chapterless);
						$status['pass'] = 'The Series has been updated';

						if ($_POST['show_page_update']) {
							if($db->option_detail('series',$_POST['series_id'],'show_page_update')) {
									$db->option_update('series',$_POST['series_id'],'show_page_update',$_POST['show_page_update']);
								} else {
									$db->option_create('series',$_POST['series_id'],'show_page_update',$_POST['show_page_update']);
							}
						}						
						
						if(!$noRename)
							rename(UPLOAD_FOLDER.'/'.$_OLD['slug'], UPLOAD_FOLDER.'/'.$_CLEAN['slug']);
						if(!$_POST['chapter'])
							kommiku_model_chapter();
						else
							kommiku_model_page();
					}
				} else {
					if ($series['fail']['title']) $status['error'] .= 'The series name has already been taken.<br/>';
					if ($series['fail']['slug']) $status['error'] .= 'The series slug has already been taken.<br/>';
					$series['title'] = $_POST['title'];
					$series['slug'] = $_POST['slug'];
					$series['summary'] = stripslashes($_POST['summary']);
					$series['chapterless'] = $_POST['chapterless'];
					
					if(!$_POST['chapter'] && $_POST['action']) {
						if($_POST['action'] == 'create' && $status['error'])
							kommiku_model_series();
						else
							kommiku_model_chapter();
					} else if ($_POST['action'])
						kommiku_model_page();
					else
						kommiku_model_series();
				}
					
			} 

			//
			//Create a New Chapter
			//
			if(is_numeric($_POST['series_id']) && $_POST['what'] == "chapter" && $_POST['action'] == "dump") {
				include KOMMIKU_FOLDER.'/extension/dumper.php';
				kommiku_model_chapter();
			}
			
			if(is_numeric($_POST['series_id']) && $_POST['what'] == "chapter" && $_POST['action'] != "dump") { 		
				$table = $wpdb->prefix."comic_chapter";
				
				$series = $db->series_detail($_POST['series_id']);
				$chapter = $db->chapter_detail($_POST['chapter_id']);
				
				if($_POST['action'] == "update" && is_numeric($_POST['chapter_id'])) {
					$_OLD['number'] = $wpdb->get_var("SELECT number FROM `".$table."` WHERE id = '".$chapter['id']."'"); 
					if($_OLD['number'] == $_CLEAN['number'])
						$noRename = true;
				}				 
				
				if($wpdb->get_var("SELECT number FROM ".$table." WHERE number = '".$_CLEAN['number']."' AND series_id = '".$_CLEAN['series_id']."'") == $_CLEAN['number'])  
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['number'] != $_CLEAN['number']))
						$chapter['fail']['number']['duplicate'] = true;
						
				if (!is_numeric($_POST['number'])) 
					$chapter['fail']['number']['character'] = true;
					
				if(!$chapter['fail']) {
					if($_POST['action'] == "create") {
						$db->chapter_create($_CLEAN['title'],$_POST['number'],$_CLEAN['summary'],$_POST['series_id'],$phpdate,$_POST['number']);
						$chapterID = $wpdb->get_var("SELECT id FROM `".$table."` WHERE number = '".$_POST['number']."'");
						$db->historyu('chapter','create',$phpdate,$series['title'],$series['slug'],$_CLEAN['title'],$_POST['number'],'0','0');
						
						if(!is_dir(UPLOAD_FOLDER.'/'.$series['slug'].'/'.$_POST['number']))
							mkdir(UPLOAD_FOLDER.'/'.$series['slug'].'/'.$_POST['number'], 0755);
							
						$status['pass'] = 'The Chapter has been successfully created';
						kommiku_model_chapter();
					} else if($_POST['action'] == "update" && is_numeric($_POST['chapter_id'])) {						
						$db->chapter_update($_POST['chapter_id'],$_CLEAN['title'],$_POST['number'],$_CLEAN['summary'],$_POST['series_id'],$phpdate,$_POST['number']);
						$status['pass'] = 'The Chapter has been successfully updated';
						$OldChapterFolder = str_replace('.0','',$_OLD['number']).'/';
						$NewChapterFolder = str_replace('.0','',$_POST['number']).'/';
						if(!$noRename)
							rename(UPLOAD_FOLDER.'/'.$series['slug'].'/'.$OldChapterFolder, UPLOAD_FOLDER.'/'.$series['slug'].'/'.$NewChapterFolder);
						kommiku_model_page();
					}
				} else {
					if ($chapter['fail']['number']['duplicate']) $status['error'] .= 'The Chapter number has already been taken.<br/>';
					if ($chapter['fail']['number']['character']) $status['error'] .= 'The Chapter number has to be in decimals or numbers.<br/>';
					$chapter['title'] = $_POST['title'];
					$chapter['number'] = $_POST['number'];
					$chapter['summary'] = $_POST['summary'];
					if($_POST['destination'] == "chapter") 
						kommiku_model_chapter();
					else
						kommiku_model_page();
				}
				
			}

			
			//
			//Create a New Page
			//
			
			if($_POST['what'] == "page" && is_numeric($_POST['series_id'])) { 
				$table = $wpdb->prefix."comic_page";
					$page['title']           = $_POST['title'];
					$page['number']          = $_POST['number'];
					$page['show_date']       = $_POST['show_date'];
					$page['show_title']      = $_POST['show_title'];
					$page['show_first']      = $_POST['show_first'];
					$page['show_one']        = $_POST['show_one'];
					$page['show_last']       = $_POST['show_last'];
					$page['show_comment']    = $_POST['show_comment'];
					$page['seodescription']  = $_POST['seodescription'];
					$page['seokeyword']      = $_POST['seokeyword'];
					$page['story']           = $_POST['story'];
					$page['series_id']       = $_POST['series_id'];
					$page['chapter_id']      = $_POST['chapter_id'];
					$page['slug']      		 = $_POST['slug'];
					
				if (is_numeric($_POST['page_id'])) {
					$oldPage = $db->page_detail($_POST['page_id']);
					$page['id'] = $oldPage['id'];
					$page['img'] = $oldPage['img'];
					if(!$oldPage) {
						die('Oh no 404!');	
					}
				}
					
				$series = $db->series_detail($_POST['series_id']);
				$seriesFolder .= '/'.strtolower($series["slug"]).'/';

				if(is_numeric($_POST['chapter_id'])) {
					$chapter = $db->chapter_detail($_POST['chapter_id']);
					}
				
				if(is_numeric($chapter['number'])) 
					$chapterFolder = $chapter['number'].'/';
				else
					$chapterFolder = '';
					
				if (!$chapterFolder && $chapter['number'])
					die($chapterFolder.' not Being set');
				
				//Check slug //doube check for UPDATE
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."' AND series_id = '".$_POST['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'") == $_CLEAN['slug'])  
					if (($_POST['action'] && !$oldPage) || ($_POST['action'] == "update" && $oldPage['slug'] != $_CLEAN['slug']))
						$page['fail']['slug'] = true;

				//If Updating Check Number
				if($wpdb->get_var("SELECT number FROM `".$table."` WHERE number = '".$_CLEAN['number']."' AND series_id = '".$_POST['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'") == $_CLEAN['number'])  
					if (($_POST['action'] && !$oldPage) || ($_POST['action'] == "update" && $oldPage['number'] != $_CLEAN['number']))
						$page['fail']['number']['duplicate'] = true;
							
				if(!is_numeric($page['number']))
						$page['fail']['number']['character'] = true;	
						 
				//Check that we have a file
				if((!empty($_FILES["img"])) && ($_FILES['img']['error'] == 0)) {
					//Check if the file is JPEG image and it's size is less than 350Kb
					$basefilename = basename($_FILES['img']['name']);
					$ext = substr($basefilename, strrpos($basefilename, '.') + 1);
					$filename = $_CLEAN['slug'].'.'.$ext;
					
					if (((strtolower($ext) == "jpg") && ($_FILES["img"]["type"] == "image/jpeg")) || 
				        ((strtolower($ext) == "png") && ($_FILES["img"]["type"] == "image/png")) || 
				        ((strtolower($ext) == "gif") && ($_FILES["img"]["type"] == "image/gif")) && 
				        ($_FILES["img"]["size"] < 2048000)) {
						//Determine the path to which we want to save this file
				   			$newname = UPLOAD_FOLDER.$seriesFolder.$chapterFolder.$filename;
				   		//Go Ahead and Move :D	
				   			$_CLEAN['img'] = $filename; //str_replace(WP_LOAD_PATH,'',$db->clean($newname));
			   		} else {
			   			//More than 2MB?
			   			$page['error']['toolarge'] = true; 
			   		}
			   		
			   	} else {
			   		//No File Uploaded
			   		if (!$_POST['action'])  
				   		$page['fail']['nofile'] = true;
			   		 else 
				   		$_CLEAN['img'] = $oldPage['img'];
			   	}
			if(!$page['fail']) {
				$page['pubdate'] = date("Y-m-d H:i:s O");
				if($chapter['number']) $chapterHistory = ' Chapter '.$chapter['number'].' -';
				if ($_POST['action'] == "create") {
		   			//Attempt to move the uploaded file to it's new place
		   				move_uploaded_file($_FILES['img']['tmp_name'],$newname);
						$db->page_create($_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_CLEAN['story'],$_POST['number'],$page['series_id'],$_POST['chapter_id']);
						$page['id'] = $wpdb->get_var("SELECT id FROM `".$table."` WHERE number = '".$_POST['number']."' AND series_id = '".$_POST['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'");
						$db->historyu('page','create',$phpdate,$series['title'],$series['slug'],$chapter['title'],$chapter['number'],$_CLEAN['title'],$_CLEAN['slug']);
						$status['pass'] = 'The Page has been successfully created';
						$table = $wpdb->prefix."comic_page";
					} else if (is_numeric($_POST['page_id']) && $_POST['action'] == "update") {
						//Uploaded a File? Delete The Last File!
						$status['pass'] = 'The Page has been updated';
						if ($newname) {
							error_reporting(E_ALL ^ E_WARNING); 
							if(!unlink(UPLOAD_FOLDER.$seriesFolder.$chapterFolder.$oldPage['img']))
								$status['pass'] .= "<br/>There were no file name ".$oldPage['img']." to Delete";
							error_reporting(E_ALL ^ E_NOTICE); 
							move_uploaded_file($_FILES['img']['tmp_name'],$newname); 
										}
						$db->page_update($oldPage['id'],$_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_CLEAN['story'],$_CLEAN['number'],$page['series_id'],$page['chapter_id']);
					}
					kommiku_model_createpage();
				} else {
					if ($page['fail']['number']['duplicate']) $status['error'] .= 'The Page number has already been taken.<br/>';
					if ($page['fail']['number']['character']) $status['error'] .= 'The Page number has to be in decimals or numbers.<br/>';
					if ($page['fail']['slug']) $status['error'] .= 'The Slug for the Page on this Chapter has already been taken';
					if ($page['fail']['nofile']) $status['error'] .= "There was no file to upload";
					if ($page['fail']['toolarge']) $status['error'] .= "The file is too large";
					if ($page['fail']['exist']) $status['error'] .= "The file couldn't be moved!? Please check permission on your folders";;
					kommiku_model_createpage();
				}
			}	
				
		} else {
			
			if(!$_GET['sub'] && !$_POST['action']) {
				// echo $test; /home/anraiki/public_html/wp-content/plugins/kommiku-viewer/upload/
				kommiku_model_series();
			}
				
			if($_GET['sub'] == 'delete' && is_numeric($_GET['series'])) {
				kommiku_model_delete();
			}
				
			if($_GET['sub'] == "listchapter" && is_numeric($_GET['series'])) 
				kommiku_model_chapter();
				
			if($_GET['sub'] == "listpage" && (is_numeric($_GET['series']) || (is_numeric($_GET['series']) && is_numeric($_GET['chapter']))))
				kommiku_model_page();
				
			if($_GET['sub'] == "edit" && is_numeric($_GET['series']) && is_numeric($_GET['chapter']) && is_numeric($_GET['pg']))
				kommiku_model_page();
				
			if($_GET['sub'] == "createpage" && is_numeric($_GET['series']))
				kommiku_model_createpage();

	}
}

add_action( 'widgets_init', 'load_widgets' );

function load_widgets() {
	register_widget( 'story_lister' );
}

class story_lister extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function story_lister() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'kstory-lister', 'description' => __('A widget that lists the Stories under the Kommiku plugin.', 'Kommiku: Story Lister') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'kommiku-story-lister-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'kommiku-story-lister-widget', __('Kommiku: Story Lister', 'Kommiku: Story Lister'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		
		$series_list = $db->series_list();
		if($series_list)
			foreach ($series_list as $row) {
				$kommiku['series_list'] .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a></li>';
			};	
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Grab Series
		if($kommiku['series_list']) {
			echo "<ul>";
			echo $kommiku['series_list'];
			echo "</ul>";
			}
			
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Example', 'example'), 'name' => __('John Doe', 'example'), 'sex' => 'male', 'show_sex' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}

function kommiku_model_series() {
	global $series,$db,$status;		
	include KOMMIKU_FOLDER.'/admin/list_series.php';
	}
	
function kommiku_model_delete() {
	global $series,$page,$chapter,$db,$status;		
	include KOMMIKU_FOLDER.'/admin/delete.php';
	}
	
function kommiku_model_chapter() {
	global $series,$chapter,$db,$status;		
	include KOMMIKU_FOLDER.'/admin/list_chapter.php';
	}
	
function kommiku_model_page() {
	global $series,$page,$chapter,$db,$status;		
	include KOMMIKU_FOLDER.'/admin/list_page.php';
	}
	
function kommiku_model_createpage() {
	global $db, $page, $status;
	include KOMMIKU_FOLDER.'/admin/list_page_create.php';
	}

function kommiku_settings() {
	global $settings,$status,$wpdb;
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
			
		if ($_POST['what'] == "settings" && $_POST['action'] == "update") {
			
			$settings['one_comic'] = $db->clean($_POST['one_comic']);
			$settings['url'] = $db->clean($_POST['url']);
			$settings['upload'] = $db->clean($_POST['upload']);
			$settings['skin'] = $db->clean($_POST['skin']);

			if($_POST['url'] == "")
				update_option('kommiku_no_slug', 'true');
			else
				update_option('kommiku_no_slug', 'false');
			
			if($settings['one_comic'] != "")
				update_option('kommiku_one_comic', $settings['one_comic']);
			else
				update_option('kommiku_one_comic', 'false');
								
			//Remove Trialing and LEading Slash
			$settings['url'] = $db->trail($settings['url']);
			$settings['upload'] = $db->trail($settings['upload']);
			
			//Check if the Directory Already Exist
			$oldName = WP_LOAD_PATH.'/'.get_option( 'kommiku_comic_upload' );
			$newName = WP_LOAD_PATH.'/'.$settings['upload'];
				
				if(is_dir($newName) && $oldName != $newName) {
						$settings['error'] = "The 'Upload Directory' you are trying to rename already exist.";
						$settings['upload'] = get_option( 'kommiku_comic_upload' );
						$settings['fail']['upload'] = true;
					} else if($oldName != $newName) {
						rename($oldName,$newName);
						update_option('kommiku_comic_upload', $settings['upload']);
					}
				
				if(!is_dir(KOMMIKU_FOLDER.'/themes/'.$settings['skin'])) {
					if($settings['error']) $settings['error'] .= '<br/>';
					$settings['error'] .= 'The skin does not exist';
					}
					
				if(!$settings['fail']) $settings['pass'] = "Your Settings has been updated";
				update_option('kommiku_url_format', $settings['url']);
			}
		
			if ($_POST['what'] == 'settings' && $_POST['action'] == 'checktable') {
				if($wpdb->query("Show columns from `".$wpdb->prefix."comic_chapter` like 'pub_date'")) {
					$query = "ALTER TABLE `".$wpdb->prefix."comic_chapter` DROP `pub_date`";
					$wpdb->query($query);
					$query = "ALTER TABLE `".$wpdb->prefix."comic_chapter` DROP `slug`";
					$wpdb->query($query);
					$query = "ALTER TABLE `".$wpdb->prefix."comic_chapter` ADD `pubdate` VARCHAR( 30 ) NOT NULL , ADD `slug` VARCHAR( 100 ) NOT NULL";
					$wpdb->query($query);
					$post['pass'] .= 'Seems like you have a outdated Column Name in a Table. It has been Fixed';
				} else if(!$wpdb->query("Show columns from `".$wpdb->prefix."comic_chapter` like 'pubdate'")) {
					$query = "ALTER TABLE `".$wpdb->prefix."comic_chapter` ADD `pubdate` VARCHAR( 30 ) NOT NULL , ADD `slug` VARCHAR( 100 ) NOT NULL";
					$wpdb->query($query);
					$post['pass'] .= 'Seems like you have a outdated Table. It has been Fixed';
				}		
				if($post['pass']) $post['pass'] .= "<br/>";
				$post['pass'] .= 'Tables are Okay! Checking is all done!';
			}
			
			
	include KOMMIKU_FOLDER.'/admin/settings.php';
	}
	
function install()
	{
	    global $wpdb, $kommiku_version;
	    
		$version = get_option( 'kommiku_version' );
		
				if (!$wpdb->get_var("SELECT * FROM `".$wpdb->prefix."comic_series`")) { 
				    $table = $wpdb->prefix."comic_page";
						    $structure = "CREATE TABLE $table (
						        id INT(9) NOT NULL AUTO_INCREMENT,
						        title VARCHAR(100) NOT NULL,
						        slug VARCHAR(100) NOT NULL,
						        img VARCHAR(255) NOT NULL,
						        pubdate VARCHAR(30) NOT NULL,
								number int(3) NOT NULL,
 						        story TEXT NOT NULL,
						        series_id INT(9) NOT NULL,
						        chapter_id INT(9) NOT NULL,
							UNIQUE KEY id (id)
						    );";
						    $wpdb->query($structure);
						
					    $table = $wpdb->prefix."comic_series";
						    $structure = "CREATE TABLE $table (
						        id INT(9) NOT NULL AUTO_INCREMENT,
						        title VARCHAR(100) NOT NULL,
						        slug VARCHAR(100) NOT NULL,
						        summary TEXT NOT NULL,
						        chapterless bool NOT NULL,
							UNIQUE KEY id (id)
						    );";
						    $wpdb->query($structure);
						
						$table = $wpdb->prefix."comic_chapter";
						    $structure = "CREATE TABLE $table (
						        id INT(9) NOT NULL AUTO_INCREMENT,
						        title VARCHAR(100) NOT NULL,
						        number VARCHAR(5) NOT NULL,
						        summary TEXT NOT NULL,
						        series_id INT(9) NOT NULL,
								pubdate VARCHAR(30) NOT NULL,
								slug VARCHAR(100), 
							UNIQUE KEY id (id)
						    );";
						    $wpdb->query($structure);
						
						$table = $wpdb->prefix."comic_history";
						    $structure = "CREATE TABLE $table (
						        id INT(11) NOT NULL AUTO_INCREMENT,
						        what VARCHAR(12) NOT NULL,
						        action VARCHAR(12) NOT NULL,
						        pubdate VARCHAR(30) NOT NULL,
						        series_name VARCHAR(30) NOT NULL,
						        series_slug VARCHAR(30) NOT NULL,
						        chapter_name VARCHAR(30) NOT NULL,
						        chapter_number INT(9) NOT NULL,	
						        page_name VARCHAR(30) NOT NULL,	
						        page_slug VARCHAR(12) NOT NULL,		
						        show_to_update TINYINT(4) NOT NULL,					        
							UNIQUE KEY id (id)
						    );";
						    $wpdb->query($structure);
						    
						$table = $wpdb->prefix."comic_options";
						    $structure = "CREATE TABLE $table (
						        id INT(9) NOT NULL AUTO_INCREMENT,
						        type VARCHAR(9) NOT NULL,
						        type_id INT(9) NOT NULL,
						        option_name VARCHAR(30) NOT NULL,
						        value TEXT NOT NULL,
							UNIQUE KEY id (id)
						    );";
						    $wpdb->query($structure);
						    
			}
		
		if(!get_option( 'kommiku_comic_upload' ))
			add_option("kommiku_comic_upload", 'comics');
			
		if(!get_option( 'kommiku_version' ))
			add_option("kommiku_version", "2.0.3");
			
		if(!get_option( 'kommiku_url_format' ))
			add_option("kommiku_url_format", 'manga');
			
		if(!get_option( 'kommiku_skin_directory' ))
			add_option("kommiku_skin_directory", 'default');
			
		if(!get_option( 'kommiku_one_comic' ))
			add_option("kommiku_one_comic", 'false');
			
		if(!get_option( 'kommiku_no_slug' ))
			add_option("kommiku_no_slug", 'false');
			
		if(!is_dir(WP_LOAD_PATH."/comics"))
			mkdir(WP_LOAD_PATH."/comics", 0755);
			
		if ($version = '2.0') { update_option('kommiku_version', '2.0.3'); 	}	
		if ($version = '2.0.3') { update_option('kommiku_version', '2.0.3.1'); }		
		
		//Newest
		if (!$wpdb->query("Show columns from `".$wpdb->prefix."comic_chapter` like 'pubdate'")) {
			$updateTable = 'ALTER TABLE `'.$wpdb->prefix.'comic_chapter` 
							ADD `pubdate` VARCHAR(30) NOT NULL ,
							ADD `slug` VARCHAR(100) 
							NOT NULL';
			$wpdb->query($updateTable);
				
			update_option('kommiku_version', '2.0.5');	
		}			
		
	}
register_activation_hook( __FILE__ , 'install');
	
	
?>