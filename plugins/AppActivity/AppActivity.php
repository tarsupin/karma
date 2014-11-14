<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

------------------------------------------
------ About the AppActivity Plugin ------
------------------------------------------

This plugin provides methods to track a user's activity on the system.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppActivity {
	
	
/****** Log a user's action ******/
	public static function logAction
	(
		$uniID				// <int> The UniID to log an action for.
	,	$siteHandle			// <str> The site handle that the action is coming from.
	,	$action				// <str> The type of action being logged.
	)						// RETURNS <void>
	
	// AppActivity::logAction($uniID, $siteHandle, $action);
	{
		// Prepare Values
		$timestamp = time();
		$monthCycle = (int) date("ym");
		
		// Add the current log entry
		Database::query("INSERT INTO activity_current_log (uni_id, action, site_handle, date_action) VALUES (?, ?, ?, ?)", array($uniID, $action, $siteHandle, $timestamp));
		
		// Check if the permanent log has been updated with this type of activity
		if($check = Database::selectValue("SELECT uni_id FROM activity_perm_log WHERE uni_id=? AND month_cycle=? AND site_handle=? AND action=? LIMIT 1", array($uniID, $monthCycle, $siteHandle, $action)))
		{
			Database::query("UPDATE activity_perm_log SET count=count+1 WHERE uni_id=? AND month_cycle=? AND site_handle=? AND action=? LIMIT 1", array($uniID, $monthCycle, $siteHandle, $action));
		}
		else
		{
			Database::query("REPLACE INTO activity_perm_log (uni_id, month_cycle, site_handle, action, count) VALUES (?, ?, ?, ?, ?)", array($uniID, $monthCycle, $siteHandle, $action, 1));
		}
		
		// Prune the current log entry once in a while
		if(mt_rand(0, 100) == 22)
		{
			self::pruneCurrentLogs($uniID);
		}
	}
	
	
/****** Prune a user's current logs ******/
	public static function pruneCurrentLogs
	(
		$uniID				// <int> The UniID to prune the logs of.
	)						// RETURNS <void>
	
	// AppActivity::pruneCurrentLogs($uniID);
	{
		if($checkDate = Database::selectValue("SELECT date_action FROM activity_current_log WHERE uni_id=? ORDER BY date_action DESC LIMIT 150, 1", array($uniID)))
		{
			Database::query("DELETE FROM activity_current_log WHERE uni_id=? AND date_action < ?", array($checkDate));
		}
	}
}
