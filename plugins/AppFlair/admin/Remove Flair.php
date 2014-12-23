<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Get user data, if applicable
$username = isset($url[3]) ? Sanitize::variable($url[3]) : '';
$flairList = array();
$userData = array();

// Get User Data and Flair
if($username)
{
	if($userData = User::getDataByHandle($username, "uni_id, handle, display_name"))
	{
		// Remove Flair
		if(isset($_GET['del']))
		{
			AppFlair::unassignByID((int) $userData['uni_id'], (int) $_GET['del']);
		}
		
		// Flair List
		$flairList = AppFlair::getUserList((int) $userData['uni_id']);
	}
}

// Form Submission
if(Form::submitted("remove-uni6-flair"))
{
	// Check if all of the input you sent is valid: 
	FormValidate::variable("Handle", $_POST['handle'], 1, 22);
	
	// Final Validation Test
	if(FormValidate::pass())
	{
		header("Location: /admin/AppFlair/Remove Flair/" . $_POST['handle']); exit;
	}
}

// Sanitize Post Values
else
{
	$_POST['handle'] = isset($_POST['handle']) ? Sanitize::variable($_POST['handle']) : "";
}

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Show the user's flair (if applicable)
if($flairList)
{
	foreach($flairList as $flair)
	{
		$details = json_decode($flair['settings_json'], true);
		
		echo '
		<div style="margin-bottom:16px;">
			<a href="/admin/AppFlair/Remove Flair/' . $userData['handle'] . '?del=' . $flair['id'] . '" style="background-color:#eea0a0; border-radius:4px; padding:2px 6px 2px 6px;">X</a> <div style="display:inline-block; font-size:0.9em; border-radius:6px; padding:4px; margin:3px; background-color:#' . $flair['color'] . ';"><a href="' . URL::karma_unifaction_com() . '/flair/' . $flair['title'] . '" style="color:#606060;"><span class="' . $flair['icon_class'] . '" style="font-size:1.2em; vertical-align:middle;"></span> ' . $flair['title'] . '</a></div>
			<div style="display:inline-block;"> - ' . $flair['description'] . '</div>';
		
		// Prepare mini-descriptions
		$miniDesc = $flair['reward_desc'];
		
		if(isset($details['free_auro_per_day']))
		{
			$miniDesc .= ' The user gains +' . $details['free_auro_per_day'] . ' auro per day' . (isset($details['limited']) ? ' while this flair is active.' : '.');
		}
		
		if(isset($details['assigned']))
		{
			$miniDesc .= ' This flair can only be assigned by the UniFaction staff.';
		}
		
		// Display the description
		if($miniDesc)
		{
			echo '
			<div style="font-size:0.8em;">' . $miniDesc . '</div>';
		}
		
		echo '
		</div>';
	}
}
else
{
	// Display the Form to find the user's flair
	echo '
	<h3>Remove Flair from User</h3>
	<form class="uniform" action="/admin/AppFlair/Remove Flair" method="post">' . Form::prepare("remove-uni6-flair") . '
	
	<p>
		<strong>Username:</strong><br />
		<input type="text" name="handle" value="' . $_POST['handle'] . '" placeholder="e.g. @admin" style="width:200px;" maxlength="22" />
		<input type="submit" name="submit" value="Get User\'s Flair List" />
	</p>
	</form>';
}

// Display the Footer
require(SYS_PATH . "/controller/includes/admin_footer.php");
