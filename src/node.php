<?php
require_once("src/CNode.php");
require_once("src/CPage.php");
$page = Page::get_instance($css);
$p=get_page();
$log.="<br/>\npage: --$p--";
if(!$p) $p = 'page';
$node = new Node($p);
$tags = $node->getTags();	
$debug =  "<pre>".$log.$node->getLog() . $page->getLog(). "Session: ". print_r($_SESSION, true) ."</pre>";
//$html="base: $base_url | ". $_SESSION['base_url'] . "theme: $theme";
$page->set_meta($node);
$page->header($node->title);
$page->body($node->content);
$page->footer($debug, $tags);
