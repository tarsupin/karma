<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

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
	public static $maxBookmarks = 25;		// <int>
	
	
/****** Get a list of the user's bookmarks ******/
	public static function getUserList
	(
		$uniID				// <int> The UniID to retrieve bookmarks for.
	)						// RETURNS <int> The ID of the bookmark, or 0 on failure.
	
	// $bookmarkList = AppBookmarks::getUserList($uniID);
	{
		$bmList = array();
		
		$results = Database::selectMultiple("SELECT b.* FROM users_bookmarks ub INNER JOIN bookmarks b ON ub.bookmark_id=b.id WHERE ub.uni_id=? ORDER BY title ASC", array($uniID));
		
		foreach($results as $res)
		{
			$bmList[$res['book_group']][$res['title']] = $res['url'];
		}
		
		return $bmList;
	}
	
	
/****** Get a list of default bookmarks ******/
	public static function fetchDefaultBookmarks (
	)				// RETURNS <str:[str:str]> The list of bookmarks.
	
	// $bookmarkList = AppBookmarks::fetchDefaultBookmarks();
	{
		return array(
			"Communities" => array(
					"Avatar"	=> "http://avatar.unifaction.community"
				,	"Books"		=> "http://books.unifaction.community"
				,	"Gaming"	=> "http://gaming.unifaction.community"
				,	"Humor"		=> "http://humor.unifaction.community"
				,	"Movies"	=> "http://movies.unifaction.community"
				,	"Music"		=> "http://music.unifaction.community"
				,	"Pets"		=> "http://pets.unifaction.community"
				,	"Politics"	=> "http://politics.unifaction.community"
				,	"Shows"		=> "http://shows.unifaction.community"
				,	"Tech"		=> "http://tech.unifaction.community"
				)
		,	"Sites" => array(
					"Avatar"			=> "http://avatar.unifaction.com"
				,	"Entertainment"		=> "http://entertainment.unifaction.com"
				,	"Food"				=> "http://food.unifaction.com"
				,	"Gaming"			=> "http://gaming.unifaction.com"
				,	"News"				=> "http://news.unifaction.com"
				,	"Sports"			=> "http://sports.unifaction.com"
				,	"Tech"				=> "http://tech.unifaction.com"
				)
		);
	}
	
	
/****** Get a list of the user's bookmarks ******/
	public static function assignDefaultBookmarks
	(
		$uniID		// <int> The UniID to assign the default bookmarks to.
	)				// RETURNS <str:[str:str]> The list of bookmarks.
	
	// AppBookmarks::assignDefaultBookmarks($uniID);
	{
		$bookmarkList = AppBookmarks::fetchDefaultBookmarks();
		
		foreach($bookmarkList as $groupName => $groupList)
		{
			foreach($groupList as $title => $url)
			{
				AppBookmarks::assignByType($uniID, "Communities", "Avatar");
			}
		}
		
		return AppBookmarks::getUserList($uniID);
	}
	
	
/****** Create a new bookmark ******/
	public static function create
	(
		$group				// <str> The bookmark group to put this bookmark in.
	,	$title				// <str> The title of the bookmark to create.
	,	$url				// <str> The URL that the bookmark takes you to.
	)						// RETURNS <int> The ID of the bookmark, or 0 on failure.
	
	// $bookmarkID = AppBookmarks::create($group $title, $url);
	{
		Database::query("INSERT IGNORE INTO `bookmarks` (book_group, title, url) VALUES (?, ?, ?)", array($group, $title, $url));
		
		return Database::$lastID;
	}
	
	
/****** Get the bookmark ID by the type ******/
	public static function getIDByType
	(
		$group		// <str> The bookmark group that the bookmark belongs to.
	,	$title		// <str> The title of the bookmark to assign.
	)				// RETURNS <int> the ID of the bookmark, or 0 on failure.
	
	// $bookmarkID = AppBookmarks::getIDByType($group, $title);
	{
		return (int) Database::selectValue("SELECT id FROM bookmarks WHERE book_group=? AND title=? LIMIT 1", array($group, $title));
	}
	
	
/****** Assign bookmark to a user ******/
	public static function assignByID
	(
		$uniID				// <int> The UniID of the user to assign a bookmark to.
	,	$bookmarkID			// <int> The bookmark ID to assign to the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBookmarks::assignByID($uniID, $bookmarkID);
	{
		// Check if the user has more than the maximum allowed number of bookmarks
		$countMarks = (int) Database::selectValue("SELECT COUNT(*) as totalNum FROM users_bookmarks WHERE uni_id=? LIMIT 1", array($uniID));
		
		if($countMarks > self::$maxBookmarks)
		{
			return false;
		}
		
		return Database::query("REPLACE INTO users_bookmarks (uni_id, bookmark_id) VALUES (?, ?)", array($uniID, $bookmarkID));
	}
	
	
/****** Assign bookmark to a user based on the bookmark type (instead of ID) ******/
	public static function assignByType
	(
		$uniID		// <int> The UniID of the user to assign bookmark to.
	,	$group		// <str> The bookmark group that the bookmark belongs to.
	,	$title		// <str> The title of the bookmark to assign.
	)				// RETURNS <bool> TRUE on success, FALSE on failure.
	
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
		$uniID				// <int> The UniID of the user to un-assign bookmark from.
	,	$bookmarkID			// <int> The bookmark ID to remove from the user.
	)						// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBookmarks::unassignByID($uniID, $bookmarkID);
	{
		return Database::query("DELETE FROM users_bookmarks WHERE uni_id=? AND bookmark_id=? LIMIT 1", array($uniID, $bookmarkID));
	}
	
	
/****** Un-assign bookmark from a user based on the bookmark type (instead of ID) ******/
	public static function unassignByType
	(
		$uniID		// <int> The UniID of the user to assign bookmark to.
	,	$group		// <str> The bookmark group that the bookmark belongs to.
	,	$title		// <str> The title of the bookmark to assign.
	)				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBookmarks::unassignByType($uniID, $group, $title);
	{
		if($bookmarkID = self::getIDByType($group, $title))
		{
			return AppBookmarks::unassignByID($uniID, $bookmarkID);
		}
		
		return false;
	}
}
