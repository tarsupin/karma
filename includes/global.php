<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); } 

// UniFaction Dropdown Menu
WidgetLoader::add("UniFactionMenu", 10, '
<div class="menu-wrap hide-600">
	<ul class="menu"><li class="menu-slot"><a href="/">Public Chats</a><ul><li class="dropdown-slot"><a href="/Books">Books</a></li><li class="dropdown-slot"><a href="/Food">Food</a></li><li class="dropdown-slot"><a href="/Gaming">Gaming</a></li><li class="dropdown-slot"><a href="/Movies">Movies</a></li></ul></li><li class="menu-slot"><a href="/private-join">Private Rooms</a></li><li class="menu-slot"><a href="/private-create">Create Room</a></li></ul>
</div>');

// Main Navigation
WidgetLoader::add("SidePanel", 10, '
<div class="panel-box">
	<ul class="panel-slots">
		<li class="nav-slot"><a href="/">Home<span class="icon-circle-right nav-arrow"></span></a></li>
		<li class="nav-slot"><a href="/Books">Chatroom: Books<span class="icon-circle-right nav-arrow"></span></a></li>
	</ul>
</div>');
