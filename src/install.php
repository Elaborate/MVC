<?php
$default_prefix="Projekt_";
$default_theme="default";
$default_css="";
$url = $_SERVER['PHP_SELF']; 
$default_base_url = "http://". $_SERVER['HTTP_HOST'] . preg_replace('|(/src/install.php.?)?(/index.php.?)?|', '', $url);

$default_db_user="rohg10";
$default_db_password="";
$default_db_database="rohg10";
$default_db_host="blu-ray.student.bth.se";
require_once("funktioner.php");
require_once("CPage.php");

get_request('prefix', 'theme', 'base_url', 'db_password','db_user','db_database','db_host');

if ($prefix&&$theme&&$base_url&&url_exists("$base_url/src/install.php"))
{
$file=<<<END
<?php
\$GLOBALS['prefix']="$prefix";
\$GLOBALS['theme']="$theme";
\$GLOBALS['base_url'] ="$base_url";
\$GLOBALS['css']=Array("$css");
\$GLOBALS['log'].="config loaded. theme: \$theme";

define('DB_USER',       '$db_user');  // <-- mysql server host
define('DB_PASSWORD',   '$db_password');  // <-- mysql password
define('DB_DATABASE',   '$db_database');    // <-- mysql db name
define('DB_HOST',       '$db_host');  // <-- mysql server host
END;
file_put_contents( "../site/config.php", $file);
// INSTALL SQL
header("Location: $base_url/src/install_sql.php");
}



else {
get_request('prefix', 'theme', 'base_url');
	$exists="no base_url".$base_url;
  if ($base_url)
  	  if (url_exists("$base_url/src/install.php"))
	$exists = "base_url seems to work.";
	else $exists = "base_url is non-functional ($base_url/src/install.php).";
$html=<<<END
	<fieldset>
	  <legend>Install config variables</legend>
	  <form action='{$_SERVER['PHP_SELF']}' method='get'>
	  Database Prefix: <input type="text" name="prefix" value="{$default_prefix}"/> <br/>
	Base Theme: <input type="text" name="theme" value="$default_theme"/> <br/>
	Base URL: <input type="text" name="base_url" value="$default_base_url"/><br/>
	$exists <br/>
	Default CSS: <input type="text" name="css" value="$default_css" /><br/><br/><br/>
	Database user: <input type="text" name="db_user" value="$default_db_user" /><br/>
	Database password: <input type="password" name="db_password" value="$default_db_password" /><br/>
	Database name: <input type="text" name="db_database" value="$default_db_database" /><br/>
	Database host: <input type="text" name="db_host" value="$default_db_host" /><br/>
	<button type="submit" 
		name="action" 
		value="post">
		Update Settings</button>
	  </form>
	</fieldset>
END;

$page = new CPage("");
$page->header("install page");
$page->body($html);
$page->footer("<pre>" . $log . $page->getLog() . print_r($_SESSION, true) . print_r($_REQUEST, true) ."</pre>");
}


