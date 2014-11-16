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
	,	"duration"		=> int $duration		// <int> The number of seconds to grant this flair for (0 for infinite)
	);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "GrantFlairAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

	TRUE if the auro was sent properly.
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
	): array <int, array<str, mixed>>					// RETURNS <int:[str:mixed]> the response depends on the type of command being requested.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']) or !isset($this->data['site_handle']) or !isset($this->data['title']) or !isset($this->data['duration']))
		{
			return false;
		}
		
		// Prepare Values
		$desc = isset($this->data['desc']) ? Sanitize::safeword($this->data['desc']) : "";
		$record = $desc ? true : false;
		$siteName = isset($this->data['site_name']) ? $this->data['site_name'] : '';
		
		return AppAuro::grantAuro((int) $this->data['uni_id'], (int) $this->data['auro'], $record, $desc, $siteName);
	}
	
}