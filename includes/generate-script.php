<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Community Bookmarks
$bookmarkID = AppBookmarks::create("Communities", "Avatar", "http://avatar.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Books", "http://books.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Gaming", "http://gaming.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Humor", "http://humor.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Movies", "http://movies.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Music", "http://books.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Pets", "http://pets.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Politics", "http://politics.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Shows", "http://shows.unifaction.community");
$bookmarkID = AppBookmarks::create("Communities", "Tech", "http://tech.unifaction.community");

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

// Create Flair
AppFlair::create("Friendly", "Invite a friend that becomes an active user on UniFaction.", "Automatically earn +15 auro every day that you log in.", "", array("free_auro_per_day" => 15));

AppFlair::create("Charismatic", "Invite 3 friends that become active users on UniFaction.", "Automatically earn +15 auro every day that you log in.", "", array("free_auro_per_day" => 15));

AppFlair::create("Charming", "Invite 10 friends that become active users on UniFaction.", "Automatically earn +20 auro every day that you log in.", "", array("free_auro_per_day" => 20));

AppFlair::create("Life of the Party", "Invite 25 friends that become active users on UniFaction.", "Automatically earn +25 auro every day that you log in.", "", array("free_auro_per_day" => 25));
