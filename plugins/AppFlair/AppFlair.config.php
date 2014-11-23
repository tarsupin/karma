<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppFlair_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppFlair";
	public $title = "Flair Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Provides methods to work with user flair.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `flair`
		(
			`id`					int(10)			unsigned	NOT NULL	AUTO_INCREMENT,
			
			`category`				varchar(22)					NOT NULL	DEFAULT '',
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '',
			`title`					varchar(22)					NOT NULL	DEFAULT '',
			
			`rank`					tinyint(2)		unsigned	NOT NULL	DEFAULT '0',
			`icon_class`			varchar(22)					NOT NULL	DEFAULT '',
			`color`					varchar(6)					NOT NULL	DEFAULT '',
			
			`description`			varchar(180)				NOT NULL	DEFAULT '',
			`reward_desc`			varchar(120)				NOT NULL	DEFAULT '',
			
			`settings_json`			varchar(200)				NOT NULL	DEFAULT '',
			
			PRIMARY KEY (`id`),
			UNIQUE (`site_handle`, `title`),
			UNIQUE (`category`, `rank`, `title`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `users_flair`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`flair_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`expires`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`total_duration`		int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			`hidden`				tinyint(1)		unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `flair_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 7;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("flair", array("id", "title"));
		$pass2 = DatabaseAdmin::columnsExist("users_flair", array("uni_id", "flair_id"));
		
		return ($pass1 and $pass2);
	}
	
}