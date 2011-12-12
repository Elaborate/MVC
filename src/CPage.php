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
  	if (!CPage::$instance)
  	CPage::$instance=new CPage();
  	return CPage::$instance;
  }
  
  private $html="";
  
  protected $iMenu = Array (
      'Hem' => 'Main',
      'Info' => 'Info',
      'Source Code' => '/source.php'
    );
    
  protected $iStylesheet;
  
  protected $title;

  // ------------------------------------------------------------------------------------
  //
  // Constructor
  //
  public function __construct($css=false, $title=false) {    
    if (!$css) $css="stylesheet.css";
    if (!$title) $title="unnamed page";
    $this->iStylesheet = $css;
    $this->title = $title;
    CPage::$instance = $this; //does this work? 
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

<link rel="stylesheet" href="css/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="css/blueprint/print.css" type="text/css" media="print">
<!--[if lt IE 8]>
  <link rel="stylesheet" href="css/blueprint/ie.css" type="text/css" media="screen, projection">
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
    if (!loggedIn()) 
      $log = "<a href='?action=login'>log in</a>";
else {
	
	$name= $_SESSION['accountUser'];
	$log = "$name | <a href='?action=logout'>log out</a> | <a href='?action=userpage'>my account</a>";
	}
  
  $html=<<<END
  <div class="login">
  $log
  </div>
END;
  	
	return $html;
  }
  
  
  public function loggedIn($title=false) {
  return false;
  }
  
  public function PageHeader($title=false) {
  if (!$title) $title=$this->title;
    $menu = "";
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
    if ($text) $text ="<p>$text</p>";
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
