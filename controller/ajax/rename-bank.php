<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// The user must be logged in
if(!Me::$loggedIn)
{
	exit;
}

// Rename the bank account (checks are performed in the function)
if(AppBank::renameAccount(Me::$id, (int) $_POST['id'], $_POST['name']))
{
	echo json_encode($_POST);
}