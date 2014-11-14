<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppAward_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppAward";
	public $title = "Award Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides methods to assign, remove, and work with user awards.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `awards`
		(
			`id`					int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '',
			`title`					varchar(22)					NOT NULL	DEFAULT '',
			
			`description`			varchar(180)				NOT NULL	DEFAULT '',
			`reward_desc`			varchar(120)				NOT NULL	DEFAULT '',
			
			`settings_json`			varchar(200)				NOT NULL	DEFAULT '',
			
			PRIMARY KEY (`id`),
			UNIQUE (`site_handle`, `title`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `users_awards`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`award_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`count`					tinyint(3)		unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `award_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 5;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("awards", array("id", "title"));
		$pass2 = DatabaseAdmin::columnsExist("users_awards", array("uni_id", "award_id"));
		
		return ($pass1 and $pass2);
	}
	
}