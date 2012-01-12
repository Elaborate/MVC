<?php
require_once("src/CNode.php");

$p = get_page();

$node = new Node($p,false);
$node->update();
include("src/sitemap.php");
echo "This is SAVE.php <br/>\n" . $log . $node->getLog();
//header("Location: $base_url/$p");
