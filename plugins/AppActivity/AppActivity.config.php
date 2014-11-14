<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppActivity_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppActivity";
	public $title = "Activity Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows the system to keep track of user activity across all of UniFaction.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `activity_current_log`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`action`				varchar(22)					NOT NULL	DEFAULT '0',
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '0',
			`date_action`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `date_action`, `action`, `site_handle`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 61;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `activity_perm_log`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`month_cycle`			smallint(5)		unsigned	NOT NULL	DEFAULT '0',
			`site_handle`			varchar(22)					NOT NULL	DEFAULT '0',
			`action`				varchar(22)					NOT NULL	DEFAULT '0',
			`count`					mediumint(6)	unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `month_cycle`, `site_handle`, `action`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 61;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("activity_current_log", array("uni_id", "action"));
		$pass2 = DatabaseAdmin::columnsExist("activity_perm_log", array("uni_id", "month_cycle"));
		
		return ($pass1 and $pass2);
	}
	
}