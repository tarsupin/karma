<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure you're logged in
if(!Me::$loggedIn)
{
	Me::redirectLogin("/bank", "/");
}

if(Me::$clearance < 4)
{
	header("Location:/"); exit;
}

// New bank account if the user owns less than 10
if(Form::submitted("new-bank-account"))
{
	// Check the name
	if(AppBank::createAccount(Me::$id, $_POST['accountname']))
	{
		Alert::success("Account Created", "The bank account has been created!");
	}
	else
	{
		Alert::error("Account Not Created", "The account could not be created. Please check whether the name contains invalid characters.");
	}
}

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Display the page
echo '
<div id="panel-right"></div>
<div id="content">' . Alert::display();

if(!$auroData = AppAuro::getData(Me::$id))
{
	$auroData['auro'] = 0;
}

echo '
<h1>Bank</h1>
<p>You currently have <span id="balance">' . number_format($auroData['auro']) . ' Auro</span>.</p>
<style>
	table { border-right:solid 1px #e2e2e1; width:100%; text-align:left; }
	tr { }
	th { border-left:solid 1px #e2e2e1; color:white; background-color:#57c2c1; padding:6px 10px 6px 12px; }
	td { border-left:solid 1px #e2e2e1; color:#263a54; padding:6px 10px 6px 12px; }
	tr:nth-child(odd) { background-color:#f8f8f7; }
	tr th:first-child { width:20%; }
	tr th:nth-child(2) { width:30%; }
	
	table [class^="icon-"], [class*=" icon-"] { vertical-align:text-bottom; }
</style>';

// List of owned bank accounts
$list = AppBank::getOwnedAccounts(Me::$id);
echo '
<h3>Owned Bank Accounts</h3>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th>Name</th>
		<th>Balance</th>
		<th>Shared With</th>
	</tr>';
foreach($list as $l)
{
	$shared = json_decode($l['shared_with'], true);
	$sharedwith = array();
	if($shared != null)
	{	
		foreach($shared as $key => $value)
		{
			$user = User::get((int) $key, "handle");
			$sharedwith[] = '<a href="javascript:removePermission(' . $l['account_id'] . ', \'' . $user['handle'] . '\');" title="Remove Access"><span class="icon-trash"></span></a> <a href="javascript:editPermission(' . $l['account_id'] . ', \'' . $user['handle'] . '\');" title="Edit Access"><span class="icon-settings"></span></a> ' . $user['handle'] . " (" . ($value ? "Full Access" : "Deposit Only") . ")";
		}
	}
	if(count($sharedwith) <= 9)
		$sharedwith[] = '<a href="javascript:addPermission(' . $l['account_id'] . ');" title="Add User"><span class="icon-attachment"></span></a>';
	echo '
	<tr>
		<td><a href="javascript:editName(' . $l['account_id'] . ');" title="Edit Name"><span class="icon-pencil"></span></a> <span id="name-' .$l['account_id'] . '">' . $l['name'] . '</span></td>
		<td><a href="javascript:withdraw(' . $l['account_id'] . ');" title="Withdraw Auro"><span class="icon-circle-minus" style="font-size:1.3em;"></span></a> &nbsp; <a href="javascript:deposit(' . $l['account_id'] . ');" title="Deposit Auro"><span class="icon-circle-plus" style="font-size:1.3em;"></span></a> &nbsp; <span id="balance-' . $l['account_id'] . '">' . number_format($l['balance']) . ' Auro</a></td>
		<td id="shared-' . $l['account_id'] . '">' . implode(", ", $sharedwith) . '</td>
	</tr>';
}
echo '
</table>';

// List of shared bank accounts
$list = AppBank::getSharedAccounts(Me::$id);
echo '
<br/>
<h3>Shared Bank Accounts</h3>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th>Name</th>
		<th>Balance</th>
		<th>Shared By</th>
	</tr>';
foreach($list as $l)
{
	$owner = User::get((int) $l['uni_id'], "handle");
	echo '
	<tr>
		<td><a href="javascript:leaveAccount(' . $l['account_id'] . ');" title="Leave Bank Account"><span class="icon-login"></span></a> ' . $l['name'] . '</td>
		<td>' . ($l['access'] ? '<a href="javascript:withdraw(' . $l['account_id'] . ');" title="Withdraw Auro"><span class="icon-circle-minus" style="font-size:1.3em;"></span></a> &nbsp; ' : '<span class="icon-circle-close" style="font-size:1.3em;" title="Deposit Only"></span> &nbsp; ') . '<a href="javascript:deposit(' . $l['account_id'] . ');" title="Deposit Auro"><span class="icon-circle-plus" style="font-size:1.3em;"></span></a> &nbsp; <span id="balance-' . $l['account_id'] . '">' . number_format($l['balance']) . ' Auro</a></td>
		<td>Owner: ' . $owner['handle'] . ', Access: ' . ($l['access'] ? "Full Access" : "Deposit Only") . '</td>
	</tr>';
}
echo '
</table>';

// Make new bank account
$count = AppBank::ownedAccounts(Me::$id);
echo '
<br/>
<h3>New Bank Account</h3>
<p>You may own up to 10 bank accounts.</p>';
if($count < 10)
{
	echo '
<form class="uniform" method="post">' . Form::prepare("new-bank-account") . '
	<input type="text" name="accountname" maxlength="30" placeholder="Bank Account Name" />
	<input type="submit" value="Create" />
</form>';
}

echo '
</div>';

// Display the Footer
Metadata::addFooter('<script src="/assets/scripts/bank.js"></script>');
require(SYS_PATH . "/controller/includes/footer.php");