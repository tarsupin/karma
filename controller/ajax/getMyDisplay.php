<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare a response
header('Access-Control-Allow-Origin: *');

// Make sure the appropriate data was sent
if(!isset($_POST['username']) or !isset($_POST['enc']))
{
	exit;
}

// Make sure the encryption passed
if($_POST['enc'] != Security::jsEncrypt($_POST['username']))
{
	exit;
}

// Retrieve the UniID
if(!$uniID = User::getIDByHandle($_POST['username']))
{
	// Attempt to silently register the user so that the functions can work appropriately
	if(!User::silentRegister($uniID))
	{
		exit;
	}
}

// Get the bookmark list
if(!$bookmarkList = AppBookmarks::getUserList($uniID))
{
	$bookmarkList = AppBookmarks::fetchDefaultBookmarks();
}

// Update any auro
AppAuro::allotAuro($uniID);

// Get the user's auro count
$auro = Database::selectValue("SELECT auro FROM users_auro WHERE uni_id=? LIMIT 1", array($uniID));

// Display the bookmark list
echo json_encode(array("auro" => number_format($auro), "bookmarks" => $bookmarkList)); exit;