<?php 
require_once("src/CNode.php");

Node::load("SELECT * FROM {$prefix}node WHERE type LIKE 'post'");
$html = Page::get_posts(Node::$nodes);
$page->header("Posts");
$page->body($html);
$page->footer("<pre>".$log. Node::getLog() . $page->getLog(). "Session: ". print_r($_SESSION, true) ."</pre>");



