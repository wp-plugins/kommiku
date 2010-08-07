<?php
/*
Plugin Name: Kommiku Viewer
Version: 2.1.1
Plugin URI: http://dotspiral.com/kommiku/
Description: Kommiku is a Online Media Viewer.
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
define('KOMMIKU_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
define('KOMMIKU_PLUGIN_PATH', plugin_basename( dirname(__FILE__) ) . '/' );
define('KOMMIKU_FOLDER', dirname(__FILE__) );
define('UPLOAD_FOLDER',WP_LOAD_PATH.$comic_upload_directory  );
define('UPLOAD_URLPATH',get_bloginfo('wpurl').'/'.$comic_upload_directory );
define('KOMMIKU_ABSPATH', str_replace("\\","/", WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) . '/' ));
define('KOMMIKU_URL_FORMAT', get_option( 'kommiku_url_format' ));
define('KOMMIKU_SKIN', get_option( 'kommiku_skin_directory' ));
define('KOMMIKU_URL_INDEX', get_option( 'kommiku_url_index' ) );
define('HTTP_HOST', get_bloginfo('url').'/' );
define('K_SCANLATOR_URL', get_option('kommiku_scanlator') );
add_action('admin_menu', 'kommiku_menu');
load_plugin_textdomain('kommiku', false, dirname( plugin_basename(__FILE__) ) . '/lang');

$kommiku['alphabets'] = array('0-9',A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z);

function kommiku_fancy_url($var='REQUEST_URI'){
	global $kommiku;
	if (!in_array($var, array('REQUEST_URI', 'PATH_INFO'))) $var = 'REQUEST_URI';
	$req = $_SERVER[$var];
			
	if (($var != 'PATH_INFO') && isset($_SERVER['PATH_INFO'])) {
		kommiku_fancy_url('PATH_INFO');
	}
	
	$searchURL = explode('?',$req);

	if($searchURL[0] == "/find") {
		$searchExplosion = explode('=',$searchURL[1]);
		header('Location: '.HTTP_HOST.'find/'.$searchExplosion[1].'/');
		exit;
	}	
	
	$explodeURL = array_slice(explode('/',$req),1,5);
	$checkExplosion = $explodeURL;
	$unecessaryThing = array_shift($checkExplosion);
	if($checkExplosion[0] == KOMMIKU_URL_FORMAT && $explodeURL[0] != '' && !get_option('kommiku_one_comic')) 
		$explodeURL = $checkExplosion;
	unset($checkExplosion);
	
	//Replace Index
	if($explodeURL[0] == '' && get_option('kommiku_one_comic') != 'false' && get_option('kommiku_override_index') == true) {
				$kommiku['manga'] = true;
				$kommiku['series'] = get_option( 'kommiku_one_comic' );
				$kommiku['one_comic'] = true;
				$kommiku['pages'] = "latest";
				$kommiku['index'] = true;
		}
	
	if(strtolower($explodeURL[0]) == "find" && $explodeURL[1]) {
		$kommiku['manga'] = true;
		$kommiku['find'] = $explodeURL[1];
	} else if($explodeURL[0] == KOMMIKU_URL_INDEX) {
		$kommiku['manga'] = true;
		if($explodeURL[0] != '')
		$kommiku['category'] = $explodeURL[1];
	} else if($explodeURL[0] == KOMMIKU_URL_FORMAT && $explodeURL[0] != '') {
		//If you are only hosting one series on the site
		if(get_option('kommiku_one_comic') != 0 && get_option('kommiku_one_comic') != false) {
			$kommiku['manga'] = true;
			$kommiku['series'] = get_option( 'kommiku_one_comic' );
			$kommiku['one_comic'] = true;
			$kommiku['chapter'] = $explodeURL[1];
			$kommiku['pages'] = $explodeURL[2];
		} else if($explodeURL[1] != '') {
		//Else normal style!!
			global $wpdb;
			$kommiku['series'] = strtolower($explodeURL[1]);
			if($kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$kommiku['series']."'"))
				$kommiku['manga'] = true;
			$kommiku['chapter'] = $explodeURL[2];
			$kommiku['pages'] = $explodeURL[3];
		} else {
			$kommiku['manga'] = true;
		} 
	} else if ($explodeURL[0] == K_SCANLATOR_URL && get_option('kommiku_scanlator_enabled')) {
		$kommiku['scanlator'] = true;
		$kommiku['scanlator_slug'] = $explodeURL[1];	
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


//Blocks
function kommiku_sidebar_category_list() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/blocks/sidebar_category_list.php';
		return;
}

function kommiku_series_table_list() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/blocks/series_table_list.php';
		return;
}

function kommiku_series_information() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/blocks/series_information.php';
		return;
}

function kommiku_chapter_table_list() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/blocks/chapter_table_list.php';
		return;
}

function kommiku_page_navigation() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/blocks/page_navigation.php';
		return;
}
//End of blocks

function kommiku_css() {

	if(file_exists(KOMMIKU_URLPATH.'themes/'.KOMMIKU_SKIN.'/style.css'))
		echo KOMMIKU_URLPATH.'themes/'.KOMMIKU_SKIN.'/style.css';
	else 
		echo KOMMIKU_URLPATH.'themes/'.KOMMIKU_SKIN.'/stylesheets/main.css';

	return;
}

function kommiku_title() {
		global $kommiku;
		echo $kommiku['seotitle'];
		return;
}

define('K_A_K', get_option( 'K_A_K' ));

function kommiku_footer() {
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter;
		include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/footer.php';
		return;
}

function kommiku_source()
{
	global $wpdb, $post, $comment, $kommiku, $page, $series, $chapter, $category;	
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
				
	if($kommiku['scanlator']) {
		$scanlator = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."comic_scanlator` WHERE slug = '".$kommiku['scanlator_slug']."'", ARRAY_A);
		if($scanlator && $kommiku['scanlator_slug'] != '') {
			$kommiku['seotitle'] = K_SCANLATOR_URL.": ". $scanlator['title'];
			$kommiku['description'] = "Information on ".K_SCANLATOR_URL.": ".$scanlator['title'];	
			$scanlator['releases'] = $db->scanlators_chapter($scanlator['slug']);
			include KOMMIKU_FOLDER.'/extension/scanlator/body_scanlator_detail.php';
		} else {
			$kommiku['description'] = "A page or directory displaying a List of ".K_SCANLATOR_URL;	
			$kommiku['seotitle'] = K_SCANLATOR_URL."Listings";
			include KOMMIKU_FOLDER.'/extension/scanlator/body_scanlator.php';
		}
		exit;
	}	
				
	if($kommiku['manga'])	{
						
		if($kommiku['find']) {
			$kommiku['find'] = urldecode($kommiku['find']);
			$kommiku['results'] = $db->find_series($kommiku['find']);
			$kommiku['description'] = __("Search Results for: ", 'kommiku').$kommiku['find'];	
			$kommiku['keyword'] = "Manga, Comics, ".$kommiku['find'];		
			$kommiku['seotitle'] = __('Search Results for: ', 'kommiku')."'".$kommiku['find'].'"';
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_search.php';
			exit;
		}		
				
		if($kommiku['category'] == 'complete') {
			#Seo Feature
			#$kommiku['keyword'] = "Manga, Comics,Completed Series";
			#$kommiku['description'] = "Completed stories by various authors and illustrators.";
		} else if($kommiku['category']) {
			$category = $db->category_detail($kommiku['category']);
			$category["url"] = HTTP_HOST.KOMMIKU_URL_INDEX.'/'.$category["slug"].'/';
			$category["name"] = ucfirst($category["name"]);
			$category["list"] = $db->category_read();
			#$kommiku['keyword'] = "Manga, Comics, Tosho, Toshokan, Library";
			$kommiku['description'] = __("Dead page is Dead.", 'kommiku');
			if ($category["name"]) {
				$kommiku['keyword'] .= ', '.$category["name"];
				$kommiku['seotitle'] = __('Category: ', 'kommiku').$category["name"];
				$kommiku['description'] = __('Category: ', 'kommiku').$category["name"].". Series Listing.";
			} else {
				$kommiku['seotitle'] = __('OMG! 404?! ', 'kommiku'); 
			}
			$search_results = $db->search_category($kommiku['category']);
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_category.php';
			exit;
		}
				
		if(!empty($kommiku['series'])) {
			if(!$kommiku['series_id'])
				$kommiku['series_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_series` WHERE slug = '".$kommiku['series']."'"); 
			$series = $db->series_detail($kommiku['series_id']);
			$kommiku['seotitle'] = $series['title'];
			$kommiku['slug']['series'] = $series['slug'];	
			$kommiku['title']['series'] = $series['title'];
			$kommiku['url']['series'] = KOMMIKU_URL_FORMAT.'/'.$series['slug'].'/';
		}
		
		if(!empty($series['chapterless']) && !$kommiku['index']) {
			$kommiku['pages'] = $kommiku['chapter'];
			$kommiku['chapter'] = 0; //or False?
		} 
		
		//Replace Index - 2
		if($kommiku['pages'] == "latest") {
			$kommiku['page_id']  = $wpdb->get_var("SELECT max(id) FROM `".$wpdb->prefix."comic_page`");
			$kommiku['chapter_id']  = $wpdb->get_var("SELECT max(id) FROM `".$wpdb->prefix."comic_chapter`"); 			
		}
		
		if(isset($kommiku['chapter']) || is_numeric($kommiku['chapter_id'])) {
			if(is_numeric($kommiku['chapter']) || is_numeric($kommiku['chapter_id'])) {
				if(!$kommiku['chapter_id']) $kommiku['chapter_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_chapter` WHERE series_id = '".$kommiku['series_id']."' AND number = '".$kommiku['chapter']."'"); 
				$chapter = $db->chapter_detail($kommiku['chapter_id']);
				$kommiku['seotitle'] .= " : Chapter ".$chapter['number'];
				$kommiku['slug']['chapter'] = $chapter['slug'];	
				$kommiku['number']['chapter'] = $chapter['number'];
				$kommiku["breacrumb"] = "Chapter ".$kommiku["number"]["chapter"]." ";
				$kommiku['title']['chapter'] = $chapter['title'];
				$kommiku['url']['chapter'] = $series['url'].$chapter['slug']."/";
			}
			$kommiku['series_chapter'] = $db->series_chapter($kommiku['series_id']);
		}
				
		if(empty($kommiku['chapter_id'])) {
			$kommiku['chapter_id'] = 0;
		} 	
				
		if(isset($kommiku['pages']) && ($chapter || $series['chapterless']) && $kommiku['pages'] != '') {
			if(!$kommiku['page_id']) $kommiku['page_id'] = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."comic_page` WHERE series_id = '".$kommiku['series_id']."' AND slug = '".$kommiku['pages']."' AND chapter_id = '".$kommiku['chapter_id']."'"); 
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
		
		$kommiku['series_list_raw'] = $db->series_list();
		if($kommiku['series_list_raw'] )
			foreach ($kommiku['series_list_raw'] as $row) {
			$chapterTitle = stripslashes($row->title);
				if(strtolower($row->slug) == strtolower($kommiku['series']))
					$seriesOption .= '<option selected=selected value="'.$row->slug.'">'.$chapterTitle.'</option>';
				else
					$seriesOption .= '<option value="'.$row->slug.'">'.$chapterTitle.'</option>';
				$kommiku['series_list'] .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$chapterTitle.'</a></li>';
			};	
								
		//Page, Chapter, Series		
		if((!empty($kommiku['series']) && isset($kommiku['chapter']) && $kommiku['page']) || 
			(!empty($kommiku['series_id']) && isset($kommiku['chapter_id']) && !empty($kommiku['page_id']))){
			$isPage = true; 
			include KOMMIKU_FOLDER.'/reader.php';		
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_page.php';
		//Series
		} else if(!empty($kommiku['series']) && $kommiku['series'] != 'index.php') {
			$isChapter = true; 
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_chapter.php';
		//Main Page with no Series Selected
		} else {
			$chapterUpdates = $db->chapter_update_list();
			$pageUpdates = $wpdb->get_results($pageUquery);
			$kommiku['seotitle'] .= __("Story Listing", 'kommiku');
			$category["list"] = $db->category_read();
			include KOMMIKU_FOLDER.'/themes/'.KOMMIKU_SKIN.'/body_index.php';
		}
		
		exit;
	}
	
	unset($db);
	
}

define('V32c7148e', K_A_K);
	
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
					$status['error'] = __('The Image could not be deleted (or it doesn\'t exist) but the record was deleted (or maybe it was already gone?)', 'kommiku');
				if($chapterFolder) $chapterHistory = sprintf(__(' - Chapter %d', 'kommiku'),$chapter['number']); 
				$db->page_delete($_POST['pg'],$page['chapter_id'],$page['series_id']);
				error_reporting(E_ALL ^ E_NOTICE);
				unset($page);
				if($status['error'])
					$status['error'] .= '<br/>';
					$status['pass'] = __('The Page has been deleted', 'kommiku');
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
					$status['pass'] = __('The Chapter has been deleted', 'kommiku');
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
					$status['pass'] = __('The Series has been deleted', 'kommiku');
					kommiku_model_series();
				} else				
					kommiku_model_chapter();
			}	
			
		} else if($_POST['action']) {
			$_CLEAN['title']          = $_POST['title'];
			$_CLEAN['name']           = $_POST['name'];
			$_CLEAN['slug']           = $_POST['slug'];
			$_CLEAN['summary']        = $_POST['summary'];
			$_CLEAN['description']    = $_POST['description'];
			$_CLEAN['number']         = $_POST['number'];
			$_CLEAN['series_id']      = $_POST['series_id'];
			$_CLEAN['chapter_id']     = $_POST['chapter_id'];
			$_CLEAN['seodescription'] = $_POST['seodescription'];
			$_CLEAN['seokeyword']     = $_POST['seokeyword'];
			$_CLEAN['story']          = $_POST['story'];
			$_CLEAN['scanlator']	  = $_POST['scanlator'];
			$_CLEAN['author']         = $_POST['author'];
			$_CLEAN['illustrator']	  = $_POST['illustrator'];
			$_CLEAN['link'] 		  = $_POST['link'];
			$_CLEAN['creation'] 	  = $_POST['creation'];
			$_CLEAN['alt_name'] 	  = $_POST['alt_name'];
			$_CLEAN['story_type'] 	  = $_POST['story_type'];
			$_CLEAN['text'] 		  = $_POST['text'];
			
			if($_POST['what'] == "scanlator") { 
				$table = $wpdb->prefix."comic_scanlator";
				$oldScanlator = $db->scanlator_detail($_POST['scanlator_id']);
				
				if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  
					if ($_POST['action'] == "update" && $oldScanlator['title'] != $_CLEAN['title'])
						$scanlator['fail']['title'] = true;
					
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
					if ($_POST['action'] == "update" && $oldScanlator['slug'] != $_CLEAN['slug'])
						$scanlator['fail']['slug'] = true;
						
				if(!$scanlator['fail']) {
						$db->scanlator_update($_POST['scanlator_id'],$_POST['title'],$_CLEAN['slug'],stripslashes($_CLEAN['text']),stripslashes($_CLEAN['link']));
						$status['pass'] = 'The scanlator has been updated';
					
					kommiku_scanlator_edit();
				} else {
					if ($scanlator['fail']['title']) $status['error'] .= __('The scanlator name has already been taken.<br/>', 'kommiku');
					if ($scanlator['fail']['slug']) $status['error'] .= __('The scanlator slug has already been taken.<br/>', 'kommiku');
					$scanlator['title'] = $_POST['title'];
					$scanlator['slug'] = $_POST['slug'];
					$scanlator['text'] = stripslashes($_POST['text']);
					$scanlator['link'] = stripslashes($_POST['link']);
					kommiku_scanlator_edit();
				}
			}
			
			if($_POST['what'] == "category") { 
				$table = $wpdb->prefix."comic_category";
				$oldScanlator = $db->category_detail($_POST['slug']);
				
				if($wpdb->get_var("SELECT title FROM `".$table."` WHERE title = '".$_CLEAN['title']."'") == $_CLEAN['title'])  
					if ($_POST['action'] == "update" && $oldScanlator['title'] != $_CLEAN['title'])
						$category['fail']['name'] = true;
					
				if($wpdb->get_var("SELECT slug FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'") == $_CLEAN['slug']) 
					if ($_POST['action'] == "update" && $oldScanlator['slug'] != $_CLEAN['slug'])
						$category['fail']['slug'] = true;
						
				if(!$category['fail']) {
						$db->category_update($_POST['id'],$_CLEAN['name'],stripslashes($_CLEAN['description']),$_CLEAN['slug']);
						$status['pass'] = 'The Category has been updated';
					
					kommiku_category_edit();
				} else {
					if ($category['fail']['name']) $status['error'] .= __('The category name has already been taken.<br/>', 'kommiku');
					if ($category['fail']['slug']) $status['error'] .= __('The category slug has already been taken.<br/>', 'kommiku');
					$category['name'] = $_POST['name'];
					$category['slug'] = $_POST['slug'];
					$category['description'] = stripslashes($_POST['description']);
					kommiku_category_edit();
				}
			}
			
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
					
					$series = $db->series_detail($_POST['series_id']);
					$seriesFolder .= '/'.strtolower($series["slug"]).'/';
					
					$series['title'] = $_POST['title'];
					$series['slug'] = $_POST['slug'];
					$series['summary'] = stripslashes($_POST['summary']);
					$series['chapterless'] = $_POST['chapterless'];
					$series['author'] = $_POST['author'];
					$series['illustrator'] = $_POST['illustrator'];
					
					if(is_numeric($_POST['type']))
						$story_type = $_POST['type'];
					else
						$story_type = 0;
					
					if((!empty($_FILES["img"])) && ($_FILES['img']['error'] == 0)) {
						//Check if the file is JPEG image and it's size is less than 350Kb
						$basefilename = basename($_FILES['img']['name']);
						$ext = substr($basefilename, strrpos($basefilename, '.') + 1);
						$filename = 'icon.'.$ext;
						
						if ((strtolower($ext) == "jpeg") || 
							(strtolower($ext) == "jpg") || 
							(strtolower($ext) == "png") || 
							(strtolower($ext) == "gif") && 
							($_FILES["img"]["size"] < 2048000)) 
						{
							//Determine the path to which we want to save this file
								$newname = UPLOAD_FOLDER.$seriesFolder.$filename;
							//Go Ahead and Move :D	
								$_CLEAN['img'] = $filename;
								$series['img'] = $_CLEAN['img'];

						} else {
							$_CLEAN['img'] = $series['img'];
						}
			   		
					} else {

						//No File Uploaded
						if (!$_POST['action'])  
							$page['fail']['nofile'] = true;
						 else 
							$_CLEAN['img'] = $series['img'];
					}
					
					if($newname){
						if(!is_dir(UPLOAD_FOLDER))
							mkdir(UPLOAD_FOLDER, 0755);
							
						if(!is_dir(UPLOAD_FOLDER.strtolower($seriesFolder)))
							mkdir(UPLOAD_FOLDER.strtolower($seriesFolder), 0755);
							
						move_uploaded_file($_FILES['img']['tmp_name'],$newname);	
					}
						
					if($_POST['action'] == "create") {
						$db->series_create($_CLEAN['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']),$chapterless,$categories,$author,$illustrator,$read,$creation,$alt_name,$status,$rating,$story_type,$_CLEAN['img']);
						if(!is_dir(UPLOAD_FOLDER.'/'.strtolower($_POST['slug'])))
							mkdir(UPLOAD_FOLDER.'/'.strtolower($_POST['slug']), 0755);
						$status['pass'] = __('The Series has been successfully created', 'kommiku');
						$seriesID =	$wpdb->get_var("SELECT id FROM `".$table."` WHERE slug = '".$_CLEAN['slug']."'");
						unset($series);
						kommiku_model_series();
					} else if($_POST['action'] == "update" && is_numeric($_POST['series_id'])) {
						$db->series_update($_POST['series_id'],$_CLEAN['title'],$_CLEAN['slug'],stripslashes($_CLEAN['summary']),$chapterless,$_POST['categories'],$_CLEAN['author'],$_CLEAN['illustrator'],$_POST['read'],$_CLEAN['creation'],$_CLEAN['alt_name'],$_POST['status'],$_POST['mature'],$story_type,$_CLEAN['img']);
						$status['pass'] = __('The Series has been updated', 'kommiku');						
						
						if(!$noRename)
							rename(UPLOAD_FOLDER.'/'.$_OLD['slug'], UPLOAD_FOLDER.'/'.$_CLEAN['slug']);
						if(!$_POST['chapter'])
							kommiku_model_chapter();
						else
							kommiku_model_page();
					}
				} else {
					if ($series['fail']['title']) $status['error'] .= __('The series name has already been taken.<br/>', 'kommiku');
					if ($series['fail']['slug']) $status['error'] .= __('The series slug has already been taken.<br/>', 'kommiku');
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
					$_OLD['slug'] = $wpdb->get_var("SELECT slug FROM `".$table."` WHERE id = '".$chapter['id']."'"); 
					if($_OLD['slug'] == $_CLEAN['slug'])
						$noRename = true;
				}				 
				
				if($wpdb->get_var("SELECT number FROM ".$table." WHERE number = '".$_CLEAN['number']."' AND series_id = '".$_CLEAN['series_id']."'") == $_CLEAN['number'])  
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['number'] != $_CLEAN['number']))
						$chapter['fail']['number']['duplicate'] = true;
				
				if($wpdb->get_var("SELECT slug FROM ".$table." WHERE slug = '".$_CLEAN['slug']."' AND series_id = '".$_CLEAN['series_id']."'") == $_CLEAN['slug'])  
					if (($_POST['action'] != 'update') || ($_POST['action'] == "update" && $_OLD['slug'] != $_CLEAN['slug']))
						$chapter['fail']['slug']['duplicate'] = true;
						
				if (!is_numeric($_POST['number'])) 
					$chapter['fail']['number']['character'] = true;
				
				if (!is_numeric($_POST['slug'])) 
					$chapter['fail']['number']['slug'] = true;
					
				if (!is_numeric($_POST['volume']) && $_POST['volume'] != 0) 
					$chapter['fail']['volume'] = true;

				if(!$chapter['fail']) {

					if($_POST['action'] == "create") {

						$chapterID = $db->chapter_create($_CLEAN['title'],$_POST['number'],$_CLEAN['summary'],$_POST['series_id'],$phpdate,$_POST['slug'],$scalator,$scalator_slug,0,$folder,true);
						
						if(!is_dir(UPLOAD_FOLDER.'/'.strtolower($series['slug']).'/'.$_POST['number']))
							mkdir(UPLOAD_FOLDER.'/'.strtolower($series['slug']).'/'.$_POST['number'], 0755);
							
						$status['pass'] = __('The Chapter has been successfully created', 'kommiku');
						kommiku_model_chapter();
					} else if($_POST['action'] == "update" && is_numeric($_POST['chapter_id'])) {						
						$db->chapter_update($_POST['chapter_id'],$_CLEAN['title'],$_POST['number'],$_CLEAN['summary'],$_POST['series_id'],$chapter['pubdate'],$_POST['slug'],$_POST['scanlator'],$_POST['scanlator_slug'],$_POST['volume'],$folder);
						$status['pass'] = 'The Chapter has been successfully updated';
						$chapter['scanlator'] = $_POST['scanlator'];
						$chapter['scanlator_slug'] = $_POST['scanlator_slug'];
						$chapter['volume'] = $_POST['volume'];
						$chapter['slug'] = $_POST['slug'];
						$chapter['number'] = $_POST['number'];
						$OldChapterFolder = str_replace('.0','',$_OLD['number']).'/';
						$NewChapterFolder = str_replace('.0','',$_POST['number']).'/';
						if(!$noRename)
							rename(UPLOAD_FOLDER.'/'.$series['slug'].'/'.$OldChapterFolder, UPLOAD_FOLDER.'/'.$series['slug'].'/'.$NewChapterFolder);
						kommiku_model_page();
					}
				} else {
					if ($chapter['fail']['number']['duplicate']) $status['error'] .= __('The Chapter number has already been taken.<br/>', 'kommiku');
					if ($chapter['fail']['number']['character']) $status['error'] .= __('The Chapter number has to be in decimals or numbers.<br/>', 'kommiku');
					if($chapter['fail']['number'])  $status['error'] .= __('The "Volume Input" must be Numeric', 'kommiku');
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
					
				if (!isset($chapterFolder) && $chapter['number'])
					die($chapterFolder.' not being set');
				
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
					
					if ((strtolower($ext) == "jpeg") || 
						(strtolower($ext) == "jpg") || 
				        (strtolower($ext) == "png") || 
				        (strtolower($ext) == "gif") && 
				        ($_FILES["img"]["size"] < 2048000)) {
						//Determine the path to which we want to save this file
				   			$newname = UPLOAD_FOLDER.$seriesFolder.$chapterFolder.$filename;
				   		//Go Ahead and Move :D	
				   			$_CLEAN['img'] = $filename;
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
						if($newname){
							if(move_uploaded_file($_FILES['img']['tmp_name'],$newname)) {
								//Attempt to move the uploaded file to it's new place
								//Check if Directory Exist
								if(!is_dir(UPLOAD_FOLDER))
									mkdir(UPLOAD_FOLDER, 0755);
								if(!is_dir(UPLOAD_FOLDER.strtolower($seriesFolder)))
									mkdir(UPLOAD_FOLDER.strtolower($seriesFolder), 0755);
								if(!is_dir(UPLOAD_FOLDER.strtolower($seriesFolder).$chapterFolder))
									mkdir(UPLOAD_FOLDER.strtolower($seriesFolder).$chapterFolder, 0755);
								
								$table = $wpdb->prefix."comic_page";
								$db->page_create($_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_POST['story'],$_POST['number'],$page['series_id'],$_POST['chapter_id'],'');
								$table = $wpdb->prefix."comic_page";
								$page['id'] = $wpdb->get_var("SELECT id FROM `".$table."` WHERE number = '".$_POST['number']."' AND series_id = '".$_POST['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'");								
								
								if ($handle = opendir(UPLOAD_FOLDER.$seriesFolder.$chapterFolder)) {
									while (false !== ($file = readdir($handle))) {
										if ($file != "." && $file != "..") {
											$status['pass'] = $file.'<br/>';
										}
									}
									closedir($handle);
								}
								
							} else {
								$status['pass'] = __('Error 1: Could not move file', 'kommiku').' - '.UPLOAD_FOLDER.$seriesFolder.$chapterFolder.$filename;
							}
						} else {
							$status['pass'] = __('No file were uploaded. But a page was created.', 'kommiku');
							$table = $wpdb->prefix."comic_page";
							$db->page_create($_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_POST['story'],$_POST['number'],$page['series_id'],$_POST['chapter_id'],'');
							$table = $wpdb->prefix."comic_page";
							$page['id'] = $wpdb->get_var("SELECT id FROM `".$table."` WHERE number = '".$_POST['number']."' AND series_id = '".$_POST['series_id']."' AND chapter_id = '".$_POST['chapter_id']."'");								
						}
					} else if (is_numeric($_POST['page_id']) && $_POST['action'] == "update") {
						//Uploaded a File? Delete The Last File!
						$status['pass'] = __('The Image/Page has been updated', 'kommiku');
						if ($newname) {
							error_reporting(E_ALL ^ E_WARNING); 
							if(!unlink(UPLOAD_FOLDER.$seriesFolder.$chapterFolder.$oldPage['img']))
								$status['pass'] .= "<br/>There were no file name ".$oldPage['img']." to Delete";
							error_reporting(E_ALL ^ E_NOTICE); 
							if(!is_dir(UPLOAD_FOLDER))
								mkdir(UPLOAD_FOLDER, 0755);
							if(!is_dir(UPLOAD_FOLDER.strtolower($seriesFolder)))
								mkdir(UPLOAD_FOLDER.strtolower($seriesFolder), 0755);
							if(!is_dir(UPLOAD_FOLDER.strtolower($seriesFolder).$chapterFolder))
								mkdir(UPLOAD_FOLDER.strtolower($seriesFolder).$chapterFolder, 0755);
							if(move_uploaded_file($_FILES['img']['tmp_name'],$newname))
								$status['pass'] .= __(' | Image moved!', 'kommiku');
							else
								$status['pass'] .= __(' | Image Failed!', 'kommiku');
										}
						

						if($wpdb->get_var("SELECT * FROM `".$wpdb->prefix."posts` WHERE post_status = 'publish' AND post_name = '".$_POST['wp_post_slug']."'")) {
							$wp_post_slug = $_POST['wp_post_slug'];
						} else if($_POST['wp_post_slug'] != '') {
							$wp_post_slug = '';
							$status['error'] .= __('<br/>No such Wordpress Post', 'kommiku');
						}
									
						$db->page_update($oldPage['id'],$_CLEAN['title'],$_CLEAN['slug'],$_CLEAN['img'],$page['pubdate'],$_CLEAN['story'],$_CLEAN['number'],$page['series_id'],$page['chapter_id'],$wp_post_slug);
					
					}
					kommiku_model_createpage();
				} else {
					if ($page['fail']['number']['duplicate']) $status['error'] .= __('The Page number has already been taken.<br/>', 'kommiku');
					if ($page['fail']['number']['character']) $status['error'] .= __('The Page number has to be in decimals or numbers.<br/>', 'kommiku');
					if ($page['fail']['slug']) $status['error'] .= __('The Slug for the Page on this Chapter has already been taken', 'kommiku');
					if ($page['fail']['nofile']) $status['error'] .= __("There was no file to upload", 'kommiku');
					if ($page['fail']['toolarge']) $status['error'] .= __("The file is too large", 'kommiku');
					if ($page['fail']['exist']) $status['error'] .= __("The file couldn't be moved!? Please check permission on your folders", 'kommiku');
					kommiku_model_createpage();
				}
			}	
				
		} else {
			
			if(!$_GET['sub'] && !$_POST['action']) {
				kommiku_model_series();
			}
				
			if($_GET['sub'] == 'delete' && is_numeric($_GET['series'])) {
				kommiku_model_delete();
			}
				
			if($_GET['sub'] == "scanlator_edit")
				kommiku_scanlator_edit();
				
			if($_GET['sub'] == "listchapter" && is_numeric($_GET['series'])) 
				kommiku_model_chapter();
				
			if($_GET['sub'] == "listpage" && (is_numeric($_GET['series']) || (is_numeric($_GET['series']) && is_numeric($_GET['chapter']))))
				kommiku_model_page();
				
			if($_GET['sub'] == "edit" && is_numeric($_GET['series']) && is_numeric($_GET['chapter']) && is_numeric($_GET['pg']))
				kommiku_model_page();
				
			if($_GET['sub'] == "createpage" && is_numeric($_GET['series']))
				kommiku_model_createpage();

			if($_GET['sub'] == "category_edit")
				kommiku_category_edit();
				
	}
}

add_action( 'widgets_init', 'load_widgets' );

function load_widgets() {
	register_widget( 'story_lister' );
	register_widget( 'chapter_lister' );
}

class story_lister extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function story_lister() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'kstory-lister', 'description' => __('A widget that lists the Stories under the Kommiku plugin.', 'Kommiku: Story Lister', 'kommiku') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'kommiku-story-lister-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'kommiku-story-lister-widget', __('Kommiku: Story Lister', 'Kommiku: Story Lister', 'kommiku'), $widget_ops, $control_ops );
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
				$seriesOption .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.stipslashes($row->title).'</a></li>';
			};	
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Grab Series
		if($seriesOption) {
			echo "<ul>";
			echo $seriesOption;
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
		$defaults = array( 'title' => __('Example', 'example', 'kommiku'), 'name' => __('John Doe', 'example', 'kommiku'), 'sex' => 'male', 'show_sex' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid', 'kommiku'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}


class chapter_lister extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function chapter_lister() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'kchapter-lister', 'description' => __('A widget that lists the Stories under the Kommiku plugin.', 'Kommiku: Chapter Lister', 'kommiku') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'kommiku-chapter-lister-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'kommiku-chapter-lister-widget', __('Kommiku: Chapter Lister', 'Kommiku: Chapter Lister', 'kommiku'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		
		$upnum = $instance['upnum'];
		
		$chapter_list = $db->chapter_hupdate($upnum);
		if($chapter_list)
			foreach ($chapter_list as $row) {
				$date = date( 'm/d',  strtotime($row->date) );
				$kommiku['chapter_list'] .= '<li><small>['.$date.']</small> <a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->series_slug.'/'.$row->chapter_slug.'">'.$row->series_title.' '.$row->chapter_slug.'</a></li>';
			};	
		
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		//Grab Series
		if($kommiku['chapter_list']) {
			echo "<ul>";
			echo $kommiku['chapter_list'];
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
		$instance['upnum'] = strip_tags( $new_instance['upnum'] );
		
		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Kommiku Chapter Updates', 'Kommiku Chapter Updates', 'kommiku'), 'upnum' => 30 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'kommiku'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'upnum' ); ?>"><?php _e('How many updates to show?', 'kommiku'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upnum' ); ?>" name="<?php echo $this->get_field_name( 'upnum' ); ?>" value="<?php echo $instance['upnum']; ?>" style="width:100%;" />
		</p>
	<?php
	}
}

function kommiku_model_series() {
	global $kommiku,$series,$db,$status;	
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
	
function kommiku_category_edit() {
	global $db, $category, $status;
	include KOMMIKU_FOLDER.'/admin/category_edit.php';
	}	
	
function kommiku_scanlator_edit() {
	global $db, $scanlator, $status;
	include KOMMIKU_FOLDER.'/extension/scanlator/scanlator_edit.php';
	}
	
function kommiku_category() {
	global $kommiku,$settings,$status,$wpdb;
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
	
	include KOMMIKU_FOLDER.'/admin/category.php';
	}
	
function kommiku_model_createpage() {
	global $db, $page, $status;
	include KOMMIKU_FOLDER.'/admin/list_page_create.php';
	}

function kommiku_ftp_dump() {
		define('B36421ec14', K_A_K);
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		include KOMMIKU_FOLDER.'/extension/ftp-dumper-p.php';
	}
	
function kommiku_scanlator() {
	global $kommiku;
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		include KOMMIKU_FOLDER.'/extension/scanlator/scanlator.php';
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
			
			if($_POST['scanlator_url']){
				update_option('kommiku_scanlator', $_POST['scanlator_url']);
				$settings['scanlator_url'] = $_POST['scanlator_url'];
			} else {
				update_option('kommiku_scanlator', 'author');
				$settings['scanlator_url'] = 'author';
			}
			
			if($_POST['scanlator_enable'] == 1){
				if (!$wpdb->query("Show columns from `".$wpdb->prefix."comic_scanaltor` like 'title'")) {
					$structure = "CREATE TABLE `".$wpdb->prefix."comic_scanlator` (
					`id` INT NOT NULL AUTO_INCREMENT ,
					`title` VARCHAR( 32 ) NOT NULL ,
					`slug` VARCHAR( 32 ) NOT NULL ,
					`text` TEXT NOT NULL,
					UNIQUE KEY id (id)
					) ;";
					$wpdb->query($structure);
				}
				update_option('kommiku_scanlator_enabled', true);
				$settings['scanlator_enable'] = $_POST['scanlator_enable'];
			} else {
				update_option('kommiku_scanlator_enabled', false);
				$settings['scanlator_enable'] = false;
			}
			
			if($_POST['directory']){
				update_option('kommiku_url_index', urlencode($_POST['directory']));
				$settings['directory'] = $_POST['directory'];
			} else {
				if(!get_option( 'kommiku_url_index' ))
				add_option("kommiku_url_index", 'directory');
			
				update_option('kommiku_url_index', 'directory');
				$settings['directory'] = 'directory';
			}
			
			if($_POST['override_index'] == 1){
				update_option('kommiku_override_index', true);
				$settings['kommiku_override_index'] = true;
			} else {
				update_option('kommiku_override_index', false);
				$settings['kommiku_override_index'] = false;
			}
			
			if(!get_option( 'kommiku_skin_directory' ))
				add_option("kommiku_skin_directory", 'default');
			
			if($_POST['url'] == "")
				update_option('kommiku_no_slug', 'true');
			else
				update_option('kommiku_no_slug', 'false');
			
			
			if($_POST['apikey']) {
				update_option('K_A_K', $_POST['apikey']);
				$settings['key'] = $_POST['apikey'];
				if(!get_option( 'K_A_K' ))
					add_option("K_A_K", $_POST['apikey']);
			} else {
				update_option('K_A_K', '');
				$settings['key'] = $_POST['apikey'];
			}
			
			if($settings['one_comic'] != "")
				update_option('kommiku_one_comic', $settings['one_comic']);
			else
				update_option('kommiku_one_comic', 'false');
								
			//Remove Trialing and Leading Slash
			$settings['url'] = $db->trail($settings['url']);
			$settings['upload'] = $db->trail($settings['upload']);
			
			//Check if the Directory Already Exist
			$oldName = WP_LOAD_PATH.'/'.get_option( 'kommiku_comic_upload' );
			$newName = WP_LOAD_PATH.'/'.$settings['upload'];
				
				if(is_dir($newName) && $oldName != $newName) {
						$settings['error'] = __("The 'Upload Directory' you are trying to rename already exist.", 'kommiku');
						$settings['upload'] = get_option( 'kommiku_comic_upload' );
						$settings['fail']['upload'] = true;
					} else if($oldName != $newName) {
						rename($oldName,$newName);
						update_option('kommiku_comic_upload', $settings['upload']);
					}
				
				if(is_dir(KOMMIKU_FOLDER.'/themes/'.$settings['skin'])) {
						$settings['pass'] = __("Your skin has been updated", 'kommiku');
						update_option('kommiku_skin_directory', $settings['skin']);
					} else {
						if($settings['error']) $settings['error'] .= '<br/>';
						$settings['error'] .= __('The skin does not exist', 'kommiku');
					}
					
				if(!$settings['fail']) $settings['pass'] = __("Your Settings has been updated", 'kommiku');
				update_option('kommiku_url_format', $settings['url']);
			}
			
	include KOMMIKU_FOLDER.'/admin/settings.php';
}
	

function install() {
	global $wpdb, $kommiku_version;
	
	//Plug Options
	$kommiku_options = array('kommiku_version','kommiku_comic_upload','kommiku_url_format','kommiku_lang','kommiku_skin_directory','kommiku_one_comic','kommiku_no_slug','kommiku_override_index','kommiku_url_index');
	$kommiku_default_values = array('2.1.rc1','comics','manga','english','default','false','false','false','directory');
	foreach($kommiku_options as $option)	{
		$i++;
		if(!get_option( $option )) {
			add_option ( $option, $kommiku_default_values[$i] );
		}
	}
	
	//Main Kommiku Folder!
	if(!is_dir(WP_LOAD_PATH."/".get_option( 'kommiku_comic_upload' )))
		mkdir(WP_LOAD_PATH."/".get_option( 'kommiku_comic_upload' ), 0755);

	//Create and Update the Tables
	$table = 'comic_series';		
	$attribute[$table] = array('id' => 'int(9) NOT NULL AUTO_INCREMENT',
				'title' => 'title varchar(100) NOT NULL',
				'slug' => 'varchar(100) NOT NULL',
				'summary' => 'text NOT NULL',
				'chapterless' => 'tinyint(1) NOT NULL',
				'categories' => 'text NOT NULL',
				'author' => 'varchar(32) NOT NULL',
				'illustrator' => 'varchar(32) NOT NULL',
				'read' => 'int(1) NOT NULL',
				'creation' => 'varchar(32) NOT NULL',
				'alt_name' => 'text NOT NULL',
				'status' => 'int(1) NOT NULL',
				'rating' => 'int(1) NOT NULL',
				'type' => 'int(1) NOT NULL',
				'img' => 'varchar(255) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_chapter';		
	$attribute[$table] = array('id' => 'INT(9) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR(100) NOT NULL',
				'number' => 'INT(5) NOT NULL',
				'summary' => 'TEXT NOT NULL',
				'series_id' => 'INT(9) NOT NULL',
				'pubdate' => 'VARCHAR(30) NOT NULL',
				'slug' => 'VARCHAR(100) NOT NULL',
				'scanlator' => 'VARCHAR(100) NOT NULL',
				'scanlator_slug' => 'VARCHAR(100) NOT NULL',
				'volume' => 'int(3) NOT NULL',
				'folder' => 'VARCHAR(100) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_page';		
	$attribute[$table] = array('id' => 'BIGINT(20) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR(100) NOT NULL',
				'slug' => 'VARCHAR(100) NOT NULL',
				'img' => 'VARCHAR(255) NOT NULL',
				'pubdate' => 'VARCHAR(30) NOT NULL',
				'number' => 'int(3) NOT NULL',
				'story' => 'TEXT NOT NULL',
				'series_id' => 'INT(9) NOT NULL',
				'chapter_id' => 'INT(9) NOT NULL',
				'wp_post_slug' => 'VARCHAR(160) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_category';		
	$attribute[$table] = array('id' => 'INT(3) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR( 32 ) NOT NULL',
				'slug' => 'VARCHAR( 32 ) NOT NULL',
				'summary' => 'TEXT NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$table = 'comic_scanlator';		
	$attribute[$table] = array('id' => 'INT(9) NOT NULL AUTO_INCREMENT',
				'title' => 'VARCHAR( 32 ) NOT NULL',
				'slug' => 'TEXT NOT NULL',
				'summary' => 'VARCHAR( 32 ) NOT NULL');
	$columns[$table] = array_keys($attribute[$table]);
	
	$tables = array_keys($columns);
	foreach ($tables as $table){
		foreach ($columns[$table] as $column) {
			if(!$wpdb->query("Show columns from `".$wpdb->prefix.$table."` like '".$column."'")) {
				if($column == 'id') {
					$query = "CREATE TABLE ".$wpdb->prefix.$table." (id ".$attribute[$table]['id'].", UNIQUE KEY id (id));";
				} else {
					$query = "ALTER TABLE `".$wpdb->prefix.$table."` ADD `".$column."` ".$attribute[$table][$column].";";	
				}
				$wpdb->query($query);	
			}
		}
	}
}

register_activation_hook(__FILE__, 'install');
	
add_shortcode( 'kommiku_series_list' , 'series_list' );
	function series_list() {
		global $wpdb;
		$table = $wpdb->prefix."comic_series";
	  	$select = "SELECT * FROM `".$table."` ORDER BY `title` ASC";
	  	$series_list = $wpdb->get_results( $select );
		if($series_list)
			foreach ($series_list as $row) {
				$theLIST .= '<li><a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$row->slug.'/">'.$row->title.'</a></li>';
			};	
			
		return $theLIST;
	}

add_shortcode( 'kommiku_chapter_update_list' , 'chapter_update_list' );
	function chapter_update_list() {
		require_once(KOMMIKU_FOLDER.'/admin/database.php');
		$db = new kommiku_database();
		$chapterUpdateList = $db->chapter_update_list();
		if($chapterUpdateList)
			foreach ($chapterUpdateList as $item) {
				$theLIST .= '<li>'.strftime('[%m.%d]',strtotime($item->pubdate)).' <a href="'.HTTP_HOST.KOMMIKU_URL_FORMAT.'/'.$item->series_slug.'/'.$item->chapter_slug.'/">'.$item->series_name.' - Chapter '.$item->chapter_slug.'</a></li>';
			};	
			
		return $theLIST;
	}
		
		
		
function kommiku_menu() {
	add_menu_page('Kommiku', 'Comic', 8, KOMMIKU_FOLDER, 'kommiku', KOMMIKU_URLPATH.'comic.png'); //Thanks Lokis :)
	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('List', 'kommiku'), 8, 'kommiku', 'kommiku'); 
	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Settings', 'kommiku'), 8, 'kommiku_settings', 'kommiku_settings'); 
	
	if(file_exists(KOMMIKU_ABSPATH . 'extension/ftp-dumper-p.php')) {
		add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Ftp Dump', 'kommiku'), 8, 'kommiku_ftp_dump', 'kommiku_ftp_dump'); 
	}
	
	if(get_option('kommiku_scanlator_enabled')) {
		add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Scanlators', 'kommiku'), 8, 'kommiku_scanlator', 'kommiku_scanlator'); 
	}

	add_submenu_page(KOMMIKU_FOLDER, 'Kommiku', __('Categories', 'kommiku'), 5, 'kommiku_category', 'kommiku_category'); 
		
	}
	
?>