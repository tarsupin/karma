<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppCD_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppCD";
	public $title = "Certificate of Deposit Plugin";
	public $version = 1.0;
	public $author = "Brint Paris & Pegasus";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides tools for managing Auro in CDs.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `cd_accounts`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`account_id`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`plan`					tinyint(1)		unsigned	NOT NULL	DEFAULT '0',
			`date_expire`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`deposit`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE(`uni_id`, `account_id`),
			INDEX(`date_expire`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 31;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass = DatabaseAdmin::columnsExist("cd_accounts", array("uni_id", "account_id"));
		
		return $pass;
	}
	
}