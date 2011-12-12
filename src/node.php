<?php
require_once("src/CContent.php");
require_once("src/CPage.php");
$page = CPage::get_instance();

$match = $_SESSION['page_request'];
if (isset($match[1])) $p=$match[1];
else $p=0;
$node = new CContent($p);
	
$html="base: $base_url | ". $_SESSION['base_url'] . "theme: $theme";
$page->header($node->title);
$page->body("$html" . $node->content);
$page->footer($log);
