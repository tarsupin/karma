<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

--------------------------------------
------ About the AppAuro Plugin ------
--------------------------------------

This plugin provides methods to work with Auro.


-------------------------------
------ Methods Available ------
-------------------------------


*/

abstract class AppAuro {
	
	
/****** Plugin Variables ******/
	public static $auroPerMinute = 5;		// <int> The amount of auro to allot per minute.
	public static $auroCapOnAllot = 150;	// <int> The maximum amount of auro to generate during allotment.
	
	
/****** Retrieve the auro data for a user ******/
	public static function getData
	(
		$uniID		// <int> The UniID to retrieve the auro data for.
	)				// RETURNS <bool> TRUE on successfully added, FALSE on failure.
	
	// $auroData = AppAuro::getData($uniID);
	{
		return Database::selectOne("SELECT * FROM users_auro WHERE uni_id=? LIMIT 1", array($uniID));
	}
	
	
/****** Allot Auro to the user while they're browsing the system, based on caps ******/
	public static function allotAuro
	(
		$uniID		// <int> The UniID to give auro to.
	)				// RETURNS <bool> TRUE on successfully allotted auro, FALSE on no change.
	
	// AppAuro::allotAuro($uniID);
	{
		$checkLastAllot = Database::selectValue("SELECT date_last_allotted FROM users_auro WHERE uni_id=? LIMIT 1", array($uniID));
		
		// If the user's table entry isn't created, create it now
		if($checkLastAllot === false)
		{
			if(!AppAuro::createUserEntry($uniID, 0))
			{
				return false;
			}
		}
		
		// Determine the number of minutes since the last date
		$timeSince = (time() - (int) $checkLastAllot) / 60;
		
		// If it's been more than five minutes since your last allotment, gain auro
		if($timeSince >= 5)
		{
			// Determine how much auro to give
			$amountToGive = min(self::$auroCapOnAllot, round($timeSince * self::$auroPerMinute));
			
			// Update the user's auro and time allotted
			return Database::query("UPDATE users_auro SET auro=auro+?, date_last_allotted=? WHERE uni_id=? LIMIT 1", array($amountToGive, time(), $uniID));
		}
		
		return false;
	}
	
	
/****** Update a user's activity to the current time ******/
	public static function grantAuro
	(
		$uniID		// <int> The UniID to give auro to.
	,	$auro		// <int> The amount of auro to give the user.
	)				// RETURNS <bool> TRUE on successfully added, FALSE on failure.
	
	// $success = AppAuro::grantAuro($uniID, $auro);
	{
		if(!Database::query("UPDATE users_auro SET auro=auro+? WHERE uni_id=? LIMIT 1", array($auro, $uniID)))
		{
			return AppAuro::createUserEntry($uniID, $auro);
		}
		
		return true;
	}
	
	
/****** Spend Auro ******/
	public static function spendAuro
	(
		$uniID		// <int> The UniID to give auro to.
	,	$auro		// <int> The amount of auro to give the user.
	)				// RETURNS <bool> TRUE on successful spend (had sufficient auro), FALSE on failure.
	
	// $success = AppAuro::spendAuro($uniID, $auro);
	{
		if($checkAuro = (int) Database::selectValue("SELECT auro FROM users_auro WHERE uni_id=? LIMIT 1", array($uniID)))
		{
			// If you have enough auro
			if($checkAuro >= $auro)
			{
				return Database::query("UPDATE users_auro SET auro=auro-? WHERE uni_id=? LIMIT 1", array($auro, $uniID));
			}
		}
		
		return false;
	}
	
	
/****** Exchange Auro between two users ******/
	public static function exchangeAuro
	(
		$uniIDFrom		// <int> The UniID to spend auro from.
	,	$uniIDTo		// <int> The UniID to grant auro to.
	,	$auro			// <int> The amount of auro to exchange.
	)					// RETURNS <bool> TRUE on successful exchange (had sufficient auro), FALSE on failure.
	
	// $success = AppAuro::exchangeAuro($uniIDFrom, $uniIDTo, $auro);
	{
		// Cannot exchange to yourself
		if($uniIDFrom == $uniIDTo) { return false; }
		
		// Make sure the user has enough auro
		if($checkAuro = (int) Database::selectValue("SELECT auro FROM users_auro WHERE uni_id=? LIMIT 1", array($uniIDFrom)))
		{
			// If you have enough auro
			if($checkAuro >= $auro)
			{
				Database::startTransaction();
				
				if($pass = Database::query("UPDATE users_auro SET auro=auro-? WHERE uni_id=? LIMIT 1", array($auro, $uniIDFrom)))
				{
					if(!$checkExist = Database::selectValue("SELECT uni_id FROM users_auro WHERE uni_id=? LIMIT 1", array($uniIDTo)))
					{
						AppAuro::createUserEntry($uniIDTo, $auro);
						
						if(!$checkExist = Database::selectValue("SELECT uni_id FROM users_auro WHERE uni_id=? LIMIT 1", array($uniIDTo)))
						{
							return false;
						}
					}
					
					$pass = Database::query("UPDATE users_auro SET auro=auro+? WHERE uni_id=? LIMIT 1", array($auro, $uniIDTo));
				}
				
				return Database::endTransaction($pass);
			}
		}
		
		return false;
	}
	
	
/****** Create the user's auro table entry ******/
	public static function createUserEntry
	(
		$uniID		// <int> The UniID to setup the table for.
	,	$auro		// <int> The amount of auro to start the user with.
	)				// RETURNS <bool> TRUE on successfully created, FALSE on failure.
	
	// AppAuro::createUserEntry($uniID, $auro);
	{
		$auroDay = (int) (date("y") . sprintf('%03d', (int) date('z')));
		
		return Database::query("INSERT INTO users_auro (uni_id, auro, date_last_allotted, auro_day) VALUES (?, ?, ?, ?)", array($uniID, $auro, time(), $auroDay));
	}
	
}
