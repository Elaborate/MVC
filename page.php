<?php
require_once("config.php");
if file_exists ("../site/$theme.php") 
  include("../site/$theme.php");
else include("CPage.php");

