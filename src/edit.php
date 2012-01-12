<?php
require_once("src/CNode.php");
require_once("src/CPage.php");
$page = CPage::get_instance();

$p = get_page();

$node = new Node($p);
	
$page->header($node->title);
$page->body("$html" . $node->editNode() );
$page->footer($log . $node->getLog());
