<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Load the appropriate page
if(!isset($userData))
{
	header("Location: /"); exit;
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

echo '
<h1>' . $userData['display_name'] . '\'s Flair</h1>';

if($flairList = AppFlair::getUserList((int) $userData['uni_id']))
{
	AppFlair::drawList($flairList);
}
else
{
	echo '
	<p>' . $userData['display_name'] . ' is not currently revealing any visible flair.</p>';
}

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");