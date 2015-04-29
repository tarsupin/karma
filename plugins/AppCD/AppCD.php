<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

--------------------------------------
------ About the AppCD Plugin ------
--------------------------------------

This plugin provides methods to work with the Certificates of Deposit for Auro. CDs lock Auro away for a certain time and return them with interest. Each user is limited to 3 CDs at a time.


-------------------------------
------ Methods Available ------
-------------------------------

			AppCD::createAccount(Me::$id, 2, 10000);
$owned	 =	AppCD::ownedAccounts(Me::$id);
$list 	 = 	AppCD::getOwnedAccounts(Me::$id);
$data	 = 	AppCD::getAccount(Me::$id, 1);
$outcome = 	AppCD::getOutcome($data);
			AppCD::mature($uniID, $accountID);
			AppCD::checkMature();

*/

abstract class AppCD {
	
/****** Plugin Variables ******/
	public static $duration = array(1 => 10, 2 => 60, 3 => 120);	// <str:int>
	public static $interest = array(1 => 0.75, 2 => 5, 3 => 12);	// <str:float>
	
/****** Create a CD ******/
	public static function createAccount
	(
		$uniID		// <int> The ID of the user creating the CD.
	,	$plan		// <int> The plan to use: 1 for short, 2 for medium or 3 for long.
	,	$amount		// <int> The amount of Auro to deposit.
	)				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppCD::createAccount(Me::$id, 2, 10000);
	{
		$count = self::ownedAccounts($uniID);
		if($count > 2)
		{
			Alert::error("Too Many Accounts", "You already have the maximum number of CDs. Please wait until one of them matures.");
			return false;
		}
		
		$uniqueID = UniqueID::get("cd-accounts");
		if(!$uniqueID)
		{
			UniqueID::newCounter("cd-accounts");
			$uniqueID = UniqueID::get("cd-accounts");
		}
		
		Database::startTransaction();
		if($pass = AppAuro::spendAuro($uniID, $amount, false))
		{
			$expire = time() + self::$duration[$plan] * 24 * 3600;
			$pass = Database::query("INSERT INTO cd_accounts VALUES (?, ?, ?, ?, ?)", array($uniID, $uniqueID, $plan, $expire, $amount));
		}
		Database::endTransaction($pass);
		
		return $pass;
	}
	
/****** Get number of owned bank accounts *****/
	public static function ownedAccounts
	(
		$uniID		// <int> The UniID of the user we are checking for.
	)				// RETURNS <int> the number of owned CD accounts.
	
	// $owned = AppCD::ownedAccounts(Me::$id);
	{
		return (int) Database::selectValue("SELECT COUNT(account_id) FROM cd_accounts WHERE uni_id=? LIMIT 3", array($uniID));
	}
	
/****** Get list of owned CDs ******/
	public static function getOwnedAccounts
	(
		$uniID		// <int> The UniID of the user we are checking for.
	)				// RETURNS <str:str> the list of owned CDs.
	
	// $list = AppCD::getOwnedAccounts(Me::$id);
	{
		return Database::selectMultiple("SELECT account_id, plan, date_expire, deposit FROM cd_accounts WHERE uni_id=? LIMIT 3", array($uniID));
	}
	
/****** Get CD data ******/
	public static function getAccount
	(
		$uniID		// <int> The UniID of the user we are checking for.
	,	$accountID	// <int> The ID of the CD to get data for.
	)				// RETURNS <str:str> the CD's data.
	
	// $data = AppCD::getAccount(Me::$id, 1);
	{
		return Database::selectOne("SELECT account_id, plan, date_expire, deposit FROM cd_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID));
	}
	
/****** Get Outcome ******/
	public static function getOutcome
	(
		$data				// <str:str> The CD's data.
	,	$expired = false	// <bool> Returns 0 if set to TRUE and the CD hasn't matured yet.
	)				// RETURNS <int> the outcome with interest.
	
	// $outcome = AppCD::getOutcome($data);
	{
		if($expired && $data['date_expire'] >= time())
		{
			return 0;
		}
		
		return (int) floor($data['deposit'] * (1 + self::$interest[$data['plan']] / 100));
	}
	
/****** Mature CD ******/
	public static function mature
	(
		$uniID		// <int> The UniID of the user.
	,	$accountID	// <int> The ID of the CD account.
	)				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppCD::mature($uniID, $accountID);
	{
		if(!$data = self::getAccount($uniID, $accountID))
		{
			return false;
		}

		if(!$outcome = self::getOutcome($data, true))
		{
			Alert::error("Not Mature", "This CD has not matured yet.");
			return false;
		}
		
		Database::startTransaction();
		if($pass = AppAuro::grantAuro($uniID, $outcome, false))
		{
			if($pass = Database::query("DELETE FROM cd_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)))
			{
				$pass = AppAuro::record($uniID, 0, ($outcome - $data['deposit']), 'CD Interest', "Karma System");
			}
		}
		Database::endTransaction($pass);
		
		if($pass && $uniID == Me::$id)
		{
			Alert::success("Mature", "You have received " . $outcome . " Auro from your CD!");
		}
		
		return $pass;
	}
	
/****** Check for ready-to-matured CDs ******/
	public static function checkMature
	(
	)			// RETURNS <void>
	
	// AppCD::checkMature();
	{
		$ready = Database::selectMultiple("SELECT * FROM cd_accounts WHERE date_expire<=?", array(time()));
		//Database::startTransaction();
		foreach($ready as $r)
		{
			Notifications::create((int) $r['uni_id'], URL::karma_unifaction_com() . "/certificate-of-deposit", "Your CD has matured, giving you " . self::getOutcome($r) . " Auro!");
			self::mature((int) $r['uni_id'], (int) $r['account_id']);
		}
		Database::endTransaction(true);
	}
}