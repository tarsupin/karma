<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// The user must be logged in
if(!Me::$loggedIn)
{
	exit;
}

$owner = Database::selectValue("SELECT uni_id FROM bank_accounts WHERE account_id=? LIMIT 1", array((int) $_POST['id']));
if($owner !== false)
{
	// Remove the user (checks are performed in the function)
	if(AppBank::removeShare((int) $owner, (int) $_POST['id'], Me::$id))
	{
		echo json_encode($_POST);
	}
}