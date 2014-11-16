<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Get a list of bookmarks from the user
$bookmarkList = AppBookmarks::getUserList(Me::$id);

// Run the bookmark
if($value = Link::clicked() and $value == "bookmarkAction")
{
	if(isset($_GET['delGroup']) and isset($_GET['del']))
	{
		// If this is the first entry you've deleted (because you have no bookmarks), set the defaults
		if(!$bookmarkList)
		{
			// Set the user's default bookmarks
			$bookmarkList = AppBookmarks::assignDefaultBookmarks(Me::$id);
		}
		
		// Remove the designated bookmark
		if(AppBookmarks::unassignByType(Me::$id, Sanitize::variable($_GET['delGroup']), Sanitize::safeword($_GET['del'])))
		{
			Alert::success("Bookmark Deleted", "You have successfully deleted the bookmark.");
			
			$bookmarkList = AppBookmarks::getUserList(Me::$id);
		}
	}
}

// Link Prepare
$linkProtection = Link::prepare("bookmarkAction");

// Prepare the default bookmarks if you didn't have your own
if(!$bookmarkList)
{
	$bookmarkList = AppBookmarks::fetchDefaultBookmarks();
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
<h3>My Bookmarks</h3>';

echo '
<style>
.bm-opt { display:inline-block; padding:1px 6px 1px 6px; background-color:#eeeeee; border-radius:4px; }
.bm-delete { font-size:1.1em; vertical-align:middle; color:#dd8888; }
.bm-delete:hover { color:#ffaaaa; }
</style>
';

foreach($bookmarkList as $groupName => $groupList)
{
	echo '
	<div style="font-weight:bold; margin-top:16px;">' . $groupName . '</div>';
	
	foreach($groupList as $title => $url)
	{
		echo '
		<div class="bm-opt"><a href="/bookmarks?delGroup=' . $groupName . "&del=" . $title . "&" . $linkProtection . '"><span class="icon-circle-close bm-delete"></span></a> ' . $title . '</div>';
	}
}

echo '
</div>';

// Display the Footer
require(SYS_PATH . "/controller/includes/footer.php");