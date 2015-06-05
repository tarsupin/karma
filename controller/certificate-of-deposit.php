<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure you're logged in
if(!Me::$loggedIn)
{
	Me::redirectLogin("/certificate-of-deposit", "/");
}

// New bank CD if the user owns less than 3
if(Form::submitted("new-cd") && in_array((int) $_POST['plan'], array(1, 2, 3)))
{
	if(AppCD::createAccount(Me::$id, (int) $_POST['plan'], (int) $_POST['deposit']))
	{
		Alert::success("Account Created", "The CD has been created!");
	}
	else
	{
		Alert::error("Account Not Created", "The CD could not be created.");
	}
}

// Manually mature CD
if($link = Link::clicked())
{
	if($link == "cd-mature")
	{
		AppCD::mature(Me::$id, (int) $_GET['mature']);
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
<h1>Certificates of Deposit</h1>
<p>You currently have ' . number_format($auroData['auro']) . ' Auro.</p>
<h3>Running CDs</h3>
<style>
	table { border-right:solid 1px #e2e2e1; width:100%; text-align:left; table-layout:fixed; }
	tr { }
	th { border-left:solid 1px #e2e2e1; color:white; background-color:#57c2c1; padding:6px 10px 6px 12px; }
	td { border-left:solid 1px #e2e2e1; color:#263a54; padding:6px 10px 6px 12px; }
	tr:nth-child(odd) { background-color:#f8f8f7; }
</style>';

// List of CDs
$list = AppCD::getOwnedAccounts(Me::$id);
echo '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th>Plan</th>
		<th>Deposit</th>
		<th>Outcome</th>
		<th>Matures</th>
	</tr>';
foreach($list as $l)
{
	echo '
	<tr>
		<td>' . ($l['plan'] == 1 ? "Short" : ($l['plan'] == 2 ? "Medium" : "Long")) . ' Plan</td>
		<td>' . number_format($l['deposit']) . ' Auro</td>
		<td>' . number_format(AppCD::getOutcome($l)) . ' Auro</td>
		<td>' . ($l['date_expire'] <= time() ? '<a href="/certificate-of-deposit?mature=' . $l['account_id'] . '&' . Link::prepare("cd-mature") . '"><span class="icon-circle-check"></span> Claim Auro</a>' : date("M j, Y g:ia", $l['date_expire'])) . '</td>
	</tr>';
}
echo '
</table>';

// Make new CD
$count = AppCD::ownedAccounts(Me::$id);
echo '
<br/>
<h3>New CD</h3>
<p>Certificates of Deposit (CDs) lock Auro away for a certain time and then return them with interest.<br/>You may have up to 3 CDs running at the same time.</p>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>Short Plan</td>
		<td>' . AppCD::$duration[1] . ' Days</td>
		<td>' . AppCD::$interest[1] . ' % Interest</td>
	</tr>
	<tr>
		<td>Medium Plan</td>
		<td>' . AppCD::$duration[2] . ' Days</td>
		<td>' . AppCD::$interest[2] . ' % Interest</td>
	</tr>
	<tr>
		<td>Long Plan</td>
		<td>' . AppCD::$duration[3] . ' Days</td>
		<td>' . AppCD::$interest[3] . ' % Interest</td>
	</tr>
</table>';
if($count < 3)
{
	echo '
<br/>
<form class="uniform" method="post">' . Form::prepare("new-cd") . '
	<select name="plan">
		<option value="">Choose Plan:</option>
		<option value="1">Short Plan</option>
		<option value="2">Medium Plan</option>
		<option value="3">Long Plan</option>
	</select>
	<input type="text" name="deposit" maxlength="10" placeholder="Deposit Amount" />
	<input type="submit" value="Create" />
</form>';
}

echo '
</div>';

// Display the Footer
Metadata::addFooter('<script src="/assets/scripts/bank.js"></script>');
require(SYS_PATH . "/controller/includes/footer.php");