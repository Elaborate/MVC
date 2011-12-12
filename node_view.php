<?php
 
$page->header($node->title);
$page->body("Yo HTML: $html" . $node->content);
$page->footer($log);
