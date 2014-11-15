<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows users on external sites to spend auro.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array(
		"uni_id"	=> $uniID
	,	"auro"		=> $auroAmount
	,	"desc"		=> $desc			// Optional: add a description to record the transaction.
	,	"site_name"	=> $siteName		// Optional: If recording the transaction, set the site name.
	);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "SpendAuroAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

	TRUE if the auro was spent properly (had enough auro, etc).
	FALSE if there was an error or not enough auro available.

*/

class SpendAuroAPI extends API {
	
	
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
		if(!isset($this->data['uni_id']) or !isset($this->data['auro']))
		{
			return false;
		}
		
		// Prepare Values
		$desc = isset($this->data['desc']) ? Sanitize::safeword($this->data['desc']) : "";
		$record = $desc ? true : false;
		$siteName = isset($this->data['site_name']) ? $this->data['site_name'] : '';
		
		return AppAuro::spendAuro((int) $this->data['uni_id'], (int) $this->data['auro'], $record, $desc, $siteName);
	}
	
}