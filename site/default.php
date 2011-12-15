<?php 
require_once("src/CPage.php");
class Page extends CPage{

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
  
	  public function footer($text=false) {
    if (($this->show_debug)&&($text)) $text ="<p>$text</p>";
    $html = <<<EOD
<footer>
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
 

}// End of Of Class
