<?php
session_start();
//Note: rewrite index.php to optimize itself later?

/* SETUP */
require_once("src/funktioner.php");

require_either("site/config.php", "src/install.php");
require_either("site/$theme.php", "src/CPage.php"); 

sanitize();

$_SESSION['base_url'] = $base_url; 
$log.="base_url: $base_url, theme: $theme";
$mysqli = connectMySQL();
$html="test";

$node=false;
$page = new CPage($css);



/* LOGIN */ 
// login should be done first, to ease return to previous page
get_request('username', 'password');
if ($username && $password) 
	include('src/login/PLoginProcess.php');



/* FRONT CONTROLLER */

$choice = url_chopper($_SERVER['REDIRECT_URL']);
$path = "src/node"; //default controller

if ($choice){ 
	if (file_exists("site/$choice.php"))
	$path = "site/$choice";
	else if (file_exists("src/$choice.php"))
	$path = "src/$choice";	
	}
include("$path.php");

//if (file_exists("{$path}_view.php"))
//	include("{$path}_view.php");
	


/* PAGE CONTROLLER */

$page->echoHTML();
//Add code so this can only be done once



//-----------------------------
//-----------------------------

function url_chopper($url=false){ 
global $log;
	$ret=0;
preg_match("/Projekt\/(.*)/", $url, $match); //"Projekt" should be changed
$res = explode('/', $match[1]);
$_SESSION['page_request'] = $res;
if (isset($res[0])) return $res[0];
else return false;
}

function loggedIn(){
if (isset( $_SESSION['groupUser'] )) 
	return  $_SESSION['groupUser'];
else return false;
}

