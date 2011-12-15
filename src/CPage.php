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
  
  static function get_instance(){
  	CPage::$log.="<br/>\nget_instance()";
  	if (!CPage::$instance)
  	CPage::$instance=new CPage();
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
  public function __construct($css=false, $title=false) {
  	  $this->log("__construct($css, $title)");
    if (!$css) $css="stylesheet.css";
    if (!$title) $title="unnamed page";
    $this->iStylesheet = $css;
    $this->title = $title;
    $this->current_page = $_SESSION['current_page'];
    CPage::$instance = $this; //does this work?
    $this->iMenu = Array(
    	    'Hem' => 'main',
    	    'Info' => 'info',
    	    'Edit this page' => "edit/{$this->current_page}",
    	    'Source Code' => 'source.php'
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

  
  //----------------------------------------
  public function HTMLHeader($aTitle) {
  	  if (!$aTitle) $aTitle=$this->title;
    $html = <<<EOD
<!doctype html>
<html lang=sv>
<head>
<meta charset=utf-8>
<title>{$aTitle}</title>

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
            
  //$html = preg_replace('/\*\*\*(\w+)\*\*\*/', 'yope' , $html);  	          
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
  //
  //

  public function getStats() {
  include('pages/PStatistics.php');
  return $stats;
  }  
  
  public function getLatest() {
  include('pages/PLatest.php');
  return $latest;
  }
  
  public function getTags() {
  include('pages/PTags.php');
  return $tags;
  }
  
  public function getCalendar() {
  include("CCalendar.php");
  return $calendar;
  }
  
  
  public function getSidebar($sBody) { 
    //$calendar=$this->getCalendar();
    //$latest=$this->getLatest();
    //$tags=$this->getTags();
    //$stats = $this->getStats();
    $html=<<<END
    <div class="newPosts">
    $latest
    </div>
    <div class="tags">
    $tags
    </div>
    <div class="statistics">
    $stats
    </div>
    <div class="calendar">
    $calendar
    </div>
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
  public function footer($text=false) {
    if (($this->show_debug)&&($text)) $text ="<p>$text</p>";
    $html = <<<EOD
<footer>
<a href="http://validator.w3.org/check/referer">html</a>
<a href="http://jigsaw.w3.org/css-validator/check/referer?profile=css3">css</a>
<a href="source.php">source</a>
$text
</footer>
</body>
</html>
EOD;

    echo $html;
  }
  
  public function echoHTML(){
  echo $this->html;
  $this->html="";
  }
} // End of Of Class
