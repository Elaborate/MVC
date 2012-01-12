<?php

$body='
<pre>
MODEL
The Node class loads nodes - the variables, methods for loading different kinds of nodes etc.
Tags, Posts and Comments are all different types of nodes.

VIEW
The CPage class processes nodes - making them into properly formatted posts, comments, etc - and also contains various webpage shortcuts, such as header and footer. 
By extending this class, you can control the design of the page.

CONTROLLER
index.php is the main controller page. 
Note that if site/config.php is missing, it will load up install.php to create a new one, reset the database, etc.
sub-controllers:
*node.php - view a given node. The default option.
*edit.php - edit existing pages  EXAMPLE: Projekt/edit/main
*save.php - save page, update sitemap.xml

pages: 
*posts.php - view the latest posts
*help.php - this help page
-------------------------------------
-------------------------------------

NODE       [src/CNode.php]

Node($id="main", $load=1, $obj=false) 
$id: id - or name - of node to load from database
$load: set as false if node is not to be loaded from database (as in, when creating a new node). 
$obj: when loading a node from an already existing object
Static variables:
Node::$nodes: ................ Every node created is accessible from here.
Node::$log: .....................This variable contains a log file for things done by class members.
Static functions: 
Node::load($sql): loads nodes from database (according to query in $sql parameter ),  stores them in $nodes. Returns $nodes
normal functions: 
getLog()................. returns the log
getTags()................returns tags for the current node

----------------

CPAGE     [src/CPage.php]
singleton class

CPage::get_instance()............. returns CPage instance
CPage::get_posts($node_array)..... returns nodes formatted as posts
CPage::post($node)................ formats a single node as a post.
CPage::comment($node)............. formats a single node as a comment.
CPage::getPostsRSS($node_array)... formats nodes in RSS form

getLog()................... returns the log file

set_meta($node)............. sets page keywords and content by a specific node.
HTMLHeader($title).......... echoes the <head></head> part of the file. 
PageHeader() ............... echoes the title header, and title menu.
header($title).............. echoes both of the above
body($aBody)................ echoes page body/content
footer($html)............... echoes page footer, containing debug info and useful links.

postForm().............. returns form for posting
commentForm()........... returns form for comments
editUser().............. returns form for editing user
getUserMenu()........... returns login menu.

menu($array)..................... dynamic menu creator 
replace_tags_with_php($html)
</pre>';

$page->header('help page'); 
$page->body($body);
$page->footer();
