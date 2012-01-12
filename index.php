<?php
session_set_cookie_params(36000);
session_start();
//Note: rewrite index.php to optimize itself later?

# SETUP 

if (file_exists("site/config.php")){include("site/config.php");    	}
else if (file_exists ("src/install.php")){ 
	chdir("src" ); 
	include("install.php"); 
}

require_once("src/funktioner.php");


if (file_exists("site/$theme.php")) include("site/$theme.php");
else include("src/CPage.php"); 

sanitize();


$_SESSION['base_url'] = $base_url; 
$log.="base_url: $base_url, theme: $theme";
$mysqli = connectMySQL();
$html="test";

$node=false;



# LOGIN  
// login should be done first, to ease return to previous page
get_request('username', 'password');
if ($username && $password) 
	include('src/login/PLoginProcess.php');


# FRONT CONTROLLER 

$choice = strtolower(url_chopper($_SERVER['REDIRECT_URL']));
$log.="Choice is $choice <br/>\n";
$p = get_page();
$page = Page::get_instance($css);

$path = "src/node"; //default controller

if ($choice){ 
	if (file_exists("src/$choice.php"))
	$path = "src/$choice";
	else if (file_exists("site/$choice.php"))
	$path = "site/$choice";
	else if (file_exists("pages/$choice.php"))
	$path = "pages/$choice";
	}
$log.="Path is $path <br/>\n";	
include("$path.php");

//if (file_exists("{$path}_view.php"))
//	include("{$path}_view.php");
	


# PAGE CONTROLLER 

$page->echoHTML();
//Add code so this can only be done once



//-----------------------------
//-----------------------------

function url_chopper($url=false){ 
global $log;
$log.="url_chopper($url)\n";
	$ret=0;
preg_match("/Projekt\/(.*)/", $url, $match); //"Projekt" should be changed
$res = explode('/', $match[1]);
$_SESSION['page_request'] = $res;
if (isset($res[0])) return $res[0];

$log.="Going to Node!<br/>\n";
return false;
}

function loggedIn(){
if (isset( $_SESSION['groupUser'] )) 
	return  $_SESSION['groupUser'];
else return false;
}

function get_page(){
	global $log;
	$log.="get_page()\n";
	$match = $_SESSION['page_request'];
	$log.= print_r($match,true)."<br/>\n";
	if ((isset($match[1])) && (strlen($match[1])>0)) $p=$match[1];
	else if ((isset($match[0])) && (strlen($match[0])>0)) $p=$match[0];
	//else if (isset($_REQUEST['name'])) $p=$_REQUEST['name'];
	else $p='main';
	$log.="current page: $p";
	if (strlen($p)<1) $p='main'; 
	
	$_SESSION['current_page']=$p;
	$log.="current page: $p";
	$log.="page_request: ".print_r($_SESSION['page_request'],true);
	return $p;
}

