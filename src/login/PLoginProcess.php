<?php
$redirect = false;
  if (isset($_SESSION['lastCommand']))
  $redirect = $_SESSION['lastCommand'];
get_request('username', 'password');
// ---------------------------------------------------------------------------------------
//
// Destroy the current session (logout user), if it exists. 
//
require_once('FDestroySession.php');

// ---------------------------------------------------------------------------
//
// Prepare and perform a SQL query.
//
$tableUser = DB_PREFIX . 'User';
//SELECT u.*, m.* FROM m6_User AS u, m6_GroupMember AS m 
//WHERE m.GroupMember_idUser = u.idUser 
$query = <<< EOD
SELECT 
    u.node, 
    u.name
FROM blog_user AS u 
WHERE 
    u.name        = '{$username}' AND
    u.  passwordUser     = md5('{$password}') 
;
EOD;

$query2 = <<< EOD
SELECT 
    u.id AS node, 
    u.name,
    u.type AS groupUser
FROM Projekt_user AS u
WHERE 
    u.name        = '{$username}' AND
    u.password     = md5('{$password}');
EOD;

$res = $mysqli->query($query2) 
                    or die("<p>Could not query database,</p><code>{$query2}</code>");
                    
                    // -----------------------------------------------------------------------------
//
// Use the results of the query to populate a session that shows we are logged in
//
session_start(); // Must call it since we destroyed it above.
session_regenerate_id(); // To avoid problems 

$row = $res->fetch_object();

// Must be one row in the resultset
if($res->num_rows === 1) {
  $_SESSION['userID']       = $row->node;
  $_SESSION['idUser']       = $row->node;
  $_SESSION['name']         = $row->name;
  $_SESSION['groupUser']     = $row->groupUser;
  $_SESSION['errorMessage']  =  $error = "Grattis! Inloggningen lyckades; du är grupp $row->groupUser"; 
  
} else {
	$_SESSION['errorMessage']  =  $error="Inloggningen misslyckades" . "Rows: " . $res->num_rows . ", Objekt: " . print_r($res) . 
	"user= $name, password= $userPassword";
  $redirect       = '?action=login';
  
}
$res->close();

//redirect($redirect);
