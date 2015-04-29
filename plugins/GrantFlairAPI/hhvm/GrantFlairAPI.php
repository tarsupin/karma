<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to grant flair to a user.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array(
		"uni_id"		=> $uniID
	,	"site_handle"	=> $siteHandle
	,	"title"			=> $title
	,	"add_time"		=> int $timeToAdd		// <int> The number of seconds to grant this flair for (0 for infinite)
	);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "GrantFlairAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

	TRUE if the flair was added properly.
	FALSE if there was an error.

*/

class GrantFlairAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): bool					// RETURNS <bool> TRUE if the flair is added successfully, FALSE if not.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']) or !isset($this->data['site_handle']) or !isset($this->data['title']) or !isset($this->data['add_time']))
		{
			return false;
		}
		
		// Get the appropriate flair ID
		if(!$flairID = AppFlair::getIDByType(Sanitize::variable($this->data['site_handle']), Sanitize::variable($this->data['title'], " ")))
		{
			return false;
		}
		
		// Assign the flair
		return AppFlair::assignByID((int) $this->data['uni_id'], $flairID, (int) $this->data['add_time']);
	}
	
}