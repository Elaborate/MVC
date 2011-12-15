<?php
require_once("src/CContent.php");
require_once("src/CPage.php");
$page = Page::get_instance();
$p=get_page();
$log.="<br/>\npage: --$p--";
if(!$p) $p = 'page';
$node = new CContent($p);
	
//$html="base: $base_url | ". $_SESSION['base_url'] . "theme: $theme";
$page->header($node->title);
$page->body($node->content);
$page->footer("<pre>".$log.$node->getLog() . $page->getLog(). print_r($_SESSION, true) ."</pre>");
