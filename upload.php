 <?php
//debug
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include Zend Gdata Libs
require_once("Zend/Gdata/ClientLogin.php");
require_once("Zend/Gdata/HttpClient.php");
require_once("Zend/Gdata/YouTube.php");
require_once("Zend/Gdata/App/MediaFileSource.php");
require_once("Zend/Gdata/App/HttpException.php");
require_once('Zend/Uri/Http.php');
?>
<style>
    div#main{
        width: 640px;
        height: 820px;
        background: url(images/background.jpg);
        color: white;
        font-family: Georgia, san-serif;
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
    
    div#formbox{
        float: left;
        background: black;
        opacity: .7;
        width: 80%;
        margin: 0 50px;
        padding: 10px;
    }
    div#formbox form table{
        color: white;
        font-family: Georgia, san-serif;
    
    }
    
    input[type=submit]{
        color: white;
        background: red;
        border: none;
        padding: 10px;
        font-weight: bold;
    }
    
     textarea.big{
        width: 300px;
        height: 100px;
    }
     
    div#top-header{
        background: url(images/top-header.jpg) no-repeat;
        height: 62px;
    }
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
<div id="main">
     <div id="top-header"></div>
     <header>
        <a href="index.php" class="left">Klub254 | </a> 
        <a href="upload.php" class="left current">Upload Song/Photo/Poem</a>
        <a href="browse.php?topfive=true" class="right"> | Top Entries</a> 
        <a href="browse.php" class="right">Vote</a>
    </header>
<?php
if(!isset($_FILES['video'])){
?>
<div id="formbox">
    <h2>How to upload your photo, song or poem.</h2>
    <h3>Steps to upload:</h3>
<form id="upload" enctype="multipart/form-data" action="upload.php" method="POST">
    <table>
    
    <tr><td>Song/Poem name: </td> <td><input size="44" name="vid_title" type="text" required="required"/> </td></tr>
    <tr><td>Full name: </td><td><input size="44" name="full_name" type="text" required="required"/></td></tr>
    <tr><td>The lyrics:</td> <td><textarea class="big" name="lyrics" type="text" required="required"></textarea></td></tr>
    <tr><td>Please select a photo:</td> <td><input name="photo" type="file" required="required"/></td></tr>
    <tr><td>Please select a video:</td> <td><input name="video" type="file" required="required"/></td></tr>
    <tr><td></td><td><input type="submit" value="Upload Item" /></td></tr>
    </table>
	
</form>

<div id="intel" style="height: 50px; width: 80%; display:none;">Your video upload is ongoing. This might take several minutes. Please be patient. Meanwhile, you can tell your friends about Klub254...</div>

<script type="text/javascript">
$('#upload').submit(function() {
  $('#intel').show();
});
</script>
</div>
    
<?php
}else{
//yt account info
$yt_user = 'klub254'; //youtube username or gmail account
$yt_pw = 'nc4rm254'; //account password
$yt_source = 'Club254'; //name of application (can be anything)

//video path
$video_url = $_FILES["video"]["tmp_name"];
$video_size = $_FILES["video"]["size"] /1000;
$video_type = $_FILES["video"]["type"];
if($video_size > 3500){
    ?>
     <div id="formbox">
        <p>File size exceeds limit! Max video size 3500.</p>
        
       
    </div>
    <?php
}else{
//yt dev key
$yt_api_key = 'AI39si770_e2KeT2hVrIcrtxF21nuJWNsiE5MCbOPSQ2w5c1afc73x46m_r5lJLf8XJezdglp4HbJ09LVwSAwKSATNgkwd1lQw'; //your youtube developer key

//login in to YT
$authenticationURL= 'https://www.google.com/youtube/accounts/ClientLogin';
$httpClient = Zend_Gdata_ClientLogin::getHttpClient(
							              $username = $yt_user,
							              $password = $yt_pw,
							              $service = 'youtube',
							              $client = null,
							              $source = $yt_source, // a short string identifying your application
							              $loginToken = null,
							              $loginCaptcha = null,
							              $authenticationURL);

$yt = new Zend_Gdata_YouTube($httpClient, $yt_source, NULL, $yt_api_key);

$myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

$filesource = $yt->newMediaFileSource($video_url);
$filesource->setContentType('video/quicktime'); //make sure to set the proper content type.
$filesource->setSlug($_FILES["video"]["tmp_name"]);

$myVideoEntry->setMediaSource($filesource);

$myVideoEntry->setVideoTitle($_POST['vid_title']);
$myVideoEntry->setVideoDescription('Song by: '.$_POST['full_name'].' Lyrics: '.$_POST['lyrics']);
// Note that category must be a valid YouTube category !
$myVideoEntry->setVideoCategory('People');

// Set keywords, note that this must be a comma separated string
// and that each keyword cannot contain whitespace
$myVideoEntry->SetVideoTags('Airtel, Club254, Kenya, music');

// Upload URI for the currently authenticated user

$uploadUrl = "http://uploads.gdata.youtube.com/feeds/users/$yt_user/uploads";

// Try to upload the video, catching a Zend_Gdata_App_HttpException
// if availableor just a regular Zend_Gdata_App_Exception

try {
    $newEntry = $yt->insertEntry($myVideoEntry,
                                 $uploadUrl,
                                 'Zend_Gdata_YouTube_VideoEntry');
    
    ?>
    <div id="formbox">
        <p>Your entry was uploaded successfully! Ask/tell your friends to vote for your entry.</p>
        
       
    </div>
<?php
    } catch (Zend_Gdata_App_HttpException $httpException) {
        ?>
    <div id="formbox">
   <?php echo $httpException->getRawResponseBody(); ?>
    </div>
    <?php
} catch (Zend_Gdata_App_Exception $e) { ?>
    <div id="formbox">
   <?php echo $e->getMessage(); ?>
        </div>
    <?php
}
}

//this outputs a ton of garbage. not sure what to do with it yet.
#echo "<pre>" . var_dump($newEntry) . "</pre>";
}
?>
</div>