<?php
//$html="<h1>Test" . $error . $_SESSION['errorMessage'] . "Test</h1>";
$html =<<<END
<h1> Log In </h1>
<form action='{$_SERVER['PHP_SELF']}' method='get'>
<P>Name: <input type=text name=username></P>
<P>Name: <input type=password name=password></P>
<p><button type=submit name=action value="login">Log In</button></p>
</form>

END;
