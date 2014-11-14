<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

------------------------------------------
------ About the AppWarnings Plugin ------
------------------------------------------

This plugin allows the system to create, assign, and work with user warnings.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppWarnings {
	
	
/****** Create a new warning type ******/
	public static function create
	(
		$title				// <str> The title of the warning to create.
	,	$description		// <str> The description of the warning.
	,	$rewardDesc			// <str> The description of the penalty provided by the warning.
	,	$siteHandle = ""	// <str> The site handle that provides this warning (or "" if global warning).
	,	$settings = array()	// <str:mixed> The setting rules, such as to provide rewards.
	,	$defExpire = 0		// <int> Number of seconds to expire, or 0 to not expire (default doesn't expire).
	)						// RETURNS <void>
	
	// AppWarnings::create($title, $description, $rewardDesc, [$siteHandle], [$settings]);
	{
		// Make sure there is a default expiration
		if(!isset($settings['expire']))
		{
			$settings['expire'] = $defExpire;
		}
		
		// Create the warning type
		Database::query("INSERT IGNORE INTO `warnings` (site_handle, title, description, reward_desc, settings_json) VALUES (?, ?, ?, ?, ?)", array($siteHandle, $title, $description, $rewardDesc, json_encode($settings)));
	}
	
	
/****** Get the warning ID by the type ******/
	public static function getIDByType
	(
		$siteHandle			// <str> The site handle that assigned this warning.
	,	$title				// <str> The title of the warning to assign.
	)						// RETURNS <int> the ID of the warning, or 0 on failure.
	
	// $warningID = AppWarnings::getIDByType($siteHandle, $title);
	{
		return (int) Database::selectValue("SELECT id FROM warnings WHERE site_handle=? AND title=? LIMIT 1", array($siteHandle, $title));
	}
	
	
/****** Assign warning to a user ******/
	public static function assignByID
	(
		$uniID				// <int> The UniID of the user to assign warning to.
	,	$warningID			// <int> The warning ID to assign to the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppWarnings::assignByID($uniID, $warningID);
	{
		return Database::query("REPLACE INTO users_warnings (uni_id, warning_id) VALUES (?, ?)", array($uniID, $warningID));
	}
	
	
/****** Assign warning to a user based on the warning type (instead of ID) ******/
	public static function assignByType
	(
		$uniID				// <int> The UniID of the user to assign warning to.
	,	$siteHandle			// <str> The site handle that assigned this warning.
	,	$title				// <str> The title of the warning to assign.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppWarnings::assignByType($uniID, $siteHandle, $title);
	{
		if($warningID = self::getIDByType($siteHandle, $title))
		{
			return AppWarnings::assignByID($uniID, $warningID);
		}
		
		return false;
	}
	
	
/****** Un-assign warning from a user ******/
	public static function unassignByID
	(
		$uniID				// <int> The UniID of the user to un-assign warning from.
	,	$warningID			// <int> The warning ID to remove from the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppWarnings::unassignByID($uniID, $warningID);
	{
		return Database::query("DELETE FROM users_warnings WHERE uni_id=? AND warning_id=? LIMIT 1", array($uniID, $warningID));
	}
	
}
