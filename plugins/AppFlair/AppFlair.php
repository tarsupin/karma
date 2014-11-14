<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

---------------------------------------
------ About the AppFlair Plugin ------
---------------------------------------

This plugin allows the system to create, assign, and work with user flair.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppFlair {
	
	
/****** Create a new flair type ******/
	public static function create
	(
		$title				// <str> The title of the flair to create.
	,	$description		// <str> The description of the flair.
	,	$rewardDesc			// <str> The description of the reward provided by the flair.
	,	$siteHandle = ""	// <str> The site handle that provides this flair (or "" if global flair).
	,	$settings = array()	// <str:mixed> The setting rules, such as to provide rewards.
	)						// RETURNS <void>
	
	// AppFlair::create($title, $description, $rewardDesc, [$siteHandle], [$settings]);
	{
		Database::query("INSERT IGNORE INTO `flair` (site_handle, title, description, reward_desc, settings_json) VALUES (?, ?, ?, ?, ?)", array($siteHandle, $title, $description, $rewardDesc, json_encode($settings)));
	}
	
	
/****** Get the flair ID by the type ******/
	public static function getIDByType
	(
		$siteHandle			// <str> The site handle that assigned this flair.
	,	$title				// <str> The title of the flair to assign.
	)						// RETURNS <int> the ID of the flair, or 0 on failure.
	
	// $flairID = AppFlair::getIDByType($siteHandle, $title);
	{
		return (int) Database::selectValue("SELECT id FROM flair WHERE site_handle=? AND title=? LIMIT 1", array($siteHandle, $title));
	}
	
	
/****** Assign flair to a user ******/
	public static function assignByID
	(
		$uniID				// <int> The UniID of the user to assign flair to.
	,	$flairID			// <int> The flair ID to assign to the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppFlair::assignByID($uniID, $flairID);
	{
		return Database::query("REPLACE INTO users_flair (uni_id, flair_id) VALUES (?, ?)", array($uniID, $flairID));
	}
	
	
/****** Assign flair to a user based on the flair type (instead of ID) ******/
	public static function assignByType
	(
		$uniID				// <int> The UniID of the user to assign flair to.
	,	$siteHandle			// <str> The site handle that assigned this flair.
	,	$title				// <str> The title of the flair to assign.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppFlair::assignByType($uniID, $siteHandle, $title);
	{
		if($flairID = self::getIDByType($siteHandle, $title))
		{
			return AppFlair::assignByID($uniID, $flairID);
		}
		
		return false;
	}
	
	
/****** Un-assign flair from a user ******/
	public static function unassignByID
	(
		$uniID				// <int> The UniID of the user to un-assign flair from.
	,	$flairID			// <int> The flair ID to remove from the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppFlair::unassignByID($uniID, $flairID);
	{
		return Database::query("DELETE FROM users_flair WHERE uni_id=? AND flair_id=? LIMIT 1", array($uniID, $flairID));
	}
	
}
