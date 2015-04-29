<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// The user must be logged in
if(!Me::$loggedIn)
{
	exit;
}

$_POST['balance'] = "";

// Find Owner
$owner = Database::selectValue("SELECT uni_id FROM bank_accounts WHERE account_id=? LIMIT 1", array((int) $_POST['id']));
if($owner !== false)
{
	// Deposit or Withdraw the amount (checks are performed in the function)
	$balance = AppBank::changeBalance((int) $owner, (int) $_POST['id'], (int) $_POST['change'], Me::$id);
	if($balance !== false)
	{
		$_POST['balance'] = number_format($balance) . " Auro";
		$own = AppAuro::getData(Me::$id);
		$_POST['own'] = number_format($own['auro']) . " Auro";
	}
}
echo json_encode($_POST);