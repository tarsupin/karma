<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppAuro_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppAuro";
	public $title = "Auro Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides tools for working with auro throughout UniFaction.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `users_auro`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`auro`					int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`date_last_allotted`	int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`free_auro_per_day`		smallint(5)		unsigned	NOT NULL	DEFAULT '0',
			`auro_day`				smallint(5)		unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`)
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
		return DatabaseAdmin::columnsExist("users_auro", array("uni_id", "auro"));
	}
	
}