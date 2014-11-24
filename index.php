<?php

/****** Preparation ******/
define("CONF_PATH",		dirname(__FILE__));
define("SYS_PATH", 		dirname(CONF_PATH) . "/system");

// Load phpTesla
require(SYS_PATH . "/phpTesla.php");

// Initialize Active User
Me::initialize();

// Determine which page you should point to, then load it
require(SYS_PATH . "/routes.php");

/****** Dynamic URLs ******/
// If a page hasn't loaded yet, check if there is a dynamic load
if($url[0] != '')
{
	// Determine the handle
	if(!$userData = User::getDataByHandle(Sanitize::variable($url[0]), "uni_id, handle, display_name"))
	{
		User::silentRegister(Sanitize::variable($url[0]));
		
		$userData = User::getDataByHandle(Sanitize::variable($url[0]), "uni_id, handle, display_name");
	}
	
	if($userData)
	{
		require(APP_PATH . '/controller/user.php'); exit;
	}
}
//*/

/****** 404 Page ******/
// If the routes.php file or dynamic URLs didn't load a page (and thus exit the scripts), run a 404 page.
require(SYS_PATH . "/controller/404.php");