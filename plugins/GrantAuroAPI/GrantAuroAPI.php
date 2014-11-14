<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to grant auro to a user.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array("uni_id" => $uniID, "auro" => $auroAmount);
	
	// Connect to this API from UniFaction
	$success = Connect::to("karma", "GrantAuroAPI", $packet);
	
	
-----------------------------
------ Response Packet ------
-----------------------------

	TRUE if the auro was sent properly.
	FALSE if there was an error.

*/

class GrantAuroAPI extends API {
	
	
/****** API Variables ******/
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <int:[str:mixed]> the response depends on the type of command being requested.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id']) or !isset($this->data['auro']))
		{
			return false;
		}
		
		return AppAuro::grantAuro((int) $this->data['uni_id'], (int) $this->data['auro']);
	}
	
}
