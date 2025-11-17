<?php
session_start();

//get view count
$data = json_decode(file_get_contents('data.json'), true);
$data['viewcount'] += 1; 
$viewcount = $data['viewcount'];

//get user ip
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
$ipdata = $data['ips'];
if (!in_array($ip, $ipdata)) {
    $data['ips'][] = $ip;
}

//save infos
file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT));

//veriables
$showalert = false;
$alertmessage = "No alert set<br>"; 
$selecetedvideo = "";
$videodirectory = "videos/";

//select video
$videofiles = scandir($videodirectory);
foreach ($videofiles as $video) {
if($video != "" && $video != ".." && $video != "."){
    //set all videos
$allvideos[] = $video;//names
}
}

//count videos
$videocount = count($allvideos);

//create a playlist if not already set
if(!isset($_SESSION['playlist'])){
//create a playlist
for ($i=-1; $i <= $videocount; $i++) { 
    $playlist[] = $i;
}
//randomize the playlist;
shuffle($playlist);
//set the playlist session and counter to count videos
$_SESSION['playlist'] = $playlist;
$_SESSION['playlistnummer'] = 0;
$_SESSION['playlistdone'] = false;

}

if(!isset($_SESSION['playlist']) || $_SESSION['playlistdone'] == true){

//choose a random video
$choosenVideo = random_int(0, $videocount - 1);
if (isset($_SESSION['lastvideo'])) {
    if ($choosenVideo == $_SESSION['lastvideo']) {
        if($choosenVideo == $videocount){
            $choosenVideo -= 1;
        }elseif ($choosenVideo == 2) {
            $choosenVideo++;
        }else{
            $choosenVideo++;
        }
    }
}

}else{
    $playlistnummer = $_SESSION['playlistnummer'];//get playlist nummer
    //choose the right nummer from the playlist with the right order
    $choosenVideo = $_SESSION['playlist'][$playlistnummer];

    //go to the next nummer for the next time
    $playlistnummer++;
    $_SESSION['playlistnummer'] = $playlistnummer;

    //if at the count is at the end so all videos are seen set the done session
    if ($playlistnummer > $videocount) {
        $_SESSION['playlistdone'] = true;

    }

    $playlist = $_SESSION['playlist'];


}



//if there is a special video request made by url
if (isset($_GET['video']) && !empty($_GET['video'])) {
    $selecetedvideo = $_GET['video'];
}else{
    //set final video to watch
    $selecetedvideo = $allvideos[$choosenVideo];
}

//set video for the new users user here for the first time
if(!isset($_SESSION['lastvideo'])){
$selecetedvideo = 'fiets.mp4';
}

//set last choosen video so you dont get the same video again when refreshed
$_SESSION['lastvideo'] = $choosenVideo;


//alert box
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Yavuz Semih - Portfolio</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">

    <style>
        @font-face {
    font-family: 'Lato';      
    src: url('fonts/Lato-Black.ttf') format('truetype');
    }
    </style>

    <script src="https://kit.fontawesome.com/9653eb0bfc.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/x-icon" href="/assets/logo.png">
    <script src="js/main.js" defer></script>
</head>
<body>
    <!--video background-->
    <video autoplay muted loop playsinline class="bgvideo" id="bgvideo" src="<?php echo"$videodirectory$selecetedvideo" ?>"></video>
    <?php if($showalert == true): ?>
        <!--alert box-->
        <dialog class="alertbox" open>
            <?php echo $alertmessage;;?>
        </dialog>
    <?php endif; ?>

    <!--volumbe button-->
        <button class="volumebtn" onclick="volumeOn()">
        <i class="fa-solid fa-volume-high" id="volume-high"></i>
        <i class="fa-solid fa-volume-xmark" id="volume-muted"></i>
        </button>
    
    <div class="askperm" id="askperm" onclick="playVideo()">
        <h1>Click to enter</h1>
    </div>

    <div class="main">
        <img src="assets/logo.png" class="logo" height="17%">
        <br>
        <h1 class="title">Yavuz Semih</h1>
        <div class="socialicons">
            <a href="https://discord.gg/9KC8wVDJRN" target="_blank" class="socialicon">
                <i class="fa-brands fa-discord"></i><span class="trigger">trigger</span><span class="icontext">Discord</span>
            </a>
            <a href="https://open.spotify.com/user/31r2avmsoy64inobavr752tj7qoy?si=ad35e166b378456e" target="_blank" class="socialicon">
                <i class="fa-brands fa-spotify"></i><span class="trigger">trigger</span><span class="icontext">Spotify</span>
            </a>
            <a href="https://www.instagram.com/yavuzxclwq1/" target="_blank" class="socialicon">
                <i class="fa-brands fa-instagram"></i><span class="trigger">trigger</span><span class="icontext">Instagram</span>
            </a>
            <a href="https://steamcommunity.com/id/yavuzesah/" target="_blank" class="socialicon">
                <i class="fa-brands fa-steam"></i><span class="trigger">trigger</span><span class="icontext">Steam</span>
            </a>
            <a href="https://github.com/yavuzWWW" target="_blank" class="socialicon">
                <i class="fa-brands fa-github"></i><span class="trigger">trigger</span><span class="icontext">Github</span>
            </a>
            <a href="https://vasthosting.nl" target="_blank" class="socialicon">
                <i class="fa-solid fa-globe"></i><span class="trigger">trigger</span><span class="icontext">Vast Hosting</span>
            </a>
        </div>

        <div class="viewercount">
        <i class="fa-solid fa-eye"></i>&nbsp;<?php echo $viewcount; ?>
        </div>
    </div>

    <script>
            let volume = 'off';
    const bgvideo = document.getElementById('bgvideo');
    const mutedicon = document.getElementById('volume-muted');
    const volumeonicon = document.getElementById('volume-high');
    const askperm = document.getElementById('askperm');
    volumeonicon.style.display = "none";
    bgvideo.pause();

    function volumeOn() {
        if (volume == 'off') {
            //this one turns on the volume
        bgvideo.muted = false;
        mutedicon.style.display = "none";
        volumeonicon.style.display = "block";
        volume = 'on';
        }else if (volume == 'on') {
            //this one turns off the volume
        bgvideo.muted = true;
        mutedicon.style.display = "block";
        volumeonicon.style.display = "none";
        volume = 'off';
        }
    }

    function playVideo(){
        bgvideo.play();
        askperm.style.display = "none";
        volumeOn();

    }
    </script>
</body>
</html>
