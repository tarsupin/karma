<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// If no active user is selected, set yourself
if(!You::$id)
{
	You::$id = Me::$id;
	You::$handle = Me::$vals['handle'];
}

// Load the Social Menu
require(SYS_PATH . "/controller/includes/social-menu.php");

// Load the Social Menu
require(SYS_PATH . "/controller/includes/uni-menu.php");

/*
// UniFaction Dropdown Menu
WidgetLoader::add("UniFactionMenu", 10, '
<div class="menu-wrap hide-600">
	<ul class="menu">' . (isset($uniMenu) ? $uniMenu : '') . '<li class="menu-slot"><a href="/auro-transactions">Auro</a></li><li class="menu-slot"><a href="/bookmarks">Bookmarks</a></li><li class="menu-slot"><a href="/flair">Flair</a></li></ul>
</div>');
*/

// Main Navigation
WidgetLoader::add("SidePanel", 10, '
<div class="panel-box">
	<ul class="panel-slots">
		<li class="nav-slot"><a href="/">Home<span class="icon-circle-right nav-arrow"></span></a></li>
		<li class="nav-slot"><a href="/Books">Chatroom: Books<span class="icon-circle-right nav-arrow"></span></a></li>
	</ul>
</div>');
