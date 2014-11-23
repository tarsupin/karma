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
<h1 style="padding-bottom:0px;">List of UniFaction Flair</h1>
<p style="margin-top:0px;">Note: this list only includes flair that can be obtained through the entire UniFaction system. Individual sites may have more flair listed.</p>';

$flairList = AppFlair::getGlobalByCategories();

AppFlair::drawList($flairList, true);


echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");