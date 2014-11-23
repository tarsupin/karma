<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Community Bookmarks
$bookmarkID = AppBookmarks::create("Communities", "Avatar", "http://avatar.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Books", "http://books.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Fashion", "http://fashion.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Fitness", "http://fitness.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Food", "http://food.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Gaming", "http://gaming.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Humor", "http://humor.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Movies", "http://movies.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Music", "http://books.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Pets", "http://pets.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Relationships", "http://relationships.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Shows", "http://shows.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Travel", "http://tech.unifaction.community");

$bookmarkID = AppBookmarks::create("Communities", "News", "http://news.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Politics", "http://politics.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Science", "http://science.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Tech", "http://tech.unifaction.community");

$bookmarkID = AppBookmarks::create("Communities", "MLB", "http://mlb.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "NBA", "http://nba.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "NCAAF", "http://ncaaf.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "NCAAM", "http://ncaam.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "NFL", "http://nfl.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "NHL", "http://nhl.unifaction.community");

$bookmarkID = AppBookmarks::create("Communities", "Art", "http://art.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Programming", "http://programming.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "WebDev", "http://webdev.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Writing", "http://writing.unifaction.community");

$bookmarkID = AppBookmarks::create("Communities", "Auto DIY", "http://diyauto.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Home Improvement", "http://diyhome.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Interior Design", "http://intdesign.unifaction.community");


// Sites Bookmarks
$bookmarkID = AppBookmarks::create("Sites", "Avatar", "http://avatar.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Books", "http://books.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Chatrooms", "http://chat.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Design4", "http://design4.today");
$bookmarkID = AppBookmarks::create("Sites", "Entertainment", "http://entertainment.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Food", "http://food.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Fashion", "http://fashion.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Gaming", "http://gaming.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "GoTrek", "http://gotrek.today");
$bookmarkID = AppBookmarks::create("Sites", "Movies", "http://movies.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Music", "http://music.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "News", "http://news.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Sports", "http://sports.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "Tech", "http://tech.unifaction.com");
$bookmarkID = AppBookmarks::create("Sites", "The Nooch", "http://thenooch.org");
$bookmarkID = AppBookmarks::create("Sites", "Travel", "http://travel.unifaction.com");

// Friends
$color = "FFFACD";
$icon = "icon-group";

AppFlair::create("Friends", "Friendly", "Invite a friend that becomes an active user on UniFaction.", $color, "", "", array("free_auro_per_day" => 15), $icon);

AppFlair::create("Friends", "Charismatic", "Invite 3 friends that become active users on UniFaction.", $color, "", "", array("free_auro_per_day" => 30), $icon);

AppFlair::create("Friends", "Charming", "Invite 10 friends that become active users on UniFaction.", $color, "", "", array("free_auro_per_day" => 50), $icon);

AppFlair::create("Friends", "Life of the Party", "Invite 25 friends that become active users on UniFaction.", $color, "", "", array("free_auro_per_day" => 100), $icon);


// Projects
$color = "CCFF99";
$icon = "icon-earth";

AppFlair::create("Projects", "UniFaction Aide", "Help UniFaction with a project.", $color, "", "", array("free_auro_per_day" => 200, "limited" => true, "assigned" => true), $icon);

AppFlair::create("Projects", "Project Coordinator", "Help UniFaction coordinate a project, taking responsibility for its progress.", $color, "", "", array("free_auro_per_day" => 250, "limited" => true, "assigned" => true), $icon);


// Supporters
$color = "98FB98";
$icon = "icon-coin";

AppFlair::create("Supporters", "Supporter", "Are a contributor to UniFaction.", $color, "", "", array(), $icon);
AppFlair::create("Supporters", "Major Supporter", "Are one of the top contributors to UniFaction.", $color, "", "", array(), $icon);


// Staff
$color = "DEB887";
$icon = "icon-wand";

AppFlair::create("Staff", "Moderator", "An official UniFaction moderator.", $color, "", "", array(), $icon);
AppFlair::create("Staff", "Writer", "An official UniFaction writer.", $color, "", "", array(), $icon);
AppFlair::create("Staff", "Admin", "An official UniFaction administrator.", $color, "", "", array(), $icon);
AppFlair::create("Staff", "Programmer", "An official UniFaction programmer.", $color, "", "", array(), $icon);
AppFlair::create("Staff", "Artist", "An official UniFaction artist.", $color, "", "", array(), $icon);

// Special
$color = "FFE4E1";
$icon = "icon-star";

AppFlair::create("Special", "Original User", "Was using UniFaction back in its original days.", $color, "", "", array("free_auro_per_day" => 50, ), $icon);
