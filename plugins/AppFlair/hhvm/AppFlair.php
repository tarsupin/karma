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
	
	
/****** Check if the user has the flair or not ******/
	public static function hasFlair
	(
		int $uniID		// <int> The UniID to check if they have the flair or not.
	,	int $flairID	// <int> The flair ID to verify.
	): bool				// RETURNS <bool> TRUE if the user has this flair.
	
	// AppFlair::hasFlair($uniID, $flairID);
	{
		return (bool) Database::selectValue("SELECT uni_id FROM users_flair WHERE uni_id=? AND flair_id=? LIMIT 1", array($uniID, $flairID));
	}
	
	
/****** Get the flair data for a specific user's tag ******/
	public static function getUserFlairData
	(
		int $uniID				// <int> The UniID that possess the flair.
	,	int $flairID			// <int> The flair ID in possession.
	): array <str, mixed>						// RETURNS <str:mixed> the flair data.
	
	// $flairData = AppFlair::getUserFlairData($uniID, $flairID);
	{
		return Database::selectOne("SELECT uf.expires, uf.duration, f.* FROM users_flair uf INNER JOIN flair f ON uf.flair_id=f.id WHERE uf.uni_id=? AND uf.flair_id=? LIMIT 1", array($uniID, $flairID));
	}
	
	
/****** Get the user's flair data ******/
	public static function getUserList
	(
		int $uniID		// <int> The UniID to get flair data for.
	): array <int, array<str, mixed>>				// RETURNS <int:[str:mixed]> the list of flair data for the user, or array() on failure.
	
	// $flairList = AppFlair::getUserList($uniID);
	{
		return Database::selectMultiple("SELECT f.* FROM users_flair uf INNER JOIN flair f ON uf.flair_id=f.id WHERE uf.uni_id=?", array($uniID));
	}
	
	
/****** Get the list of flair for a specific site ******/
	public static function getSiteList
	(
		string $siteHandle		// <str> The site handle to pull flair from.
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]>
	
	// $flairList = AppFlair::getSiteList($siteHandle);
	{
		return Database::selectMultiple("SELECT * FROM flair WHERE site_handle=?", array($siteHandle));
	}
	
	
/****** Get the global list of flair ******/
	public static function getGlobalList (
	): array <int, array<str, mixed>>				// RETURNS <int:[str:mixed]>
	
	// $flairList = AppFlair::getGlobalList();
	{
		return Database::selectMultiple("SELECT * FROM flair WHERE site_handle=? ORDER BY title ASC", array(""));
	}
	
	
/****** Get the global list of flair ******/
	public static function getGlobalByCategories (
	): array <int, array<str, mixed>>				// RETURNS <int:[str:mixed]>
	
	// $flairList = AppFlair::getGlobalByCategories();
	{
		return Database::selectMultiple("SELECT * FROM flair WHERE site_handle=? ORDER BY category ASC, rank ASC, title ASC", array(""));
	}
	
	
/****** Get the global list of flair ******/
	public static function getFullList (
	): array <int, array<str, mixed>>				// RETURNS <int:[str:mixed]>
	
	// $flairList = AppFlair::getFullList();
	{
		return Database::selectMultiple("SELECT * FROM flair ORDER BY category ASC, rank ASC, title ASC", array(""));
	}
	
	
/****** Create a new flair type ******/
	public static function create
	(
		string $category			// <str> The category of the flair.
	,	string $title				// <str> The title of the flair to create.
	,	string $description		// <str> The description of the flair.
	,	string $color				// <str> The color to assign to the flair.
	,	string $rewardDesc			// <str> The description of the reward provided by the flair.
	,	string $siteHandle = ""	// <str> The site handle that provides this flair (or "" if global flair).
	,	array <str, mixed> $settings = array()	// <str:mixed> The setting rules, such as to provide rewards.
	,	string $iconClass = ""		// <str> The icon class to assign to this flair.
	): void						// RETURNS <void>
	
	// AppFlair::create($title, $description, $color, $rewardDesc, [$siteHandle], [$settings], [$iconClass]);
	{
		// Check if the flair already exists
		$check = Database::selectValue("SELECT title FROM flair WHERE site_handle=? AND title=? LIMIT 1", array($siteHandle, $title));
		
		if($check)
		{
			Database::query("UPDATE flair SET category=?, icon_class=?, color=?, description=?, reward_desc=?, settings_json=? WHERE site_handle=? AND title=? LIMIT 1", array($category, $iconClass, $color, $description, $rewardDesc, json_encode($settings), $siteHandle, $title));
		}
		else
		{
			Database::query("INSERT IGNORE INTO `flair` (category, site_handle, title, icon_class, color, description, reward_desc, settings_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", array($category, $siteHandle, $title, $iconClass, $color, $description, $rewardDesc, json_encode($settings)));
		}
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
		// Prepare Values
		$expires = 0;
		
		// Get the current flair
		if(!$flairData = AppFlair::getData($flairID))
		{
			return false;
		}
		
		// Get the flair data from the user, if applicable
		$userFlair = AppFlair::getUserFlairData($uniID, $flairID);
		
		// Check if you're adding time or not
		if($addTime)
		{
			// Extend the time of the existing flair value (if applicable)
			if($userFlair)
			{
				if($userFlair['expires'] > time())
				{
					$expires = (int) $userFlair['expires'] + $addTime;
				}
				else
				{
					$expires = time() + $addTime;
				}
			}
			else
			{
				$expires = time() + $addTime;
			}
		}
		
		// Check if the flair is an upgradable type
		// If it is, we remove all other ranks of this type and replace with the one assigned
		if($flairData['rank'])
		{
			Database::startTransaction();
			
			// Find all other instances of this rank
			$getRanks = array();
			$rankList = Database::selectMultiple("SELECT id FROM flair WHERE category=?", array($flairData['category']));
			
			foreach($rankList as $rl)
			{
				$getRanks[] = (int) $rl['id'];
			}
			
			// Identify the filters
			list($sqlWhere, $sqlArray) = Database::sqlFilters(array("uni_id" => array($uniID), "flair_id" => $getRanks));
			
			// Delete all instances that the user had this rank
			if($pass = Database::query("DELETE FROM users_flair WHERE " . $sqlWhere, $sqlArray))
			{
				// Add the new flair type
				$pass = Database::query("REPLACE INTO users_flair (uni_id, flair_id, expires, duration) VALUES (?, ?, ?, ?)", array($uniID, $flairID, $expires, $addTime));
			}
			
			return Database::endTransaction($pass);
		}
		
		// Assign the flair
		if($userFlair)
		{
			// Update the flair (standard way)
			return Database::query("UPDATE users_flair SET expires=?, duration=duration+? WHERE uni_id=? AND flair_id=? LIMIT 1", array($expires, $addTime, $uniID, $flairID));
		}
		else
		{
			return Database::query("REPLACE INTO users_flair (uni_id, flair_id, expires, duration) VALUES (?, ?, ?, ?)", array($uniID, $flairID, $expires, $addTime));
		}
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
	
	
/****** Draw a flair tag ******/
	public static function drawFlairTag
	(
		array <str, mixed> $flairData		// <str:mixed> The data of the flair to draw
	): string					// RETURNS <str> the HTML to draw the flair tag
	
	// AppFlair::drawFlairTag($flairData);
	{
		if(!$flairData) { return ''; }
		
		return '<div style="display:inline-block; font-size:0.9em; border-radius:6px; padding:4px; margin:3px; background-color:#' . $flairData['color'] . ';"><a href="' . URL::karma_unifaction_com() . '/flair/' . $flairData['title'] . '" style="color:#606060;"><span class="' . $flairData['icon_class'] . '" style="font-size:1.2em; vertical-align:middle;"></span> ' . $flairData['title'] . '</a></div>';
	}
	
	
/****** Draw a flair list ******/
	public static function drawList
	(
		array <int, array<str, mixed>> $flairList				// <int:[str:mixed]> The list of flair data.
	,	bool $showCategories = false	// <bool> TRUE to show categories.
	): void							// RETURNS <void> OUTPUTS the appropriate HTML.
	
	// AppFlair::drawList($flairList, [$showCategories]);
	{
		if(!$flairList) { return ''; }
		
		// Prepare Values
		$curCat = "";
		
		// Loop through each flair tag
		foreach($flairList as $flair)
		{
			// Display the Flair Category, if applicable
			if($showCategories and $flair['category'] != $curCat)
			{
				$curCat = $flair['category'];
				
				echo '
				<h3>' . $curCat . '</h3>';
			}
			
			$details = json_decode($flair['settings_json'], true);
			
			echo '
			<div style="margin-bottom:16px;">
				<div style="display:inline-block; font-size:0.9em; border-radius:6px; padding:4px; margin:3px; background-color:#' . $flair['color'] . ';"><a href="' . URL::karma_unifaction_com() . '/flair/' . $flair['title'] . '" style="color:#606060;"><span class="' . $flair['icon_class'] . '" style="font-size:1.2em; vertical-align:middle;"></span> ' . $flair['title'] . '</a></div>
				<div style="display:inline-block;"> - ' . $flair['description'] . '</div>';
			
			// Prepare mini-descriptions
			$miniDesc = $flair['reward_desc'];
			
			if(isset($details['free_auro_per_day']))
			{
				$miniDesc .= ' The user gains +' . $details['free_auro_per_day'] . ' auro per day' . (isset($details['limited']) ? ' while this flair is active.' : '.');
			}
			
			if(isset($details['assigned']))
			{
				$miniDesc .= ' This flair can only be assigned by the UniFaction staff.';
			}
			
			// Display the description
			if($miniDesc)
			{
				echo '
				<div style="font-size:0.8em;">' . $miniDesc . '</div>';
			}
			
			echo '
			</div>';
		}
	}
	
}