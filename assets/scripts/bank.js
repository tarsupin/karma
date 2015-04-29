// edit a bank account's name
function editName(id)
{
	var name = prompt("Please enter the new name:");
	if(name)
	{
		getAjax("", "rename-bank", "renameActivated", "id=" + id, "name=" + name);
	}
}

function renameActivated(response)
{
	if(!response) { alert("Sorry, the name contains invalid characters or is too long (max 30 characters)."); return; }
	response = JSON.parse(response);
	document.getElementById("name-" + response['id']).innerHTML = response['name'];
}

// withdraw Auro
function withdraw(id)
{
	var change = prompt("Please enter the amount to withdraw:");
	if(change)
	{
		getAjax("", "change-auro", "changeActivated", "id=" + id, "change=-" + change);
	}
}

// deposit Auro
function deposit(id)
{
	var change = prompt("Please enter the amount to deposit:");
	if(change)
	{
		getAjax("", "change-auro", "changeActivated", "id=" + id, "change=" + change);
	}
}

function changeActivated(response)
{
	if(!response) { alert("Sorry, something went wrong!"); return; }
	response = JSON.parse(response);
	if(response['balance'] != "")
	{
		document.getElementById("balance-" + response['id']).innerHTML = response['balance'];
		document.getElementById("balance").innerHTML = response['own'];
	}
	else
	{
		if(response['change'] < 0) { alert("You cannot withdraw more Auro than the bank account holds!"); }
		else { alert("You cannot deposit more Auro than you have on hand!"); }
	}
}

// add user to bank account
function addPermission(id)
{
	var handle = prompt("Please enter the user's handle:");
	if(handle)
	{
		if(confirm("Set " + handle + "'s access to this bank account:\n\nFull Access -> please confirm\nDeposit Only -> please deny\n\nYou can edit this setting later."))
		{
			getAjax("", "add-share-bank", "addActivated", "id=" + id, "handle=" + handle, "level=1");
		}
		else
		{
			getAjax("", "add-share-bank", "addActivated", "id=" + id, "handle=" + handle, "level=0");
		}
	}
}

function addActivated(response)
{
	if(!response) { alert("Sorry, the user could not be found."); return; }
	response = JSON.parse(response);
	document.getElementById("shared-" + response['id']).innerHTML = response['shared'];
}

// edit a user's access to bank account
function editPermission(id, handle)
{
	getAjax("", "edit-share-bank", "editActivated", "id=" + id, "handle=" + handle);
}

function editActivated(response)
{
	if(!response) { alert("Sorry, something went wrong."); return; }
	response = JSON.parse(response);
	document.getElementById("shared-" + response['id']).innerHTML = response['shared'];
}

// remove a user's access to bank account
function removePermission(id, handle)
{
	if(confirm("Are you sure you want to remove " + handle + " from this bank account?"))
	{
		getAjax("", "remove-share-bank", "removeActivated", "id=" + id, "handle=" + handle);
	}
}

function removeActivated(response)
{
	if(!response) { alert("Sorry, something went wrong."); return; }
	response = JSON.parse(response);
	document.getElementById("shared-" + response['id']).innerHTML = response['shared'];
}

// remove a user's access to bank account
function leaveAccount(id)
{
	if(confirm("Are you sure you want to remove your own access from this bank account? This can only be undone by the owner."))
	{
		getAjax("", "leave-share-bank", "leaveActivated", "id=" + id);
	}
}

function leaveActivated(response)
{
	if(!response) { alert("Sorry, something went wrong."); return; }
	response = JSON.parse(response);
	var row = document.getElementById("balance-" + response['id']).parentNode.parentNode;
	row.parentNode.removeChild(row);
}