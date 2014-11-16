<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows external sites to assign bookmarks to users.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array(
		"uni_id"		=> $uniID
	,	"title"			=> $siteTitle
	,	"url"			=> $bookmarkURL
	);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "AddBookmarkAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------
	
	TRUE if the bookmark was assigned (or has already been assigned).
	FALSE if there was an error.
	
*/

class AddBookmarkAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): bool					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']) or !isset($this->data['title']) or !isset($this->data['url']))
		{
			return false;
		}
		
		// Prepare Values
		$uniID = (int) $this->data['uni_id'];
		$title = Sanitize::safeword($this->data['title']);
		$url = Sanitize::url($this->data['url']);
		
		// Make sure the bookmark exists
		if(!$bookmarkID = AppBookmarks::getIDByURL($url))
		{
			$group = "Sites";
			
			// Check if the site is a community
			if(strpos($title, "Community"))
			{
				$group = "Communities";
				$title = str_replace(" Community", "", $title);
			}
			
			// Check if the site is an article site
			else if(strpos($title, "Articles"))
			{
				$group = "Sites";
				$title = str_replace(" Articles", "", $title);
			}
			
			// Create the bookmark
			if(!$bookmarkID = AppBookmarks::create($group, $title, $url))
			{
				return false;
			}
		}
		
		// Assign the bookmark with the URL
		return AppBookmarks::assignByID($uniID, $bookmarkID);
	}
	
}