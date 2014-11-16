<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Make sure you're logged in
if(!Me::$loggedIn)
{
	Me::redirectLogin("/auro-transactions", "/");
}

// Prepare Values
$myHandle = Me::$vals['handle'];
$numRows = 25;		// The number of rows to return in the recorded transaction table
$page = (isset($_GET['page']) ? $_GET['page'] + 0 : 1);		// The position to start at (for pagination)

// Get the user's auro transactions
$auroRecords = AppAuro::getRecords(Me::$id, $page, $numRows);

// Run Global Script
require(APP_PATH . "/includes/global.php");

// Display the Header
require(SYS_PATH . "/controller/includes/metaheader.php");
require(SYS_PATH . "/controller/includes/header.php");

// Display Side Panel
require(SYS_PATH . "/controller/includes/side-panel.php");

// Display Content
echo '
<div id="panel-right"></div>
<div id="content">'	. Alert::display();

echo '
<style>
	table { border-right:solid 1px #e2e2e1; width:100%; text-align:left; }
	tr { }
	th { border-left:solid 1px #e2e2e1; color:white; background-color:#57c2c1; padding:6px 10px 6px 12px; }
	td { border-left:solid 1px #e2e2e1; color:#263a54; padding:6px 10px 6px 12px; font-size:0.85em; }
	
	tr:nth-child(odd) { background-color:#f8f8f7; }
	
	.desc { max-width:120px; }
</style>

<h3>My Auro Transactions</h3>

<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th>Site</th>
		<th>Exchanger</th>
		<th>Amount</th>
		<th>Description</th>
		<th>Date</th>
	</tr>';

// Cycle through the records
$curTime = time();
$curYear = date('Y', $curTime);

foreach($auroRecords as $record)
{
	// Prepare the "Exchanger" Entry
	if($record['other_id'])
	{
		$handle = $record['handle'] ? '<a href="' . URL::unifaction_social() . '/' . $record['handle'] . '">@' . $record['handle'] . '</a>' : 'Unknown';
	}
	else
	{
		$handle = "UniFaction";
	}
	
	// Display the Transactions
	echo '
	<tr>
		<td>' . $record['site_name'] . '</td>
		<td>' . $handle . '</td>
		<td>' . $record['amount'] . ' Auro</td>
		<td class="desc">' . $record['description'] . '</td>
		<td>' . date('M jS' . (date('Y', $record['date_exchange']) != $curYear ? ", Y" : ""), $record['date_exchange']) . '</td>
	</tr>';
}

// If you have no records present
if(!$auroRecords)
{
	echo '
	<tr><td colspan="6">You have no transaction history at this time.</td></tr>';
}

echo '
</table>';

// Extra Pagination
echo '
<div style="margin-top:14px;text-align:right;">';

if($page > 1)
{
	echo '
	<a class="button" href="/auro-transactions?page=' . max(1, ($page - 1)) . '">Earlier Transactions</a>';
}

if(count($auroRecords) >= $numRows)
{
	echo '
	<a class="button" href="/auro-transactions?page=' . ($page + 1) . '">Older Transactions</a>';
}

echo '
</div>
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");