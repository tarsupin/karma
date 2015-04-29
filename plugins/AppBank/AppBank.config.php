<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppBank_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppBank";
	public $title = "Bank Plugin";
	public $version = 1.0;
	public $author = "Brint Paris & Pegasus";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides tools for managing Auro in the bank.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `bank_accounts`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`account_id`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`balance`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`name`					varchar(30)					NOT NULL	DEFAULT '',
			`shared_with`			varchar(255)				NOT NULL	DEFAULT '[]',
			
			UNIQUE(`uni_id`, `account_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 41;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `bank_shared`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`account_id`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`access`				tinyint(1)		unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE(`uni_id`, `account_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 41;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `bank_records`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`account_id`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`other_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`date_exchange`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			`amount`				int(8)						NOT NULL	DEFAULT '0',
			
			INDEX (`uni_id`, `account_id`),
			INDEX (`date_exchange`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 63;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("bank_accounts", array("uni_id", "account_id"));
		$pass2 = DatabaseAdmin::columnsExist("bank_shared", array("uni_id", "account_id"));
		$pass3 = DatabaseAdmin::columnsExist("bank_records", array("uni_id", "account_id"));
		
		return ($pass1 and $pass2 and $pass3);
	}
	
}