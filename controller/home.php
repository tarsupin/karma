<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

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

echo '
<h3>UniFaction Karma System</h3>
<p>Karma</p>';



//$success = AppAuro::exchangeAuro(Me::$id, 2, 5);
//var_dump($success);

//$success = AppAuro::grantAuro(Me::$id, 10, true, "Got some auro.");
//$success = AppAuro::spendAuro(Me::$id, 20, true, "Purchased a pet.");

/*
$auroData = AppAuro::getData(Me::$id);

var_dump($auroData);

$auroRecords = AppAuro::getRecords(Me::$id, 1, 20);

var_dump($auroRecords);
*/

$flairID = 7;

$flairData = AppFlair::getData($flairID);

var_dump($flairData);

$rewardResults = AppFlair::compileUserFlairRewards(Me::$id);

var_dump($rewardResults);

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");