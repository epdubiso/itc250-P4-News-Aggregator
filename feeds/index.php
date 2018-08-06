<?php
/**
 * index.php along with newsfeed_view.php provides a sample web application
 *
 * @package NewsFeed
 * @author Zack <zacharyforreal@gmail.com>
 * @author Jen Villacis <jennifer.villacis@seattlecentral.ed>u
 * @author Eden Dubiso <eden.dubiso@seattlecentral.edu>
 * @version 0.1 2018/07/18
 * @link http://www.edendu.com/
 * @license https://www.apache.org/licenses/LICENSE-2.0
 * @see newsfeed_view.php
 * @see Pager.php 
 * @todo none
 */

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials 
 
# SQL statement
$sql= "select * from " . PREFIX . "News_Feeds_Categories";

#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'News Feed made with love & PHP in Seattle';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Seattle Central\'s ITC250 Class News Feeds are made with pure PHP! ' . $config->metaDescription;
$config->metaKeywords = 'RSS,PHP,Fun,News,Big Data,Regular Expressions,'. $config->metaKeywords;

//adds font awesome icons for arrows on pager
$config->loadhead .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

# END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center">Categories</h3>

<?php
#reference images for pager
//$prev = '<img src="' . $config->virtual_path . '/images/arrow_prev.gif" border="0" />';
//$next = '<img src="' . $config->virtual_path . '/images/arrow_next.gif" border="0" />';

#images in this case are from font awesome
$prev = '<i class="fa fa-chevron-circle-left"></i>';
$next = '<i class="fa fa-chevron-circle-right"></i>';

# Create instance of new 'pager' class
$myPager = new Pager(10,'',$prev,$next,'');
$sql = $myPager->loadSQL($sql);  #load SQL, add offset

# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

if(mysqli_num_rows($result) > 0)
{#records exist - process
	if($myPager->showTotal()===1){$item = "category";}else{$item = "categories";}  //deal with plural
    echo '<div align="center">We have ' . $myPager->showTotal() . ' ' . $item . '!</div>';
    
    echo '
    
        <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">Categories</th>
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
              <td><a href="' . VIRTUAL_PATH . 'feeds/newsfeed_view.php?id=' . (int)$row['CategoryID'] . '">' . dbOut($row['Category']) . '</a></td>
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
    echo "<div align=center>There are currently no feeds.</div>";	
}
@mysqli_free_result($result);


	echo"
		
        <p><a  href='feed_add.php'>ADD NEWS FEED</a></p>";
	

get_footer(); #defaults to theme footer or footer_inc.php
?>
