<?php
/**
 * feed_edit.php is a single page web application that allows us to select a customer
 * and edit their data
 *
 * @package NewsFeed
 * @author Zack <zacharyforreal@gmail.com>
 * @author Jen Villacis <jennifer.villacis@seattlecentral.ed>u
 * @author Eden Dubiso <eden.dubiso@seattlecentral.edu>
 * @version 0.1 2018/08/05
 * @link http://www.edendu.com/
 * @license https://www.apache.org/licenses/LICENSE-2.0
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials

//END CONFIG AREA ----------------------------------------------------------

# Read the value of 'action' whether it is passed via $_POST or $_GET with $_REQUEST
if(isset($_REQUEST['act'])){$myAction = (trim($_REQUEST['act']));}else{$myAction = "";}

switch ($myAction) 
{//check 'act' for type of process
	case "edit": //2) show change form
	 	editDisplay();
	 	break;
	case "update": //3) Change feedID
		updateExecute();
		break; 
	default: //1)Select news feed from list
	 	selectFirst();
}

function selectFirst()
{//Select Customer
	global $config;
	$config->loadhead .= '<script type="text/javascript" src="' . VIRTUAL_PATH . 'include/util.js"></script>
	<script type="text/javascript">
			function checkForm(thisForm)
			{//check form data for valid info
				if(empty(thisForm.FeedID,"Please Select a Customer.")){return false;}
				return true;//if all is passed, submit!
			}
	</script>
	';
	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>';

	$sql = "select FeedID,CategoryID,SubCategory,Description from sm18_News_Feeds";
	$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0)//at least one record!
	{//show results
		echo '<form action="' . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">';  //TWO COPIES OF THIS LINE IN ORIG!!
		echo '<table align="center" border="1" style="border-collapse:collapse" cellpadding="3" cellspacing="3">';
		echo '<tr>
				<th>FeedID</th>
				<th>CategoryID</th>
				<th>SubCategory</th>
				<th>Description</th>
			</tr>
			';
		while ($row = mysqli_fetch_assoc($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
			echo '<tr>
					<td>
				 	<input type="radio" name="FeedID" value="' . (int)$row['FeedID'] . '">'
				     . (int)$row['FeedID'] . '</td>
				    <td>' . (int)$row['CategoryID'] . '</td>
				    <td>' . dbOut($row['SubCategory']) . '</td>
				    <td>' . dbOut($row['Description']) . '</td>
				</tr>
				';
		}
		echo '<input type="hidden" name="act" value="edit" />';
		echo '<tr>
				<td align="center" colspan="4">
					<input type="submit" value="Choose News Feed!"></em>
				</td>
			  </tr>
			  </table>
			  </form>
			  ';
	}else{//no records
      echo '<div align="center"><h3>Currently No Customers in Database.</h3></div>';
	}
	@mysqli_free_result($result); //free resources
	get_footer();
}

function editDisplay()
{# shows details from a News Feed, and preloads FeedID in a form.
	global $config;
	if(!is_numeric($_POST['FeedID']))
	{//data must be alphanumeric only	
		feedback("id passed was not a number. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
		myRedirect(THIS_PAGE);
	}


	$myID = (int)$_POST['FeedID'];  //forcibly convert to integer

	$sql = sprintf("select FeedID,CategoryID,SubCategory,Description from sm18_News_Feeds WHERE FeedID=%d",$myID);
	$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
	if(mysqli_num_rows($result) > 0)//at least one record!
	{//show results
		while ($row = mysqli_fetch_array($result))
		{//dbOut() function is a 'wrapper' designed to strip slashes, etc. of data leaving db
		     $FeedID = dbOut($row['FeedID']) . ' ' . dbOut($row['SubCategory']);
		     $CategoryID= dbOut($row['CategoryID']);
		     $SubCategory = dbOut($row['SubCategory']);
		     $Description = dbOut($row['Description']);
		}
	}else{//no records
      //feedback issue to user/developer
      feedback("No such customer. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
	  myRedirect(THIS_PAGE);
	}
	
	$config->loadhead .= '
	<script type="text/javascript" src="' . VIRTUAL_PATH . 'include/util.js"></script>
	<script type="text/javascript">
		function checkForm(thisForm)
		{//check form data for valid info
			if(empty(thisForm.CategoryID,"Please Enter News Feed Category")){return false;}
			if(empty(thisForm.SubCategory,"Please Enter News Feed SubCategory")){return false;}
			if(!isDescription(thisForm.Description,"Please Enter a Valid Description")){return false;}
			return true;//if all is passed, submit!
		}
	</script>';
	
	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>
	<h4 align="center">Update News Feed</h4>
	<p align="center">News Feed: <font color="red"><b>' . $FeedID . '</b>
	 Description: <font color="red"><b>' . $Description . '</b></font> 
	<form action="' . THIS_PAGE . '" method="post" onsubmit="return checkForm(this);">
	<table align="center">
	   <tr><td align="right">FeedID</td>
		   	<td>
		   		<input type="text" name="CategoryID" value="' .  $CategoryID . '">
		   		<font color="red"><b>*</b></font> <em>(alphanumerics & punctuation)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">Sub Category</td>
		   	<td>
		   		<input type="text" name="SubCategory" value="' .  $SubCategory. '">
		   		<font color="red"><b>*</b></font> <em>(alphanumerics & punctuation)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">Description</td>
		   	<td>
		   		<input type="text" name="Description" value="' .  $Description . '">
		   		<font color="red"><b>*</b></font> <em>(valid Description only)</em>
		   	</td>
	   </tr>
	   <input type="hidden" name="FeedID" value="' . $myID . '" />
	   <input type="hidden" name="act" value="update" />
	   <tr>
	   		<td align="center" colspan="2">
	   			<input type="submit" value="Update Info!"><em>(<font color="red"><b>*</b> required field</font>)</em>
	   		</td>
	   </tr>
	</table>    
	</form>
	<div align="center"><a href="' . THIS_PAGE . '">Exit Without Update</a></div>
	';
	@mysqli_free_result($result); //free resources
	get_footer();
	
}

function updateExecute()
{
	if(!is_numeric($_POST['FeedID']))
	{//data must be alphanumeric only	
		feedback("id passed was not a number. (error code #" . createErrorCode(THIS_PAGE,__LINE__) . ")","error");
		myRedirect(THIS_PAGE);
	}
	
	
	

	$iConn = IDB::conn();//must have DB as variable to pass to mysqli_real_escape() via iformReq()
	
	
	$redirect = THIS_PAGE; //global var used for following formReq redirection on failure

	$FeedID = iformReq('FeedID',$iConn); //calls mysqli_real_escape() internally, to check form data
	$CategoryID = strip_tags(iformReq('CategoryID',$iConn));
	$SubCategory = strip_tags(iformReq('SubCategory',$iConn));
	$Description = strip_tags(iformReq('Description',$iConn));
	
	//next check for specific issues with data
	if(!is_numeric($_POST['CategoryID'])|| !ctype_graph($_POST['SubCategory']))
	{//data must be alphanumeric or punctuation only	
		feedback("First and Last Name must contain letters, numbers or punctuation","warning");
		myRedirect(THIS_PAGE);
	}
	
	
	if(!onlyDescription($_POST['Description']))
	{//data must be alphanumeric or punctuation only	
		feedback("Data entered for Description is not valid","warning");
		myRedirect(THIS_PAGE);
	}

    //build string for SQL insert with replacement vars, %s for string, %d for digits 
    $sql = "UPDATE sm18_News_Feeds set  
    CategoryID='%s',
    SubCategory='%s',
    Description='%s'
     WHERE FeedID=%d"
     ; 
     
     
     
     
    # sprintf() allows us to filter (parameterize) form data 
	$sql = sprintf($sql,(int)$CategoryID,$SubCategory,$Description,(int)$FeedID);

	@mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	#feedback success or failure of update
	if (mysqli_affected_rows($iConn) > 0)
	{//success!  provide feedback, chance to change another!
	 feedback("Data Updated Successfully!","success");
	 
	}else{//Problem!  Provide feedback!
	 feedback("Data NOT changed!","warning");
	}
	myRedirect(THIS_PAGE);
}

