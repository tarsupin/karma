<?php if(!defined("CONF_PATH")) { die("No direct script access allowed."); }

// Prepare Values
if(!isset($_POST['flair'])) { $_POST['flair'] = 0; }
if(!isset($_POST['duration'])) { $_POST['duration'] = 0; }

// Form Submission
if(Form::submitted("assign-uni6-flair"))
{
	// Check if all of the input you sent is valid: 
	FormValidate::variable("Handle", $_POST['handle'], 1, 22);
	FormValidate::number("Flair", $_POST['flair'], 1);
	FormValidate::number("Duration", $_POST['duration'], 0);
	
	// Final Validation Test
	if(FormValidate::pass())
	{
		if($uniID = User::getIDByHandle($_POST['handle']))
		{
			AppFlair::assignByID($uniID, (int) $_POST['flair'], (int) $_POST['duration']);
			
			Alert::success("Flair Assigned", "You have successfully assigned the flair to " . $_POST['handle']);
		}
	}
}

// Sanitize Post Values
else
{
	$_POST['handle'] = isset($_POST['handle']) ? Sanitize::variable($_POST['handle']) : "";
	$_POST['flair'] = (int) $_POST['flair'];
	$_POST['duration'] = (int) $_POST['duration'];
}

// Run Header
require(SYS_PATH . "/controller/includes/admin_header.php");

// Display the Editing Form
echo '
<h3>Assign Global Flair to a User</h3>
<form class="uniform" action="/admin/AppFlair/Assign Flair" method="post">' . Form::prepare("assign-uni6-flair") . '

<p>
	<strong>Username:</strong><br />
	<input type="text" name="handle" value="' . $_POST['handle'] . '" placeholder="e.g. @admin" style="width:200px;" maxlength="22" />
</p>

<p>
	<strong>Global Flair to Assign:</strong><br />
	<select name="flair">
		<option value="0">-- Select Flair to Assign --</option>';
	
	// Get the list of flair
	$curCat = "";
	$flairList = AppFlair::getGlobalByCategories();
	
	foreach($flairList as $flair)
	{
		// Check if the category has changed
		if($flair['category'] != $curCat)
		{
			$curCat = $flair['category'];
			
			echo '
			<option value="' . $flair['id'] . '" disabled><strong>' . $curCat . '</strong></option>';
		}
		
		echo '
		<option value="' . $flair['id'] . '"' . ($_POST['flair'] == $flair['id'] ? ' selected' : '') . '> &nbsp; &nbsp; &bull; ' . $flair['title'] . '</option>';
	}
	
echo '
	</select>
</p>

<p>
	<strong>Duration to Give Flair:</strong><br />
	<input type="text" name="duration" value="' . $_POST['duration'] . '" maxlength="15" /> seconds (0 is permanent)
	<div style="font-size:0.9em;">1 week (604800), 30 days (2592000), 60 days (5184000), 90 days (7776000)
</p>

<p><input type="submit" name="submit" value="Assign Flair" /></p>
</form>';

// Display the Footer
require(SYS_PATH . "/controller/includes/admin_footer.php");
