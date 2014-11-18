<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

---------------------------------------
------ About the AppFlair Plugin ------
---------------------------------------

This plugin allows the system to create, assign, and work with user flair.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppFlair {
	
	
/****** Get the flair data ******/
	public static function getData
	(
		int $flairID		// <int> The flair ID to pull data from.
	): array <str, mixed>					// RETURNS <str:mixed> the data of the flair, or array() on failure.
	
	// $flairData = AppFlair::getData($flairID);
	{
		return Database::selectOne("SELECT * FROM flair WHERE id=? LIMIT 1", array($flairID));
	}
	
	
/****** Get the user's flair data ******/
	public static function getUserList
	(
		int $uniID		// <int> The UniID to get flair data for.
	): array <int, array<str, mixed>>				// RETURNS <int:[str:mixed]> the list of flair data for the user, or array() on failure.
	
	// $flairData = AppFlair::getUserList($uniID);
	{
		return Database::selectOne("SELECT f.* FROM users_flair uf INNER JOIN flair f ON uf.flair_id=f.id WHERE uf.uni_id=?", array($uniID));
	}
	
	
/****** Create a new flair type ******/
	public static function create
	(
		string $title				// <str> The title of the flair to create.
	,	string $description		// <str> The description of the flair.
	,	string $color				// <str> The color to assign to the flair.
	,	string $rewardDesc			// <str> The description of the reward provided by the flair.
	,	string $siteHandle = ""	// <str> The site handle that provides this flair (or "" if global flair).
	,	array <str, mixed> $settings = array()	// <str:mixed> The setting rules, such as to provide rewards.
	,	string $iconClass = ""		// <str> The icon class to assign to this flair.
	): void						// RETURNS <void>
	
	// AppFlair::create($title, $description, $color, $rewardDesc, [$siteHandle], [$settings], [$iconClass]);
	{
		Database::query("INSERT IGNORE INTO `flair` (site_handle, title, icon_class, color, description, reward_desc, settings_json) VALUES (?, ?, ?, ?, ?, ?, ?)", array($siteHandle, $title, $iconClass, $color, $description, $rewardDesc, json_encode($settings)));
	}
	
	
/****** Get the flair ID by the type ******/
	public static function getIDByType
	(
		string $siteHandle			// <str> The site handle that assigned this flair.
	,	string $title				// <str> The title of the flair to assign.
	): int						// RETURNS <int> the ID of the flair, or 0 on failure.
	
	// $flairID = AppFlair::getIDByType($siteHandle, $title);
	{
		return (int) Database::selectValue("SELECT id FROM flair WHERE site_handle=? AND title=? LIMIT 1", array($siteHandle, $title));
	}
	
	
/****** Assign flair to a user ******/
	public static function assignByID
	(
		int $uniID				// <int> The UniID of the user to assign flair to.
	,	int $flairID			// <int> The flair ID to assign to the user.
	,	int $addTime = 0		// <int> The number of seconds to add to the expiration time (or 0 to never expire).
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppFlair::assignByID($uniID, $flairID, [$addTime]);
	{
		$expires = 0;
		
		// Check if you're adding time or not
		if($addTime)
		{
			// Get the expiration time of the existing flair value (if applicable)
			if(!$getData = Database::selectOne("SELECT uni_id, expires FROM users_flair WHERE uni_id=? LIMIT 1", array($uniID)))
			{
				$expires = time() + $addTime;
			}
			else
			{
				if($getData['expires'] > time())
				{
					$expires = (int) $getData['expire'] + $addTime;
				}
				else
				{
					$expires = time() + $addTime;
				}
			}
		}
		
		// Assign the flair
		return Database::query("REPLACE INTO users_flair (uni_id, flair_id, expires) VALUES (?, ?, ?)", array($uniID, $flairID, $expires));
	}
	
	
/****** Assign flair to a user based on the flair type (instead of ID) ******/
	public static function assignByType
	(
		int $uniID				// <int> The UniID of the user to assign flair to.
	,	string $siteHandle			// <str> The site handle that assigned this flair.
	,	string $title				// <str> The title of the flair to assign.
	,	int $addTime = 0		// <int> The number of seconds to add to the expiration time (or 0 to never expire).
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppFlair::assignByType($uniID, $siteHandle, $title, [$addTime]);
	{
		if($flairID = self::getIDByType($siteHandle, $title))
		{
			return AppFlair::assignByID($uniID, $flairID, $addTime);
		}
		
		return false;
	}
	
	
/****** Un-assign flair from a user ******/
	public static function unassignByID
	(
		int $uniID				// <int> The UniID of the user to un-assign flair from.
	,	int $flairID			// <int> The flair ID to remove from the user.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppFlair::unassignByID($uniID, $flairID);
	{
		return Database::query("DELETE FROM users_flair WHERE uni_id=? AND flair_id=? LIMIT 1", array($uniID, $flairID));
	}
	
	
/****** Pull all of a user's flair rewards and compile them ******/
	public static function compileUserFlairRewards
	(
		int $uniID			// <int> The UniID to get the flair rewards for.
	): array <str, mixed>					// RETURNS <str:mixed> the reward results.
	
	// $rewardResults = AppFlair::compileUserFlairRewards($uniID);
	{
		// Prepare Values
		$rewards = array(
			"free_auro_per_day" => 0
		);
		
		// Get the list of flair for the user
		$flairList = Database::selectMultiple("SELECT settings_json FROM users_flair uf INNER JOIN flair f ON uf.flair_id=f.id WHERE uf.uni_id=?", array($uniID));
		
		foreach($flairList as $flair)
		{
			$settingData = json_decode($flair['settings_json'], true);
			
			if(isset($settingData['free_auro_per_day']))
			{
				$rewards["free_auro_per_day"] += $settingData['free_auro_per_day'];
			}
		}
		
		return $rewards;
	}
	
}