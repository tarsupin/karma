<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to exchange auro between two users.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array("uni_id_from" => $uniIDFrom, "uni_id_to" => $uniIDTo, "auro" => $auroAmount);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "ExchangeAuroAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

	TRUE if the auro was exchanged between the two users properly.
	FALSE if there was an error, or the necessary amount could not be exchanged.

*/

class ExchangeAuroAPI extends API {
	
	
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
		if(!isset($this->data['uni_id_from']) or !isset($this->data['uni_id_to']) or !isset($this->data['auro']))
		{
			return false;
		}
		
		return AppAuro::exchangeAuro((int) $this->data['uni_id_from'], (int) $this->data['uni_id_to'], (int) $this->data['auro']);
	}
	
}