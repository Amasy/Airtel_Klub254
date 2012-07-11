<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the YouTube data API.  Utilizes the Zend Framework
 * Zend_Gdata component to communicate with the YouTube data API.
 *
 * Requires the Zend Framework Zend_Gdata component and PHP >= 5.1.4
 *
 * This sample is run from within a web browser.  These files are required:
 * index.php - the main logic, which interfaces with the YouTube API
 * interface.html - the HTML to represent the web UI
 * web_browser.css - the CSS to define the interface style
 * web_browser.js - the JavaScript used to provide the video list AJAX interface
 *
 * NOTE: If using in production, some additional precautions with regards
 * to filtering the input data should be used.  This code is designed only
 * for demonstration purposes.
 */

/**
 * @see Zend_Loader
 */
//require_once 'Zend/Loader.php';
require_once("Zend/Gdata/YouTube.php");
/**
 * @see Zend_Gdata_YouTube
 */
//Zend_Loader::loadClass('Zend_Gdata_YouTube');

/**
 * Finds the URL for the flash representation of the specified video
 *
 * @param  Zend_Gdata_YouTube_VideoEntry $entry The video entry
 * @return string|null The URL or null, if the URL is not found
 */
function findFlashUrl($entry)
{
    foreach ($entry->mediaGroup->content as $content) {
        if ($content->type === 'application/x-shockwave-flash') {
            return $content->url;
        }
    }
    return null;
}

/**
 * Returns a feed of top rated videos for the specified user
 *
 * @param  string $user The username
 * @return Zend_Gdata_YouTube_VideoFeed The feed of top rated videos
 */
function getTopRatedVideosByUser($user)
{
    $userVideosUrl = 'http://gdata.youtube.com/feeds/users/' .
                     $user . '/uploads';
    $yt = new Zend_Gdata_YouTube();
    $ytQuery = $yt->newVideoQuery($userVideosUrl);
    // order by the rating of the videos
    $ytQuery->setOrderBy('rating');
    // retrieve a maximum of 5 videos
    $ytQuery->setMaxResults(5);
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);
    return $yt->getVideoFeed($ytQuery);
}

/*Return a feed of the specified user's uploads
 * 
 * @param string $user The username
 * @return Zend_Gdata_YouTube_VideoFeed The feed of videos by a user
 * 
 */

function getUserUploadVideoFeed($user, $offset=0)
{
    $userVideosUrl = 'http://gdata.youtube.com/feeds/users/'.$user.'/uploads';
    $yt = new Zend_Gdata_Youtube();
    $ytQuery = $yt->newVideoQuery($userVideosUrl);
    $ytQuery->setMaxResults(9);
    $ytQuery->setStartIndex($offset);
    $ytQuery->setFormat(5);
    
    return $yt->getVideoFeed($ytQuery);
}

/**
 * Returns a feed of videos related to the specified video
 *
 * @param  string $videoId The video
 * @return Zend_Gdata_YouTube_VideoFeed The feed of related videos
 */
function getRelatedVideos($videoId)
{
    $yt = new Zend_Gdata_YouTube();
    $ytQuery = $yt->newVideoQuery();
    // show videos related to the specified video
    $ytQuery->setFeedType('related', $videoId);
    // order videos by rating
    $ytQuery->setOrderBy('rating');
    // retrieve a maximum of 5 videos
    $ytQuery->setMaxResults(5);
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);
    return $yt->getVideoFeed($ytQuery);
}

/**
 * Echo img tags for the first thumbnail representing each video in the
 * specified video feed.  Upon clicking the thumbnails, the video should
 * be presented.
 *
 * @param  Zend_Gdata_YouTube_VideoFeed $feed The video feed
 * @return void
 */
function echoThumbnails($feed)
{
    foreach ($feed as $entry) {
        $videoId = $entry->getVideoId();
        echo '<img src="' . $entry->mediaGroup->thumbnail[0]->url . '" ';
        echo 'width="80" height="72" onclick="ytvbp.presentVideo(\'' . $videoId . '\')">';
    }
}

function echoTop5(){
    $con = mysql_connect("localhost","ishuah","sky10711");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("video_vote", $con) or die("Could not select database");

       $feed = mysql_query("SELECT * from count ORDER BY votes DESC LIMIT 5");
      
       
       if(mysql_num_rows($feed) == 0){ ?>
            <div id="formbox">
        <p> No entries have been voted for yet, vote for one <a href="browse.php">here</a></p>
    </div>
       <?php }else{ ?>
             <div id="formbox">
			 <div id="fb-root"></div>
			<script>
			window.fbAsyncInit = function() {
			FB.init({appId: '383453325045816', status: true, cookie: true,
			xfbml: true});
			};
			(function() {
			var e = document.createElement('script'); e.async = true;
			e.src = document.location.protocol +
			'//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
			}());
			</script>
     <?php
	 $count = 1;
           while($res = mysql_fetch_array($feed)){
               
            $yt = new Zend_Gdata_YouTube();
            $entryvid = $yt->getVideoEntry($res[0]);
            $videoTitle = $entryvid->mediaGroup->title;
            $thumbnailUrl = $entryvid->mediaGroup->thumbnail[0]->url;
              ?>
                 <div class="video-entry" >
           <img width=100 height=100 src="<?php echo $thumbnailUrl ?>" />
                 <a href="browse.php?showVideo=true&videoId=<?php echo $res[0] ?>"><?php echo $videoTitle ?></a>
				 <div id="share_group">
				 <?php if(isset($_COOKIE['vote'])){ ?>
            <?php if($res[0] == $_COOKIE['videoID']){ ?>
            <form action="vote.php" method="POST">
            <input type="hidden" name="videoID" value="<?php echo $res[0] ?>"/>
            <input type="hidden" name="action" value="unvote"/>
            <input  type="submit" name="do" value="Undo Vote"/>
            </form>  
            <?php }else{ ?>
        
            <?php }?>
            <?php }else{ ?>
            <form action="vote.php" method="POST">
            <input type="hidden" name="videoID" value="<?php echo $res[0] ?>"/>
            <input type="hidden" name="action" value="vote"/>
            <input id="vote_<?php echo $count; ?>" type="submit" name="do" value="Vote"/>
            </form>
            <?php } ?>
				 <img id="share_button_<?php echo $count ?>" src="images/share_button.png"/>
				 <script type="text/javascript">
	$(document).ready(function(){
	$('#share_button_<?php echo $count ?>').live('click', function(e){
	e.preventDefault();
	FB.ui(
	{
	method: 'feed',
	name: '<?php echo $videoTitle; ?>',
	link: 'https://apps.facebook.com/testklubapp/',
	picture: '<?php echo $thumbnailUrl; ?>',
	caption: 'check out Klub254',
	description: 'Upload your videos and get voted up!',
	message: ''
	});
	});
	});
	</script>
					 <script type="text/javascript">
	$(document).ready(function(){
	$('#vote_<?php echo $count ?>').live('click', function(e){
	
	FB.ui(
	{
	method: 'feed',
	name: '<?php echo $videoTitle; ?>',
	link: 'https://apps.facebook.com/testklubapp/',
	picture: '<?php echo $thumbnailUrl; ?>',
	caption: 'check out Klub254',
	description: 'Upload your videos and get voted up!',
	message: 'I voted for the video <?php echo $videoTitle; ?>'
	});
	});
	});
	</script>
	
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://apps.facebook.com/testklubapp/" data-text="Checkout klub254" data-via="Amasy" data-size="large" data-count="none">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
                 </div>
           <?php
		   $count++;
           }
          
    ?>
             </div>
           <?php
       }
}

/**
 * Echo the video embed code, related videos and videos owned by the same user
 * as the specified videoId.
 *
 * @param  string $videoId The video
 * @return void
 */
function echoVideoPlayer($videoId)
{
    $yt = new Zend_Gdata_YouTube();

    $entry = $yt->getVideoEntry($videoId);
    $videoTitle = $entry->mediaGroup->title;
    $videoUrl = findFlashUrl($entry);
    $relatedVideoFeed = getRelatedVideos($entry->getVideoId());
    $topRatedFeed = getTopRatedVideosByUser($entry->author[0]->name);

    ?>
    <div id="formbox">
    <h2><?php echo $videoTitle ?></h2><br />
      <?php if(isset($_COOKIE['vote'])){ ?>
            <?php if($videoId == $_COOKIE['videoID']){ ?>
            <form action="vote.php" method="POST">
            <input type="hidden" name="videoID" value="<?php echo $videoId ?>"/>
            <input type="hidden" name="action" value="unvote"/>
            <input type="submit" name="do" value="Undo Vote"/>
            </form>  
            <?php }else{ ?>
        
            <?php }?>
            <?php }else{ ?>
            <form action="vote.php" method="POST">
            <input type="hidden" name="videoID" value="<?php echo $videoId ?>"/>
            <input type="hidden" name="action" value="vote"/>
            <input  type="submit" name="do" value="Vote"/>
            </form>
            <?php } ?>
			<div id="fb-root"></div>
			<script>
			window.fbAsyncInit = function() {
			FB.init({appId: '383453325045816', status: true, cookie: true,
			xfbml: true});
			};
			(function() {
			var e = document.createElement('script'); e.async = true;
			e.src = document.location.protocol +
			'//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
			}());
			</script>
							 <div id="share_group">
				 <img id="share_button" src="images/share_button.png"/>
				 <script type="text/javascript">
	$(document).ready(function(){
	$('#share_button').live('click', function(e){
	e.preventDefault();
	FB.ui(
	{
	method: 'feed',
	name: '<?php echo $videoTitle; ?>',
	link: 'https://apps.facebook.com/testklubapp/',
	picture: '<?php echo $thumbnailUrl; ?>',
	caption: 'check out Klub254',
	description: 'Upload your videos and get voted up!',
	message: ''
	});
	});
	});
	</script>
	
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://apps.facebook.com/testklubapp/" data-text="Checkout klub254" data-via="Amasy" data-size="large" data-count="none">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
    <object width="425" height="350">
      <param name="movie" value="<?php echo $videoUrl ?>&autoplay=1"></param>
      <param name="wmode" value="transparent"></param>
      <embed src="<?php echo $videoUrl ?>&autoplay=1" type="application/x-shockwave-flash" wmode="transparent"
        width=425" height="350"></embed>
    </object>
<?php
    echo '<br />';
    echoVideoMetadata($entry);
    echo '</div>';
}

/**
 * Echo video metadata
 *
 * @param  Zend_Gdata_YouTube_VideoEntry $entry The video entry
 * @return void
 */
function echoVideoMetadata($entry)
{
    $title = $entry->mediaGroup->title;
    $description = $entry->mediaGroup->description;
    $authorUsername = $entry->author[0]->name;
    $authorUrl = 'http://www.youtube.com/profile?user=' . $authorUsername;
    $tags = $entry->mediaGroup->keywords;
    $duration = $entry->mediaGroup->duration->seconds;
    $watchPage = $entry->mediaGroup->player[0]->url;
    $viewCount = $entry->statistics->viewCount;
    $rating = $entry->rating->average;
    $numRaters = $entry->rating->numRaters;
    $flashUrl = findFlashUrl($entry);
    print <<<END
    <b>Title:</b> ${title}<br />
    <b>Description:</b> ${description}<br />
    <b>Author:</b> <a href="${authorUrl}">${authorUsername}</a><br />
    <b>Tags:</b> ${tags}<br />
    <b>Duration:</b> ${duration} seconds<br />
    <b>View count:</b> ${viewCount}<br />
    <b>Rating:</b> ${rating} (${numRaters} ratings)<br />
    <b>Flash:</b> <a href="${flashUrl}">${flashUrl}</a><br />
    <b>Watch page:</b> <a href="${watchPage}">${watchPage}</a> <br />
END;
}

/**
 * Echo the list of videos in the specified feed.
 *
 * @param  Zend_Gdata_YouTube_VideoFeed $feed The video feed
 * @return void
 */
function echoVideoList($feed)
{
    
    echo '<div class="videoList">';
    ?>
	<div id="fb-root"></div>
			<script>
			window.fbAsyncInit = function() {
			FB.init({appId: '383453325045816', status: true, cookie: true,
			xfbml: true});
			};
			(function() {
			var e = document.createElement('script'); e.async = true;
			e.src = document.location.protocol +
			'//connect.facebook.net/en_US/all.js';
			document.getElementById('fb-root').appendChild(e);
			}());
			</script>
	<?php
	$count = 1;
    foreach ($feed as $entry) {
        $videoId = $entry->getVideoId();
        $thumbnailUrl = $entry->mediaGroup->thumbnail[0]->url;
        $videoTitle = $entry->mediaGroup->title;
        $videoDescription = $entry->mediaGroup->description;
        
       
        
        ?>
		
        <div class="video">
        <img width=100 height=100 src="<?php echo $thumbnailUrl ?>" />
        <a href="browse.php?showVideo=true&videoId=<?php echo $videoId ?>"><?php echo $videoTitle ?></a>
        
        
            <?php if(isset($_COOKIE['vote'])){ ?>
            <?php if($videoId == $_COOKIE['videoID']){ ?>
            <form action="vote.php" method="POST">
            <input type="hidden" name="videoID" value="<?php echo $videoId ?>"/>
            <input type="hidden" name="action" value="unvote"/>
            <input type="submit" name="do" value="Undo Vote"/>
            </form>  
            <?php }else{ ?>
        
            <?php }?>
            <?php }else{ ?>
            <form action="vote.php" method="POST">
            <input type="hidden" name="videoID" value="<?php echo $videoId ?>"/>
            <input type="hidden" name="action" value="vote"/>
            <input id="vote_<?php echo $count; ?>"  type="submit" name="do" value="Vote"/>
            </form>
            <?php } ?>
		<script type="text/javascript">
	$(document).ready(function(){
	$('#vote_<?php echo $count ?>').live('click', function(e){
	
	FB.ui(
	{
	method: 'feed',
	name: '<?php echo $videoTitle; ?>',
	link: 'https://apps.facebook.com/testklubapp/',
	picture: '<?php echo $thumbnailUrl; ?>',
	caption: 'check out Klub254',
	description: 'Upload your videos and get voted up!',
	message: 'I voted for the video <?php echo $videoTitle; ?>'
	});
	});
	});
	</script>

        </div>
<?php
	$count++;
    }
    echo '</div>';
}

/*
 * The main controller logic of the YouTube video browser demonstration app.
 */
    
    ?>

<style>
    div#main{
        width: 640px;
        min-height: 820px;
        background: url(images/background.jpg);
        float: left;
        color: white;
    }
    
    div#main header {
        width: 100%;
        height: 50px;
       
    }
    
    div#main header a{
        color: white;
        font-size: 20px;
        font-weight: bold;
        text-decoration: none;
        margin: 10px 10px;
        text-shadow: 0.1em 0.1em #333;
        font-family: Georgia, san-serif;
    }
    
    div#main header a:hover{
        color: red;
    }
    
    div#main header a.right{
        float: right;
    }
    
    div#main header a.left{
        float: left;
    }
    
    div#main header a.current{
        color: red;
    }
    
    div#main header a.current:hover{
        color: #900;
    }
    
     
    div#top-header{
        background: url(images/top-header.jpg) no-repeat;
        height: 62px;
    }
    
    div.video{
        float: left;
        height: 150px;
        width: 150px;
        margin: 20px;
        padding: 10px;
    }
    
    div.video img{
        float: left;
    }
    
    div.video a{
        float: left;
        text-decoration: none;
        color: white;
		width: 100%;
    }
    div.videoList{
        float:left;
    }
    form.navi{
        float:left;
        margin-top: 5px;
        margin-left:30px;
    }
     div#formbox{
        float: left;
        background: black;
        opacity: .7;
        width: 80%;
        margin: 0 50px;
        padding: 10px;
    }
    div#formbox {
        color: white;
        font-family: Georgia, san-serif;
    
    }
    
    div#formbox object embed{
        margin: 0 15px;
    }
    
    input[type="submit"] {
            color: white;
            background: red;
            border: none;
            padding: 10px;
            font-weight: bold;
			float:left;
            margin-top: 16px;
            height: 28px;
        }
        
        div.video-entry {
            float: left;
            width: 100%;
        }
        div.video-entry img{
            float: left;
        }
        
        div.video-entry a{
           float: left;
            color: white;
            text-decoration: none;
            margin: 12px;
        }
		div#share_group{
		float: left;
		width: 60%;
		margin: 0 10px;
		}
		div#share_group img {cursor: pointer; margin: 0 5px;}
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
<div id="main">
     <div id="top-header"></div>
     <?php $current_1 = ""; $current_2 = "";?>
      <?if(isset($_GET['topfive'])) { $current_1 = "current"; }else{ $current_2 = "current"; } ?>
 <header>
        <a href="index.php" class="left">Klub254 |</a> 
        <a href="upload.php" class="left">Upload Song/Photo/Poem</a>
        <a href="browse.php?topfive=true" class="right <?php echo $current_1 ?>">  | Top Entries</a>
        <a href="browse.php" class="right <?php echo $current_2 ?>">Vote</a>
    </header>
     
<?php 


$user = 'eGikunda';
    
    if(isset($_GET['showVideo'])){
        
        $videoId = $_GET['videoId'];
        echoVideoPlayer($videoId);
    }elseif(isset($_GET['topfive'])){
        echoTop5();
    }else{
    if(isset($_POST['offset']))
    {
        if($_POST['nav']=='Previous'){
        $offset=$_POST['offset']-10;
        }
        if($_POST['nav']=='Next'){
        $offset=$_POST['offset']+10;
        }
    }else{
        $offset=0;
    }
    $feed = getUserUploadVideoFeed($user, $offset);
    echoVideoList($feed); 
    
    ?>
     <form action="browse.php" method="POST" class="navi">
         <input type="hidden" name="offset" value="<?php echo $offset; ?>"/>
         <?php if($offset != 0){?>
        <input type="submit" class="button" value="Previous" name="nav" style="margin-top:30px;"/>
        <?php }?>
        <input type="submit" class="button" value="Next" name="nav" style="margin-top: 30px;"/>
     </form>
     <?php } ?>
</div>