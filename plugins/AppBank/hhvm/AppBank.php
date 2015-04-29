<?hh if(!defined("CONF_PATH")) { die("No direct script access allowed."); } /*

--------------------------------------
------ About the AppBank Plugin ------
--------------------------------------

This plugin provides methods to work with the bank for Auro. Each user is limited to 10 bank accounts.


-------------------------------
------ Methods Available ------
-------------------------------

			AppBank::createAccount(Me::$id, "My Savings");
			AppBank::renameAccount(Me::$id, 1, "Personal Savings");
$owned	 =	AppBank::ownedAccounts(Me::$id);
$list	 =	AppBank::getOwnedAccounts(Me::$id);
$list 	 =	AppBank::getSharedAccounts(Me::$id);
$balance =	AppBank::getBalance(Me::$id, 1);
$name	 =	AppBank::getName(Me::$id, 1);
$others  =	AppBank::getOthers(Me::$id, 1);
$balance =	AppBank::changeBalance(Me::$id, 1, -1000);
$balance =	AppBank::changeBalance(5, 1, -1000, Me::$id);
			AppBank::addShare(Me::$id, 1, 5, 0);
			AppBank::editShare(Me::$id, 1, 5);
			AppBank::removeShare(Me::$id, 1, 5);
*/

abstract class AppBank {
	
/****** Create a bank account ******/
	public static function createAccount
	(
		int $uniID		// <int> The UniID of the user creating the bank account.
	,	string $name		// <str> The name of the bank account.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBank::createAccount(Me::$id, "My Savings");
	{		
		$count = self::ownedAccounts($uniID);
		if($count > 9)
		{
			return false;
		}
		FormValidate::text("Name", $name, 1, 30, "~");
		if(Alert::hasErrors())
		{
			return false;
		}
		$uniqueID = UniqueID::get("bank-accounts");
		if(!$uniqueID)
		{
			UniqueID::newCounter("bank-accounts");
			$uniqueID = UniqueID::get("bank-accounts");
		}
		return Database::query("INSERT INTO bank_accounts (uni_id, account_id, name) VALUES (?, ?, ?)", array($uniID, $uniqueID, $name));
	}
	
/****** Rename a bank account ******/
	public static function renameAccount
	(
		int $uniID		// <int> The UniID of the user creating the bank account.
	,	int $accountID	// <int> The ID of the bank account.
	,	string $name		// <str> The name of the bank account.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBank::renameAccount(Me::$id, 1, "Personal Savings");
	{
		if(!Database::selectOne("SELECT uni_id FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)))
		{
			return false;
		}
		
		FormValidate::text("Name", $name, 1, 30, "~");
		if(Alert::hasErrors())
		{
			return false;
		}		
		return Database::query("UPDATE bank_accounts SET name=? WHERE uni_id=? AND account_id=? LIMIT 1", array($name, $uniID, $accountID));
	}
	
/****** Get number of owned bank accounts *****/
	public static function ownedAccounts
	(
		int $uniID		// <int> The UniID of the user we are checking for.
	): int				// RETURNS <int> the number of owned accounts.
	
	// $owned = AppBank::ownedAccounts(Me::$id);
	{
		return (int) Database::selectValue("SELECT COUNT(account_id) FROM bank_accounts WHERE uni_id=? LIMIT 10", array($uniID));
	}
	
/***** Get list of owned bank accounts ******/
	public static function getOwnedAccounts
	(
		int $uniID		// <int> The UniID of the user we are checking for.
	): array <str, str>				// RETURNS <str:str> the list of owned bank accounts.
	
	// $list = AppBank::getOwnedAccounts(Me::$id);
	{
		return Database::selectMultiple("SELECT account_id, balance, name, shared_with FROM bank_accounts WHERE uni_id=? LIMIT 10", array($uniID));
	}
	
/***** Get list of shared bank accounts ******/
	public static function getSharedAccounts
	(
		int $uniID		// <int> The UniID of the user we are checking for.
	): array <str, str>				// RETURNS <str:str> the list of shared bank accounts.
	
	// $list = AppBank::getSharedAccounts(Me::$id);
	{
		return Database::selectMultiple("SELECT bs.account_id, bs.access, ba.uni_id, ba.balance, ba.name FROM bank_shared bs INNER JOIN bank_accounts ba ON bs.account_id=ba.account_id WHERE bs.uni_id=?", array($uniID));
	}
	
/****** Get Balance ******/
	public static function getBalance
	(
		int $uniID		// <int> The UniID of the user.
	,	int $accountID	// <int> The ID of the bank account.
	): int				// RETURNS <int> the balance.
	
	// $balance = AppBank::getBalance(Me::$id, 1);
	{
		return (int) Database::selectValue("SELECT balance FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID));
	}
	
/****** Get Name ******/
	public static function getName
	(
		int $uniID		// <int> The UniID of the user.
	,	int $accountID	// <int> The ID of the bank account.
	): string				// RETURNS <str> the name.
	
	// $name = AppBank::getName(Me::$id, 1);
	{
		return (string) Database::selectValue("SELECT name FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID));
	}
	
/****** Get Name ******/
	public static function getOthers
	(
		int $uniID		// <int> The UniID of the user.
	,	int $accountID	// <int> The ID of the bank account.
	): array <str, int>				// RETURNS <str:int> the list of people the account is shared with and their permissions.
	
	// $others = AppBank::getOthers(Me::$id, 1);
	{
		$others = json_decode(Database::selectValue("SELECT shared_with FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)), true);
		if($others != null)
			return $others;
		else
			return array();
	}
	
/****** Deposit or Withdraw Auro ******/
	public static function changeBalance
	(
		int $uniID		// <int> The UniID of the bank account owner.
	,	int $accountID	// <int> The ID of the bank account.
	,	int $change		// <int> The deposit or withdrawal amount.
	,	int $otherID = 0	// <int> The UniID of a user sharing the bank account, doing the action.
	): mixed				// RETURNS <mixed> FALSE on failure, otherwise the new balance.
	
	// $balance = AppBank::changeBalance(Me::$id, 1, -1000);
	{
		if(!$otherID || $otherID == $uniID)
		{
			$otherID = $uniID;
			if(!Database::selectOne("SELECT uni_id FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)))
			{
				return false;
			}
		}
		else
		{
			$access = Database::selectValue("SELECT access FROM bank_shared WHERE uni_id=? AND account_id=? LIMIT 1", array($otherID, $accountID));
			// check view permission
			if($access === false)
			{
				return false;
			}
			if($change < 0)
			{
				// check withdrawal permission
				if(!$access)
				{
					return false;
				}					
			}
		}

		// withdraw Auro
		if($change < 0)
		{
			$balance = self::getBalance($uniID, $accountID);
			// not enough balance to withdraw this amount
			if($balance < abs($change))
			{
				return false;
			}
			Database::startTransaction();
			if($pass = Database::query("UPDATE bank_accounts SET balance=balance-? WHERE uni_id=? AND account_id=? LIMIT 1", array(abs($change), $uniID, $accountID)))
			{
				if($pass = AppAuro::grantAuro($otherID, abs($change), false))
				{
					// record in Bank Log
					$pass = Database::query("INSERT INTO bank_records VALUES (?, ?, ?, ?, ?)", array($uniID, $accountID, $otherID, time(), $change));
					
					// only record in Auro Log when someone else withdraws
					if($uniID != $otherID && $pass)
					{
						$owner = User::get($uniID, "handle");
						$name = self::getName($uniID, $accountID);
						$pass = AppAuro::record($uniID, $otherID, ($uniID == $otherID ? -$change : $change), $owner['handle'] . '\'s Bank #' . $accountID . ' (' . $name . '): Withdrawal', "Karma System");
					}
				}
			}
			Database::endTransaction($pass);
			if($pass)
			{
				return self::getBalance($uniID, $accountID);
			}
			return false;
		}
		// deposit Auro
		elseif($change > 0)
		{
			Database::startTransaction();
			if($pass = Database::query("UPDATE bank_accounts SET balance=balance+? WHERE uni_id=? AND account_id=? LIMIT 1", array($change, $uniID, $accountID)))
			{
				// removes Auro and checks if user has enough
				if($pass = AppAuro::spendAuro($otherID, $change, false))
				{
					// record in Bank Log
					$pass = Database::query("INSERT INTO bank_records VALUES (?, ?, ?, ?, ?)", array($uniID, $accountID, $otherID, time(), $change));
					
					// only record when someone else deposits
					if($uniID != $otherID && $pass)
					{
						$owner = User::get($uniID, "handle");
						$name = self::getName($uniID, $accountID);
						$pass = AppAuro::record($otherID, $uniID, ($uniID == $otherID ? $change : -$change), $owner['handle'] . '\'s Bank #' . $accountID . ' (' . $name . '): Deposit', "Karma System");
					}
				}
			}
			Database::endTransaction($pass);
			if($pass)
			{
				return self::getBalance($uniID, $accountID);
			}
			return false;
		}
		
		return self::getBalance($uniID, $accountID);
	}

/****** Add user to bank account ******/
	public static function addShare
	(
		int $uniID		// <int> The UniID of the bank account owner.
	,	int $accountID	// <int> The ID of the bank account.
	,	int $otherID 	// <int> The UniID of a user sharing the bank account.
	,	int $level		// <int> The access level. 0 is deposit only, 1 is full access.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBank::addShare(Me::$id, 1, 5, 0);
	{
		if($uniID == $otherID)
		{
			return false;
		}
		
		if(!Database::selectOne("SELECT uni_id FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)))
		{
			return false;
		}
		
		$others = AppBank::getOthers($uniID, $accountID);
		if(count($others) > 9)
		{
			return false;
		}
				
		Database::startTransaction();
		if($pass = Database::query("REPLACE INTO bank_shared VALUES (?, ?, ?)", array($otherID, $accountID, $level)))
		{
			
			$others[$otherID] = $level;
			$pass = Database::query("UPDATE bank_accounts SET shared_with=? WHERE uni_id=? AND account_id=? LIMIT 1", array(json_encode($others), $uniID, $accountID));
		}
		Database::endTransaction($pass);		
		return $pass;		
	}
	
/****** Edit a user's access to bank account ******/
	public static function editShare
	(
		int $uniID		// <int> The UniID of the bank account owner.
	,	int $accountID	// <int> The ID of the bank account.
	,	int $otherID 	// <int> The UniID of a user sharing the bank account.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBank::editShare(Me::$id, 1, 5);
	{
		if(!Database::selectOne("SELECT uni_id FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)))
		{
			return false;
		}
		
		$others = AppBank::getOthers($uniID, $accountID);
		
		// switch between Deposit Only or Full Access
		$others[$otherID] = ($others[$otherID] ? 0 : 1);
		Database::startTransaction();
		if($pass = Database::query("UPDATE bank_shared SET access=? WHERE uni_id=? AND account_id=? LIMIT 1", array($others[$otherID], $otherID, $accountID)))
		{
			$pass = Database::query("UPDATE bank_accounts SET shared_with=? WHERE uni_id=? AND account_id=? LIMIT 1", array(json_encode($others), $uniID, $accountID));
		}
		Database::endTransaction($pass);		
		return $pass;		
	}
	
/****** Remove user from bank account ******/
	public static function removeShare
	(
		int $uniID		// <int> The UniID of the bank account owner.
	,	int $accountID	// <int> The ID of the bank account.
	,	int $otherID 	// <int> The UniID of a user sharing the bank account.
	): bool				// RETURNS <bool> TRUE on success, FALSE on failure.
	
	// AppBank::removeShare(Me::$id, 1, 5);
	{
		if(!Database::selectOne("SELECT uni_id FROM bank_accounts WHERE uni_id=? AND account_id=? LIMIT 1", array($uniID, $accountID)))
		{
			return false;
		}
		
		$others = AppBank::getOthers($uniID, $accountID);
		
		Database::startTransaction();
		if($pass = Database::query("DELETE FROM bank_shared WHERE uni_id=? AND account_id=? LIMIT 1", array($otherID, $accountID)))
		{
			unset($others[$otherID]);
			$pass = Database::query("UPDATE bank_accounts SET shared_with=? WHERE uni_id=? AND account_id=? LIMIT 1", array(json_encode($others), $uniID, $accountID));
		}
		Database::endTransaction($pass);		
		return $pass;		
	}
	
}