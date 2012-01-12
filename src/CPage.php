<?php
// ========================================================================================
//
// Class CPage
// Creating and printing out a HTML page.
//

class CPage {

  // ------------------------------------------------------------------------------------
  //
  // Internal variables
  //
  static $instance = false;
  
  static function get_instance($css){
  	CPage::$log.="<br/>\n get_instance()";
  	if (!CPage::$instance)
  	CPage::$instance=new CPage($css);
  	return CPage::$instance;
  }
  
  protected $html="";
  protected $show_debug=true;
  protected $show_menu=true;
  protected $iMenu = Array ();    
  protected $iStylesheet;
  protected $title;

  public static $log="Log for Node class";
  protected function log($text=""){  CPage::$log.="<br/>\n$text";}
  public function getLog(){ return "<br/>\n" . CPage::$log;}
  protected function shutdown($text=""){die("$text" . $this->getLog() );}
  
  // ------------------------------------------------------------------------------------
  //
  // Constructor
  //
  private function __construct($css=false, $title=false) {
  	  $this->log("__construct($css, $title)");
    if (!$css) $css="stylesheet.css";
    if (!$title) $title="unnamed page";
    $this->iStylesheet = $css;
    $this->title = $title;
    $this->current_page = $_SESSION['current_page'];
    $this->base_url = $_SESSION['base_url'];
    $this->iMenu = Array(
    	    'Hem' => $this->base_url,
    	    'Help' => $this->base_url.'/help',
    	    'Edit this page' => "{$this->base_url}/edit/{$this->current_page}",
    );
  }


  // ------------------------------------------------------------------------------------
  //
  // Destructor
  //
  public function __destruct() {
    //echo "\n<br>ending";
  }


  // ------------------------------------------------------------------------------------
  //
  //
  public function getErrorMessage() {
        $html = "";
        if(isset($_SESSION['errorMessage'])) {
        $message = $_SESSION['errorMessage'];
            $html = <<<EOD
<div class='errorMessage'>
$message
</div>
EOD;
            unset($_SESSION['errorMessage']);
        }
        return $html;   
    }

  //-----------------------------------------
  public static function get_posts($nodes=false){
	CPage::log("get_posts()");
	$html="";
	if (is_array($nodes))
	foreach($nodes AS $i)
		$html.=CPage::post($i);
	return $html;
	}
  //----------------------------------------
  public function HTMLHeader($aTitle) {
  	  $tags=$this->tags;
  	  $content=$this->content;
  	  if (!$aTitle) $aTitle=$this->title;
    $html = <<<EOD
<!doctype html>
<html lang=sv>
<head>
<title>{$aTitle}</title>
<META charset=utf-8>
$content
$tags

<link rel="stylesheet" href="blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="blueprint/print.css" type="text/css" media="print">
<!--[if lt IE 8]>
  <link rel="stylesheet" href="blueprint/ie.css" type="text/css" media="screen, projection">
<![endif]--></code></pre>

<link 
    rel='stylesheet' 
    href='{$this->iStylesheet}' 
    type='text/css' 
    media='screen'
/>
</head>
EOD;

    echo $html;
  }

  // ------------------------------------------------------------------------------------
  //
  //
  //
  public function header($title) {
  	  $this->HTMLHeader($title);
  	  $this->PageHeader($title);
  }
  
  public function echoPage($body){
  	  $this->header(false);
  	  $this->body($body);
  	  $this->footer();
  }
  
  public function SetLinks() {
  $this->iMenu=Array();
  $arr=func_get_args();
  $i=0;
  while (isset($arr[$i+1]) )
  	  $this->iMenu[$arr[$i++]] = $arr[$i++];
  $this->iMenu['Källkod']='source.php';
  }
  
  
  public function getUserMenu() {
  	  $base_url=$_SESSION['base_url'];
    if (!$this->loggedIn()) 
      $log = "<a href='$base_url/login'>log in</a>";
else {
	
	$name= $_SESSION['accountUser'];
	$log = "$name | <a href='?action=logout'>log out</a> | <a href='$base_url/userpage'>my account</a>";
	}
  
  $html=<<<END
  <div class="login">
  $log
  </div>
END;
  	
	return $html;
  }
  
  //-----------------------------------
  
  protected function replace_tags_with_php($html){
  	  $this->log("replace_tags_with_php($html)");
  	  $html = preg_replace_callback(
            '/\*\*\*(\w+)\*\*\*/',
            array($this, 'replace_star_tag')
            , $html);
          
          $html = preg_replace_callback(
          	  '|\[(\w+)\](.+)\[\/\w+\]|',
            array($this, 'replace_tag')
            , $html);
                      
  return $html;
  }
  
  protected function replace_tag($match){
  	  $this->log("replace_tag(".print_r($match,true).")");
  	  echo ("TEST replace_tag:".print_r($match,true)."!");
    switch($match[1])
    {
    case 'menu': return $this->menu($match[2]);
    	    break;
    case 'test': return "another sucessfull test";
    	    break;
    case 'sidebar': return $this->sidebar($match[2]);
    	    break;
    default: return "[$match[1]]{$match[2]}[/$match[1]]"; 
  	  }
  }

  protected function replace_star_tag($tag){
  	  $this->log("replace_star_tag(".print_r($tag,true)-")");
  	  echo( "TEST replace_star_tag:".print_r($tag,true)."!");
    switch($tag[1])
    {
    case 'current_date': return time();
    	    break;
    case 'test': return "<h2>This text was successful!</h2>";
    	    break;
    	    default: return "***{$tag[1]}***"; 
  	  }
  }
  
  //---------------------------------
  
  public function loggedIn($title=false) {
  return false;
  }
  
  public function PageHeader($title=false) {
  if (!$title) $title=$this->title;
    $menu = "";
    if ($this->show_menu)
    foreach($this->iMenu as $key => $value) {
      $menu .= "<a href='{$value}'>{$key}</a> | ";
    }
    $menu = substr($menu, 0, -3);
    $user = $this->getUserMenu();
    $error = $this->getErrorMessage();
    $html = <<<EOD
<body>
<div class='pageHeader'>
<h1>$title</h1>
<h2>$error</h2>
{$user}
<div class='pageHeaderMenu'>
{$menu}

</div>
</div>
EOD;

    echo $html;  
  }

  // ------------------------------------------------------------------------------------
  // 
  
  public function getSidebar($sBody) { 
    $html=<<<END
END;
  return $html;
  }
  
  public function body($aBody, $sBody="") {
$htmlErrorMessage = $this->getErrorMessage();
$sidebar = $this->getSidebar($sBody);

$aBody = $this->replace_tags_with_php($aBody);

    $html = <<<EOD
<div class='content'>
$htmlErrorMessage
<section>
{$aBody}
</section>
<aside>
$sidebar
</aside>

</div>
EOD;

    print $html;
  }

  // ------------------------------------------------------------------------------------
  //
  //
  //
  public function footer($text=false, $tags=false) {
  	  $tag_list="";
    if (($this->show_debug)&&($text)) $text ="<p>$text</p>";
    if ($tags){
    	    $list=array();
    	    foreach ($tags AS $i) 
    	    $list[] = "<a href='$base_url/tag/$i'>$i</a>"; 
    	    $tag_list = implode(' | ', $list);
    }
    $html = <<<EOD
<footer>
<a href="http://validator.w3.org/check/referer">html</a>
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">css</a>
<a href="source.php">source</a>
<a href="sitemap.xml">sitemap</a>
$text
$tag_list
</footer>
</body>
</html>
EOD;
    echo $html;
  }

  public function echoHTML(){
  /*
  $this->header($this->title);
  $this->body();
  $this->footer($this->getLog());
  */
  }
  
  public static function getPosts($array){
  foreach ($array as $i)
  	  $html.=CPage::post($i);
  	  return $html;
  }
  
  public static function post($row=false){
  	  CPage::log("post(object)");  
  	$text=$row->content;
	if (!$text) return "";
	$node=$row->id;
	$author=$row->user_id;
	$title=$row->title;
	$tags=$row->tags;
	//$nr=$this->getNumberOfComments($node);
	//if ($nr>0) $nr = "<a href='?p=$node'>$nr comments</a>";
	CPage::log("post $node: '$title'");
	$html=<<<END
	<article>
	<header>
	<h1><a href='?p=$node'>$title</a></h1>
	<h2>by: <a href='?p=$author'>$name</a></h2>
	</header>
	<p>$text</p>
	<footer>
	$nr
	Tags: $tags
	<br/><span>Posted: $date</span>
	</footer>
	</article>
END;
return $html;	  
  return $html;
  }
  //----------------------------
  
  public function set_meta($node){
  	  if (!is_object($node)) return false;
  if ($node->tags_text) $this->tags = 
  	  "<META name='keywords' 
  		 content='". $node->tags_text ."'>";
  		 else $this->log("No tags found");
  if ($node->content) $this->content = 
  	  "<META name='description' 
  		 content='". substr($node->content, 0, 100)."'>";
  }
  //--------------------------
  
  public function postRSS($node){
	$text=$node->content;
	if (!$text) return "";
	$name=$node->name;
	$author=$node->user_id;
	$title=$node->title;
	//$name=$node->name;
	$date=$node->edited;
	
	$html=<<<END
        <item>
                <title>$title</title>
                <description>$text</description>
                <link>{$base_url}/node/$name</link>
                <guid>$name</guid>
                <pubDate>$date</pubDate>
        </item>
END;
return $html;
}

public static function getPostsRSS($nodes){
	$html="";
	foreach ($nodes AS $row)
		$html .= CPage::postRSS($row);

return<<<END
<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
        <title>Two Thirds Done</title>
        <description>The blog posts of a procrastinator</description>
        <link>$base_url</link>
        <lastBuildDate>Mon, 06 Sep 2010 00:01:00 +0000 </lastBuildDate>
        <pubDate>Mon, 06 Sep 2009 16:45:00 +0000 </pubDate>
 $html
</channel>
</rss>
END;
}

public function comment($row){
	$text=$row->text;
	if (!$text) return "";
	$idPost=$row->node;
	$author=$row->idUser;
	$email=$row->email;
	$title=$row->title;
	$pos=strpos($email, '@');
	$name=substr($email, 0, $pos);	
	$html=<<<END
	<div class="comment">
	  <header>
	  <a href='?p=$node'><h2>$title</h2>
	  <h3>by: $name</h3></a>
	  <button type=submit name=grade value=1>+</button>
	  <button type=submit name=grade value=0>-</button>
	  </header>
	  <p>$text</p>
	</div>
	  <footer>
	<a class="reply" href="?action=comment&amp;node=$idPost"> 
	
	</footer>
END;
return $html;
}

public function postForm($id=false){
  $html="";
//  if (loggedIn()=='usr')
  $html.=<<<END
	<fieldset>
	  <legend>Add Post</legend>
	  <form action='{$this->base_url}' method='get'>
	<p>Title: <input type="text" name="title" value=""/></p>
	<p>Post: <textarea name="blog_text"></textarea></p>
	<p>Tags: <input type="text" name="tags" value=""/></p>
	<button type="submit" 
		name="action" 
		value="post">
		Add Post</button>
	  </form>
	</fieldset>
END;
	return $html;
}



public function editUser($row){
	$name=$row->name;
	$idPost=$row->email;
	$idUser=$row->idUser;
	$title=$row->title;
	$name=$row->name;

$html=<<<END
	<fieldset>
	  <legend>Edit Post</legend>
	  <form action='{$_SERVER['PHP_SELF']}' method='get'>
	Name: <input type="text" name="title" value="$name"/>
	Email: <input type="text" name="tags" value="$email"/>
	<button type="submit" 
		name="action" 
		value="editUser">
		Add Post</button>
	  </form>
	</fieldset>
END;

return $html;
}

public function commentForm($id){
  $html="";
  $html.=<<<END
	<fieldset>
	  <legend>Add Comment</legend>
	  <form action='{$this->base_url}/add' method='get'>
	      <input type=hidden name=node value='$id' />
	<p>Title: <input type="text" name="title" value=""/></p>
	<p>Email: <input type="text" name="email" value=""/></p>
	<p>Text: <textarea name="text"/></textarea></p>
	<button type="submit" 
		name="action" 
		value="comment">
		Add Comment</button>
	  </form>
	</fieldset>
END;
	return $html;
}

public function menu(){
	$arr=func_get_args();
	foreach($arr as $i=>$url)
		$list.="<a href='$url'><div class='menu_button'>$text</div></a>";
	echo $list;
}

} // End of Of Class
