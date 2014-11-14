<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

---------------------------------------
------ About the AppAward Plugin ------
---------------------------------------

This plugin allows the system to create, assign, and work with user awards.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppAward {
	
	
/****** Create a new award type ******/
	public static function create
	(
		$title				// <str> The title of the award to create.
	,	$description		// <str> The description of the award.
	,	$rewardDesc			// <str> The description of the reward provided by the award.
	,	$siteHandle = ""	// <str> The site handle that provides this award (or "" if global award).
	,	$settings = array()	// <str:mixed> The setting rules, such as to provide rewards.
	)						// RETURNS <void>
	
	// AppAward::create($title, $description, $rewardDesc, [$siteHandle], [$settings]);
	{
		Database::query("REPLACE INTO `awards` (site_handle, title, description, reward_desc, settings_json) VALUES (?, ?, ?, ?, ?)", array($siteHandle, $title, $description, $rewardDesc, json_encode($settings)));
	}
	
	
/****** Get the award ID by the type ******/
	public static function getIDByType
	(
		$siteHandle			// <str> The site handle that assigned this award.
	,	$title				// <str> The title of the award to assign.
	)						// RETURNS <int> the ID of the award, or 0 on failure.
	
	// $awardID = AppAward::getIDByType($siteHandle, $title);
	{
		return (int) Database::selectValue("SELECT id FROM awards WHERE site_handle=? AND title=? LIMIT 1", array($siteHandle, $title));
	}
	
	
/****** Assign award to a user ******/
	public static function assignByID
	(
		$uniID				// <int> The UniID of the user to assign award to.
	,	$awardID			// <int> The award ID to assign to the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAward::assignByID($uniID, $awardID);
	{
		return Database::query("REPLACE INTO users_flair (uni_id, flair_id) VALUES (?, ?)", array($uniID, $awardID));
	}
	
	
/****** Assign award to a user based on the award type (instead of ID) ******/
	public static function assignByType
	(
		$uniID				// <int> The UniID of the user to assign award to.
	,	$siteHandle			// <str> The site handle that assigned this award.
	,	$title				// <str> The title of the award to assign.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAward::assignByType($uniID, $siteHandle, $title);
	{
		if($awardID = self::getIDByType($siteHandle, $title))
		{
			return AppAward::assignByID($uniID, $awardID);
		}
		
		return false;
	}
	
	
/****** Un-assign award from a user ******/
	public static function unassignByID
	(
		$uniID				// <int> The UniID of the user to un-assign award from.
	,	$awardID			// <int> The award ID to remove from the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAward::unassignByID($uniID, $awardID);
	{
		return Database::query("DELETE FROM users_flair WHERE uni_id=? AND flair_id=? LIMIT 1", array($uniID, $awardID));
	}
	
}
