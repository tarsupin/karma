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
<h3>UniFaction Karma System</h3>';

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");