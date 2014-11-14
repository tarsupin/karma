<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

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
		string $title				// <str> The title of the award to create.
	,	string $description		// <str> The description of the award.
	,	string $rewardDesc			// <str> The description of the reward provided by the award.
	,	string $siteHandle = ""	// <str> The site handle that provides this award (or "" if global award).
	,	array <str, mixed> $settings = array()	// <str:mixed> The setting rules, such as to provide rewards.
	): void						// RETURNS <void>
	
	// AppAward::create($title, $description, $rewardDesc, [$siteHandle], [$settings]);
	{
		Database::query("REPLACE INTO `awards` (site_handle, title, description, reward_desc, settings_json) VALUES (?, ?, ?, ?, ?)", array($siteHandle, $title, $description, $rewardDesc, json_encode($settings)));
	}
	
	
/****** Get the award ID by the type ******/
	public static function getIDByType
	(
		string $siteHandle			// <str> The site handle that assigned this award.
	,	string $title				// <str> The title of the award to assign.
	): int						// RETURNS <int> the ID of the award, or 0 on failure.
	
	// $awardID = AppAward::getIDByType($siteHandle, $title);
	{
		return (int) Database::selectValue("SELECT id FROM awards WHERE site_handle=? AND title=? LIMIT 1", array($siteHandle, $title));
	}
	
	
/****** Assign award to a user ******/
	public static function assignByID
	(
		int $uniID				// <int> The UniID of the user to assign award to.
	,	int $awardID			// <int> The award ID to assign to the user.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAward::assignByID($uniID, $awardID);
	{
		return Database::query("REPLACE INTO users_flair (uni_id, flair_id) VALUES (?, ?)", array($uniID, $awardID));
	}
	
	
/****** Assign award to a user based on the award type (instead of ID) ******/
	public static function assignByType
	(
		int $uniID				// <int> The UniID of the user to assign award to.
	,	string $siteHandle			// <str> The site handle that assigned this award.
	,	string $title				// <str> The title of the award to assign.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
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
		int $uniID				// <int> The UniID of the user to un-assign award from.
	,	int $awardID			// <int> The award ID to remove from the user.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppAward::unassignByID($uniID, $awardID);
	{
		return Database::query("DELETE FROM users_flair WHERE uni_id=? AND flair_id=? LIMIT 1", array($uniID, $awardID));
	}
	
}