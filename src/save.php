<?php
require_once("src/CContent.php");

$p = get_page();

$node = new CContent($p,false);
$node->update();

header("Location: $base_url/$p");
