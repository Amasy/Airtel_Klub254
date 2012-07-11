<?php
$con = mysql_connect("localhost","ishuah","sky10711");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("video_vote", $con) or die("Could not select database");


function vote($videoID){
    
    if(isset($_COOKIE['vote'])){
        return FALSE;
        //echo "You already voted";
    }else{
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $expire = time()+60*60*24*30;
    if(mysql_query("INSERT INTO votes(videoID, IPAddress) VALUES('$videoID', '$ipaddress' )")){
        
        setcookie("vote", mysql_insert_id(), $expire);
        setcookie("videoID", $videoID, $expire);
        
        $res = mysql_query("SELECT votes from count where videoID='".$videoID."'");
       $res = mysql_fetch_array($res);
       if(!$res){
           $vote = 1;
           mysql_query("INSERT INTO count VALUES('$videoID', '$vote')") or die(mysql_error());
           //echo "Done";
           return TRUE;
       }else{
         $vote =  $res[0]+1;
           mysql_query("UPDATE count SET votes=".$vote." where videoID='".$videoID."'") or die(mysql_error());
           //echo "Done 2";
           return TRUE;
       }
    }else{
        return FALSE;
    }
    }
    return FALSE;
}

function unvote($videoID){
    if(isset($_COOKIE['vote'])){
        $entryID = $_COOKIE['vote'];
    
    if(mysql_query("Delete from votes where id='".$entryID."'")){
        
        setcookie("vote", "", time()-3600);
        setcookie("videoID", "", time()-3600);
        $res = mysql_query("SELECT votes from count where videoID='".$videoID."'");
       $res = mysql_fetch_array($res);
       if(!$res){
           $vote = 0;
           mysql_query("INSERT INTO count VALUES('$videoID', '$vote')") or die(mysql_error());
          // echo "Done";
           return TRUE;
       }else{
         $vote =  $res[0]-1;
           mysql_query("UPDATE count SET votes=".$vote." where videoID='".$videoID."'") or die(mysql_error());
           //echo "Done 2";
           return TRUE;
       }
    }else{
        return FALSE;
    }
    }else{
        //echo "You haven't voted";
        return FALSE;
    }
     return FALSE;
}

if(isset($_POST['action'])){
    if($_POST['action']=='vote'){
       vote($_POST['videoID']);
      header('Location: '.$_SERVER['HTTP_REFERER']);
    }elseif($_POST['action'] == 'unvote'){
        unvote($_POST['videoID']);
      header('Location: '.$_SERVER['HTTP_REFERER']);
    }
}


?>


