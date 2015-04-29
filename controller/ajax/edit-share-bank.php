<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// The user must be logged in
if(!Me::$loggedIn)
{
	exit;
}
if($otherID = User::getIDByHandle($_POST['handle']))
{
	// Add the user (checks are performed in the function)
	if(AppBank::editShare(Me::$id, (int) $_POST['id'], $otherID))
	{
		$shared = AppBank::getOthers(Me::$id, (int) $_POST['id']);
		$sharedwith = array();
		if($shared != null)
		{
			foreach($shared as $key => $value)
			{
				$user = User::get((int) $key, "handle");
				$sharedwith[] = '<a href="javascript:removePermission(' . $_POST['id'] . ', \'' . $user['handle'] . '\');" title="Remove Access"><span class="icon-trash"></span></a> <a href="javascript:editPermission(' . $_POST['id'] . ', \'' . $user['handle'] . '\');" title="Edit Access"><span class="icon-settings"></span></a> ' . $user['handle'] . " (" . ($value ? "Full Access" : "Deposit Only") . ")";
			}
		}
		if(count($sharedwith) <= 9)
			$sharedwith[] = '<a href="javascript:addPermission(' . $_POST['id'] . ');" title="Add User"><span class="icon-attachment"></span></a>';
		
		$_POST['shared'] = implode(", ", $sharedwith);
		
		echo json_encode($_POST);
	}
}