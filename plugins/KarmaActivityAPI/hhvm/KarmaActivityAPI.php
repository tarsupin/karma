<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows the system to track the user's activity and functionality.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array("uni_id" => $uniID, "site_handle" => $siteHandle, "action" => $action);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "KarmaActivityAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

	TRUE if the auro was sent properly.
	FALSE if there was an error.

*/

class KarmaActivityAPI extends API {
	
	
/****** API Variables ******/
	public bool $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public string $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public array <int, str> $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public int $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public int $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	): bool					// RETURNS <bool> TRUE on a successful activity pass.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']) or !isset($this->data['site_handle']) or !isset($this->data['action']))
		{
			return false;
		}
		
		// Prepare Values
		$uniID = (int) $this->data['uni_id'];
		
		// Allot new Auro
		AppAuro::allotAuro($uniID);
		
		// Log the user's activity
		AppActivity::logAction($uniID, Sanitize::variable($this->data['site_handle']), Sanitize::variable($this->data['action']));
		
		return true;
	}
	
}