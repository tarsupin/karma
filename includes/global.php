<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// If no active user is selected, set yourself
if(!You::$id and Me::$loggedIn)
{
	You::$id = Me::$id;
	You::$handle = Me::$vals['handle'];
}

// Load the Social Menu
require(SYS_PATH . "/controller/includes/social-menu.php");

if(Me::$loggedIn)
{
	// Karma Dropdown Menu
	WidgetLoader::add("UniFactionMenu", 10, '
	<div class="menu-wrap hide-600">
		<ul class="menu">' . (isset($uniMenu) ? $uniMenu : '') . '<li class="menu-slot"><a href="/' . Me::$vals['handle'] . '">My Flair</a></li><li class="menu-slot"><a href="/auro-transactions">My Auro</a></li><li class="menu-slot"><a href="/bookmarks">My Bookmarks</a></li><li class="menu-slot"><a href="/flair">Flair List</a></li>
		</ul>
	</div>');
	
	// Main Navigation
	WidgetLoader::add("MobilePanel", 10, '
	<div class="panel-box">
		<ul class="panel-slots">
			<li class="nav-slot"><a href="/' . Me::$vals['handle'] . '">My Flair<span class="icon-circle-right nav-arrow"></span></a></li>
			<li class="nav-slot"><a href="/auro-transactions">My Auro<span class="icon-circle-right nav-arrow"></span></a></li>
			<li class="nav-slot"><a href="/bookmarks">My Bookmarks<span class="icon-circle-right nav-arrow"></span></a></li>
			<li class="nav-slot"><a href="/flair">Flair List<span class="icon-circle-right nav-arrow"></span></a></li>
		</ul>
	</div>');
}
else
{
	// Karma Dropdown Menu
	WidgetLoader::add("UniFactionMenu", 10, '
	<div class="menu-wrap hide-600">
		<ul class="menu">' . (isset($uniMenu) ? $uniMenu : '') . '<li class="menu-slot"><a href="/flair">Flair List</a></li>
		</ul>
	</div>');
	
	// Main Navigation
	WidgetLoader::add("MobilePanel", 10, '
	<div class="panel-box">
		<ul class="panel-slots">
			<li class="nav-slot"><a href="/flair">Flair List<span class="icon-circle-right nav-arrow"></span></a></li>
		</ul>
	</div>');
}