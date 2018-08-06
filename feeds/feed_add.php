<?php
/**
 * feed_add.php 
 *
 * @package NewsFeed
 * @author Zack <zacharyforreal@gmail.com>
 * @author Jen Villacis <jennifer.villacis@seattlecentral.ed>u
 * @author Eden Dubiso <eden.dubiso@seattlecentral.edu>
 * @version 0.1 2018/08/05
 * @link http://www.edendu.com/
 * @license https://www.apache.org/licenses/LICENSE-2.0
 * @see 
 * @see Pager.php 
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
 

//END CONFIG AREA ----------------------------------------------------------

# Read the value of 'action' whether it is passed via $_POST or $_GET with $_REQUEST
if(isset($_REQUEST['act'])){$myAction = (trim($_REQUEST['act']));}else{$myAction = "";}

switch ($myAction) 
{//check 'act' for type of process
	case "add": //2) Form for adding new feeds data
	 	addFeed();
	 	break;
	case "insert": //3) Insert new feeds data
		insertExecute();
		break; 
	default: //1)Show existing feeds
	 	showFeeds();
}

function showFeeds()
{//Select Feed
	global $config;
	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>';

	$sql = "select FeedID,CategoryID,SubCategory,Description from sm18_Feeds";
	$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));
	if (mysqli_num_rows($result) > 0)//at least one record!
	{//show results
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
					<td>'	
				     . (int)$row['FeedID'] . '</td>
				    <td>' . dbOut($row['CategoryID']) . '</td>
				    <td>' . dbOut($row['SubCategory']) . '</td>
				    <td>' . dbOut($row['Description']) . '</td>
				</tr>
				';
		}
		echo '</table>';
	}else{//no records
      echo '<div align="center"><h3>Currently No Feeds in Database.</h3></div>';
	}
	echo '<div align="center"><a href="' . THIS_PAGE . '?act=add">ADD FEED</a></div>';
	@mysqli_free_result($result); //free resources
	get_footer();
}

function addFeed()
{# shows details from a single customer, and preloads their first name in a form.
	global $config;
	$config->loadhead .= '
	<script type="text/javascript" src="' . VIRTUAL_PATH . 'include/util.js"></script>
	<script type="text/javascript">
		function checkFeed(thisFeed)
		{//check feed data for valid info
			if(empty(thisFeed.FeedID,"Please Enter Feed\'s FeedID")){return false;}
			if(empty(thisFeed.CategoryID,"Please Enter Feed\'s CategoryID")){return false;}
			if(empty(thisFeed.SubCategory,"Please Enter Feed\'s SubCategory")){return false;}
			if(empty(thisFeed.Description,"Please Enter Feed\'s Description")){return false;}
			return true;//if all is passed, submit!
		}
	</script>';
	
	get_header();
	echo '<h3 align="center">' . smartTitle() . '</h3>
	<h4 align="center">Add Feed</h4>
	<form action="' . THIS_PAGE . '" method="post" onsubmit="return checkFeed(this);">
	<table align="center">
		<tr><td align="right">FeedID</td>
		   	<td>
		   		<input type="number" name="FeedID" />
		   		<font color="red"><b>*</b></font> <em>(Number after the last Feed)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">CategoryID</td>
		   	<td>
		   		<input type="number" name="CategoryID" />
		   		<font color="red"><b>*</b></font> <em>(1-Technology, 2- Music, 3- Sports)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">SubCategory</td>
		   	<td>
		   		<input type="text" name="SubCategory" />
		   		<font color="red"><b>*</b></font> <em>(Technology, Music, or Sports)</em>
		   	</td>
	   </tr>
	   <tr><td align="right">Description</td>
		   	<td>
		   		<input type="text" name="Description" />
		   		<font color="red"><b>*</b></font> <em>(Feed RSS link)</em>
		   	</td>
	   </tr>
	   <input type="hidden" name="act" value="insert" />
	   <tr>
	   		<td align="center" colspan="2">
	   			<input type="submit" value="Add Feed!"><em>(<font color="red"><b>*</b> required field</font>)</em>
	   		</td>
	   </tr>
	</table>    
	</form>
	<div align="center"><a href="' . THIS_PAGE . '">Exit Without Add</a></div>
	';
	get_footer();
	
}

function insertExecute()
{
	$iConn = IDB::conn();//must have DB as variable to pass to mysqli_real_escape() via iformReq()
	
	$redirect = THIS_PAGE; //global var used for following formReq redirection on failure
	$FeedID = strip_tags(iformReq('FeedID',$iConn));
	$CategoryID = strip_tags(iformReq('CategoryID',$iConn));
	$SubCategory = strip_tags(iformReq('SubCategory',$iConn));
	$Description = strip_tags(iformReq('Description',$iConn));
	
	//next check for specific issues with data
	if(!ctype_graph($_POST['FeedID'])||
	!ctype_graph($_POST['CategoryID'])|| !ctype_graph($_POST['SubCategory'])||
	!ctype_graph($_POST['Description']))
	{//data must be alphanumeric or punctuation only	
		feedback("Something went wrong, try again.");
		myRedirect(THIS_PAGE);
	}
	
	/*
	if(!onlyEmail($_POST['Email']))
	{//data must be alphanumeric or punctuation only	
		feedback("Data entered for email is not valid");
		myRedirect(THIS_PAGE);
	}*/

    //build string for SQL insert with replacement vars, %s for string, %d for digits 
    $sql = "INSERT INTO sm18_Feeds (FeedID, CategoryID, SubCategory, Description) VALUES ('%s','%s','%s', '%s')"; 

    # sprintf() allows us to filter (parameterize) form data 
	$sql = sprintf($sql,$FeedID,$CategoryID,$SubCategory,$Description);

	@mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));
	#feedback success or failure of update
	if (mysqli_affected_rows($iConn) > 0)
	{//success!  provide feedback, chance to change another!
		feedback("Feed Added Successfully!","notice");
	}else{//Problem!  Provide feedback!
		feedback("Feed NOT added!");
	}
	myRedirect(THIS_PAGE);
}
