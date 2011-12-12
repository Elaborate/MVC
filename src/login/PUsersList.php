<?php
$mysqli = connectMySQL();
//if ($_SESSION['groupUser']!="adm") return; //is this correct? Exit?

$query = <<< EOD
SELECT u.*, m.* FROM m6_User AS u, m6_GroupMember AS m 
WHERE m.GroupMember_idUser = u.idUser 
;
EOD;

$res = $mysqli->query($query) 
                    or die("<p>Could not query database,</p><code>{$query}</code>");
                                  
while ($row = $res->fetch_array()) //?
echo"<p>". print_r($row). "</p>";

$res->close();


