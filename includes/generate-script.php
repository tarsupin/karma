<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

AppFlair::create("Friendly", "Invite a friend that becomes an active user on UniFaction.", "Automatically earn +15 auro every day that you log in.", "", array("free_auro_per_day" => 15));

AppFlair::create("Charismatic", "Invite 3 friends that become active users on UniFaction.", "Automatically earn +15 auro every day that you log in.", "", array("free_auro_per_day" => 15));

AppFlair::create("Charming", "Invite 10 friends that become active users on UniFaction.", "Automatically earn +20 auro every day that you log in.", "", array("free_auro_per_day" => 20));

AppFlair::create("Life of the Party", "Invite 25 friends that become active users on UniFaction.", "Automatically earn +25 auro every day that you log in.", "", array("free_auro_per_day" => 25));
