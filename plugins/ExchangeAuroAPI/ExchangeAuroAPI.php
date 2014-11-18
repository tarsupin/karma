<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

----------------------------
------ About this API ------
----------------------------

This API allows another site to exchange auro between two users.


------------------------------
------ Calling this API ------
------------------------------
	
	// Prepare the Packet
	$packet = array(
		"uni_id_from"	=> $uniIDFrom
	,	"uni_id_to"		=> $uniIDTo
	,	"auro"			=> $auroAmount
	,	"desc"			=> $desc			// Optional: add a description to record the transaction.
	,	"site_name"		=> $siteName		// Optional: If recording the transaction, set the site name.
	);
	
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
	public $isPrivate = true;			// <bool> TRUE if this API is private (requires an API Key), FALSE if not.
	public $encryptType = "";			// <str> The encryption algorithm to use for response, or "" for no encryption.
	public $allowedSites = array();		// <int:str> the sites to allow the API to connect with. Default is all sites.
	public $microCredits = 50;			// <int> The cost in microcredits (1/10000 of a credit) to access this API.
	public $minClearance = 6;			// <int> The clearance level required to use this API.
	
	
/****** Run the API ******/
	public function runAPI (
	)					// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// $this->runAPI()
	{
		// Make sure the last ID was sent
		if(!isset($this->data['uni_id_from']) or !isset($this->data['uni_id_to']) or !isset($this->data['auro']))
		{
			return false;
		}
		
		// Prepare Values
		$desc = isset($this->data['desc']) ? Sanitize::safeword($this->data['desc']) : "";
		$record = $desc ? true : false;
		$siteName = isset($this->data['site_name']) ? $this->data['site_name'] : '';
		
		return AppAuro::exchangeAuro((int) $this->data['uni_id_from'], (int) $this->data['uni_id_to'], (int) $this->data['auro'], $record, $desc, $siteName);
	}
	
}
