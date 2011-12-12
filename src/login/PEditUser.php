<?php

// ---------------------------------------------------------------------------
// Prepare and perform a SQL query.

if ($_SESSION['userName']!=$userID) die("Can only manage your own account");

get_request('name', 'email', 'password');

$query=<<<END
UPDATE blog_user 
SET (name=$name, email=$email)
WHERE node=$node;
END;

$res = $mysqli->query($query) 
                    or die("<p>Could not query database,</p><code>{$query}</code>");
