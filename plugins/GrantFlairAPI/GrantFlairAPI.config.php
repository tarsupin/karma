<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

class GrantFlairAPI_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "api";
	public $pluginName = "GrantFlairAPI";
	public $title = "Grant Flair API";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows external sites on UniFaction to grant flair to a user.";
	
	public $data = array();
	
}