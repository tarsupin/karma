<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

class AppBookmarks_config {
	
	
/****** Plugin Variables ******/
	public $pluginType = "standard";
	public $pluginName = "AppBookmarks";
	public $title = "Bookmark Plugin";
	public $version = 1.0;
	public $author = "Brint Paris";
	public $license = "UniFaction License";
	public $website = "http://unifaction.com";
	public $description = "Allows users to generate custom bookmarks throughout UniFaction.";
	
	public $data = array();
	
	
/****** Install this plugin ******/
	public function install (
	)			// <bool> RETURNS TRUE on success, FALSE on failure.
	
	// $plugin->install();
	{
		Database::exec("
		CREATE TABLE IF NOT EXISTS `bookmarks`
		(
			`id`					int(10)			unsigned	NOT NULL	AUTO_INCREMENT,
			
			`book_group`			varchar(22)					NOT NULL	DEFAULT '',
			`title`					varchar(32)					NOT NULL	DEFAULT '',
			`url`					varchar(100)				NOT NULL	DEFAULT '',
			
			PRIMARY KEY (`id`),
			UNIQUE (`book_group`, `title`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		Database::exec("
		CREATE TABLE IF NOT EXISTS `users_bookmarks`
		(
			`uni_id`				int(10)			unsigned	NOT NULL	DEFAULT '0',
			`bookmark_id`			int(10)			unsigned	NOT NULL	DEFAULT '0',
			
			UNIQUE (`uni_id`, `bookmark_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 PARTITION BY KEY(uni_id) PARTITIONS 17;
		");
		
		return $this->isInstalled();
	}
	
	
/****** Check if the plugin was successfully installed ******/
	public static function isInstalled (
	)			// <bool> TRUE if successfully installed, FALSE if not.
	
	// $plugin->isInstalled();
	{
		// Make sure the newly installed tables exist
		$pass1 = DatabaseAdmin::columnsExist("bookmarks", array("id", "title"));
		$pass2 = DatabaseAdmin::columnsExist("user_bookmarks", array("uni_id", "bookmark_id"));
		
		return ($pass1 and $pass2);
	}
	
}