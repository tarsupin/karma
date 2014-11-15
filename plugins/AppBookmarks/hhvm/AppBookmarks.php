<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

-------------------------------------------
------ About the AppBookmarks Plugin ------
-------------------------------------------

This plugin provides methods to interact with user-selected bookmarks.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppBookmarks {
	
	
/****** Plugin Variables ******/
	public static int $maxBookmarks = 20;		// <int>
	
	
/****** Create a new bookmark ******/
	public static function create
	(
		string $group				// <str> The bookmark group to put this bookmark in.
	,	string $title				// <str> The title of the bookmark to create.
	,	string $url				// <str> The URL that the bookmark takes you to.
	): int						// RETURNS <int> The ID of the bookmark, or 0 on failure.
	
	// $bookmarkID = AppBookmarks::create($group $title, $url);
	{
		Database::query("REPLACE INTO `bookmarks` (book_group, title, url) VALUES (?, ?, ?)", array($group, $title, $url)))
		
		return Database::$lastID;
	}
	
	
/****** Get the bookmark ID by the type ******/
	public static function getIDByType
	(
		string $group		// <str> The bookmark group that the bookmark belongs to.
	,	string $title		// <str> The title of the bookmark to assign.
	): int				// RETURNS <int> the ID of the bookmark, or 0 on failure.
	
	// $bookmarkID = AppBookmarks::getIDByType($group, $title);
	{
		return (int) Database::selectValue("SELECT id FROM bookmarks WHERE book_group=? AND title=? LIMIT 1", array($group, $title));
	}
	
	
/****** Assign bookmark to a user ******/
	public static function assignByID
	(
		int $uniID				// <int> The UniID of the user to assign a bookmark to.
	,	int $bookmarkID			// <int> The bookmark ID to assign to the user.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBookmarks::assignByID($uniID, $bookmarkID);
	{
		// Check if the user has more than the maximum allowed number of bookmarks
		$countMarks = (int) Database::selectValue("SELECT COUNT(*) as totalNum FROM users_bookmarks WHERE uni_id=? LIMIT 1", array($uniID));
		
		if(self::$maxBookmarks > $countMarks);
		{
			return false;
		}
		
		return Database::query("REPLACE INTO users_bookmarks (uni_id, bookmark_id) VALUES (?, ?)", array($uniID, $bookmarkID));
	}
	
	
/****** Assign bookmark to a user based on the bookmark type (instead of ID) ******/
	public static function assignByType
	(
		int $uniID		// <int> The UniID of the user to assign bookmark to.
	,	string $group		// <str> The bookmark group that the bookmark belongs to.
	,	string $title		// <str> The title of the bookmark to assign.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBookmarks::assignByType($uniID, $group, $title);
	{
		if($bookmarkID = self::getIDByType($group, $title))
		{
			return AppBookmarks::assignByID($uniID, $bookmarkID);
		}
		
		return false;
	}
	
	
/****** Un-assign bookmark from a user ******/
	public static function unassignByID
	(
		int $uniID				// <int> The UniID of the user to un-assign bookmark from.
	,	int $bookmarkID			// <int> The bookmark ID to remove from the user.
	): bool						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBookmarks::unassignByID($uniID, $bookmarkID);
	{
		return Database::query("DELETE FROM users_bookmarks WHERE uni_id=? AND bookmark_id=? LIMIT 1", array($uniID, $bookmarkID));
	}
}