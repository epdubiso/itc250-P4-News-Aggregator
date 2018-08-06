<?php
/**
 * newsfeed_view.php along with feed.php provides a sample web application
 *
 * @package NewsFeed
 * @author Zack <zacharyforreal@gmail.com>
 * @author Jen Villacis <jennifer.villacis@seattlecentral.ed>u
 * @author Eden Dubiso <eden.dubiso@seattlecentral.edu>
 * @version 0.1 2018/08/05
 * @link http://www.edendu.com/
 * @license https://www.apache.org/licenses/LICENSE-2.0
 * @see newsfeed_view.php
 * @see Pager.php 
 * @todo none
 */
 
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials
spl_autoload_register('MyAutoLoader::NamespaceLoader');//required to load SurveySez namespace objects
$config->metaRobots = 'no index, no follow';#never index feed pages
#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'News Feed Sub-Categories';


# check variable of item passed in - if invalid data, forcibly redirect back to feeds/index.php page
if(isset($_GET['id']) && (int)$_GET['id'] > 0){#proper data must be on querystring
	 $myID = (int)$_GET['id']; #Convert to integer, will equate to zero if fails
}else{
	myRedirect(VIRTUAL_PATH . "feeds/index.php");
}

?>
<h3 align="center">Sub Categories</h3>
<?php

$sql = "select * from " . PREFIX . "News_Feeds where CategoryID=" . $myID;

$prev = '<i class="fa fa-chevron-circle-left"></i>';
$next = '<i class="fa fa-chevron-circle-right"></i>';

# Create instance of new 'pager' class
$myPager = new Pager(10,'',$prev,$next,'');
$sql = $myPager->loadSQL($sql);  #load SQL, add offset
//$sqlRSS = "select Description from sm18_News_Feeds where FeedID=" . $feedID;
# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

if(mysqli_num_rows($result) > 0)
{#records exist - process
	if($myPager->showTotal()===1){$item = "Sub category";}else{$item = "Sub categories";}  //deal with plural
    echo '<div align="center">We have ' . $myPager->showTotal() . ' ' . $item . '!</div>';
    
    echo '
    
        <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">Sub Categories</th>
          <th scope="col">Description</th>
          <th scope="col">Date Created</th>

        </tr>
      </thead>
         <tbody>
    
    ';
    
	while($row = mysqli_fetch_assoc($result))
	{# process each row
        
        echo '
                 <tr>
              <td><a href="' . VIRTUAL_PATH . 'feeds/feed.php?id=' . (int)$row['FeedID'] . '">' . dbOut($row['SubCategory']) . '</a></td>
              <td>' . dbOut($row['Description']) . '</td>
              <td>' . dbOut($row['DateAdded']) . '</td>
            </tr>
            
            
        ';
         /*echo '<div align="center"><a href="' . VIRTUAL_PATH . 'surveys/survey_view.php?id=' . (int)$row['SurveyID'] . '">' . dbOut($row['Title']) . '</a>';
         echo '</div>';
	*/
    }
    echo '
             </tbody>
        </table>

    ';
	echo $myPager->showNAV(); # show paging nav, only if enough records	 
}else{#no records
    echo "<div align=center>There are currently no sub-categories.</div>";	
}
@mysqli_free_result($result);

get_footer(); #defaults to theme footer or footer_inc.php


