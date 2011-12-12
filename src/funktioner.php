<?php

require_once("../../oophp/SQL/config.php");

function require_either($page=false, $default=false){
global $log;
$log.="starting require_either()... choices: '$page / $default'";
if (file_exists($page)){ 
  $log.="loading $page";
  include($page);
    	}
else if (file_exists ($default)){ 
  $log.="loading $default";
  include($default); 
}
else die("neither '$page' nor '$default' are available.");
}

/*
function alt($a=false, $b=false, $c=false){
	if (isset($a)) return $a;
	if (isset($b)) return $b;
	return $c;
}
*/
function exists ($value) {
  return (!(!$value && $value !== 0 && $value !== '0'));
}

 function alt($a, $b=false, $c=false){
   if (exists($a)) return $a;
   elseif (exists($b)) return $b;
   elseif (exists($c)) return $c;
   else return false;
}

function tell($string){
	global $debug;
	$debug.=$string."<br/>\n";
}

function form_get(){
	$arr=func_get_args();
	$ret=<<<END
	<form action='{$_SERVER['PHP_SELF']}' method='get'>
END;

	foreach($arr as $i=>$j)
		$ret.=<<<END
	$j: <input type="text" name="$j" value=""/><br/>
END;

$ret.=<<<END
	<button type="submit">Submit</button>
	</form>
END;
	return $ret;
}

function form_choice(){
	$arr=func_get_args();
	$ret=<<<END
	<form action='{$_SERVER['PHP_SELF']}' method='get'>
END;

	foreach($arr as $i=>$j)
		$ret.=<<<END
	<button type="submit" name="action" value="$j">$j</button>
	
END;
$ret.="</form>";
	return $ret;
}

function connectMySQL(){
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE); 
	
	if (mysqli_connect_error()) { 
		echo "Connect failed: ".mysqli_connect_error()."<br>"; 
		exit(); 
	} 
	$mysqli->set_charset("utf8");
	return $mysqli;
}

function fetch_query($query, $db=false){;
	global $mysqli;
	if (!$db) $db=$mysqli;
	$res = $db->query($query)  
        or die("Could not query database, query =<br/><pre>{$query}</pre><br/>{$db->error}");
        return $res;
}

function count_SQL($table){
$sql="SELECT COUNT(id) FROM $table";
$res=fetch_query($sql);
$arr=$res->fetch_array();
return $arr[0];
}

function form_hidden(){
	$arr=func_get_args();
	$ret="";
	foreach($arr as $i)
		$ret.="<input type='hidden' name='$i' value='{$GLOBALS[$i]}'/>\n";
	return $ret;
}

function dice($side=6){
	return rand(1, $side);
}

function many_dice($number=1, $side=6){
	for ($i=0;$i<$number;$i++)
	$ret[$i]= dice($side);
	return $ret;
}

function histogramShowValues($anArray) {
    $s = "";
    foreach($anArray as $key => $value) {
        $s .= "{$key} => {$value}<br />";
    }
    return $s;
}

function histogramPrintGraf($anArray){
	$his=array(1=>'','','','','','');
	$ret="";
	foreach($anArray as $i=>$j)
		$his[$j].='*';
	return histogramShowValues($his);
}
function destroySession(){endSession();}
function endSession(){
	$_SESSION = array();
	if (isset($_COOKIE[session_name()])) 
		setcookie(session_name(), '', time()-42000, '/');
	session_destroy();
}
function resetSession(){restartSession();}
function restartSession(){
	endSession();
	session_start();          
	session_regenerate_id();  // To avoid problems
}

function get_request(){
	$arr=func_get_args();
	foreach($arr as $i=>$j)
		if (!isset($_REQUEST[$j]))
		$GLOBALS[$j]=false;
		//die("Could not find $j in Requests!\n". print_r($_REQUEST));
		else $GLOBALS[$j] = $_REQUEST[$j];
}

function globalize_keys($list=false){
	global $log;
	$arr=func_get_args();
	$log .="...".print_r($list, true)."...";
	$log .="...".print_r($arr, true)."...";
	foreach($arr as $i=>$j){
		if ($i==0) continue;
		$log.=$j." \n";
		if (!isset($list[$j]))
		$GLOBALS[$j]=false;
	        else{ 
	        $GLOBALS[$j] = $_REQUEST[$j]; 
	        $log.="added $j";
	        }
	}
}

function request($x, $alt=""){
	if (!isset($_REQUEST[$x]))
		return $alt;
		else return $_REQUEST[$x];
}

function get_session(){
	$arr=func_get_args();
	foreach($arr as $i=>$var){	
        if(!array_key_exists($var,$_SESSION))
            $_SESSION[$var]='';
        $GLOBALS[$var]=&$_SESSION[$var];
        }
        /*
	$arr=func_get_args();
	foreach($arr as $i=>$j)
		if (!isset($_SESSION[$j])) 
		die("Could not find $j in Session!\n" . print_r($_SESSION));
		else $GLOBALS[$j] = $_REQUEST[$j]; */
}

function set_session(){
	$arr=func_get_args();
	foreach($arr as $i=>$j)
		if (!isset($GLOBALS[$j])) 
		die("Could not find $j in Globals!\n". print_r($GLOBALS));
		else $_SESSION[$j] = $GLOBALS[$j];
}
function upload_image(){return uploadImage();}
function uploadImage(){
      $allowed_filetypes = array('.jpg','.gif','.png'); 
      $max_filesize = 524288;
      $upload_path = './img/';
 
      //print_r($_FILES);
   $filename = $_FILES['userfile']['name']; 
   $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); 
 
   if(!in_array($ext,$allowed_filetypes)){
      echo 'Fel filtyp.';
      return false;
   }

   if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize){
      echo 'F�r stor';
      return false;
   }
   
   if(!is_writable($upload_path)){
      echo 'CHMOD 777.';
   return false;
   }
   if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . $filename))
   	 return "img/$filename";
      else
         echo 'Fel uppstod, f�rs�k igen.';
return false;
}

function mysql_escape_mimic($inp) {
    if(is_array($inp))
        return array_map(__METHOD__, $inp);

    if(!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }
    return $inp;
} 

function sanitize(){
$_REQUEST = array_map('trim', $_REQUEST);
if(get_magic_quotes_gpc())
    $_REQUEST = array_map('stripslashes', $_REQUEST);
$_REQUEST = array_map('mysql_escape_mimic', $_REQUEST); 
}
