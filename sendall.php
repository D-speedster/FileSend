<?php
//===================
ini_set('max_execution_time',0);
set_time_limit(0);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$Database = [
    'dbname' => "bot_filesend", // put database name
    'username' => "root", // put database username
    'password' => "lXZK7F0*1rXY9*80vK" // put database password
];
$MySQLi = mysqli_connect('localhost', $Database['username'], $Database['password'], $Database['dbname']);
$SelectAllUsers = mysqli_query($MySQLi, "SELECT `id` FROM `user`");
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(file_get_contents("botstatus/senduser.txt") >= mysqli_num_rows($SelectAllUsers)-1){
file_put_contents("botstatus/sendfwduser.txt",'false');
file_put_contents("botstatus/sendpmuser.txt",'false');
file_put_contents("botstatus/senduser.txt",0);
die;
}

if(file_get_contents("botstatus/sendfwduser.txt") == 'false' and file_get_contents("botstatus/sendpmuser.txt") == 'false'){
file_put_contents("botstatus/senduser.txt",0);
die;
}
//===================
$stats = file_get_contents("botstatus/sendfwduser.txt");
$stats2 = file_get_contents("botstatus/sendpmuser.txt");
//===================
const API_KEY = '6186090739:AAGBFEtc59gfmOXRXQGIVECChdO57l_ob5U';
//===================
function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
//===================
if($stats == 'true' or $stats2 == 'true'){
if(file_get_contents("botstatus/senduser.txt") == null){
	file_put_contents("botstatus/senduser.txt",0);
}
$member2 = mysqli_fetch_all($SelectAllUsers);
$member = file_get_contents("botstatus/senduser.txt");
$sendcount = 250;
$sendcount2 = 1000;
$dash = $member + $sendcount;
$dash2 = $member + $sendcount2;
} 
//===================
if($stats == 'true'){
file_put_contents("botstatus/senduser.txt",$dash);
$sended = 0;
    
$userss = [];
for ($s = (int)$member; $s <= (int)$dash; $s++)
{
    $userss[] = $member2[$s][0];
}

file_get_contents("https://sparta021.top/FileSend/apisend.php?send=forward&token=".API_KEY."&users=".json_encode($userss));

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($stats2 == 'true'){
file_put_contents("botstatus/senduser.txt",$dash2);
$sended = 0;
for($i = 0; $i < ($sendcount2/200); $i++){

    $sended_now = $member+$sended;
    $userss = [];
    for ($s = $sended_now; $s <= ($sended_now+200); $s++)
    {
        $userss[] = $member2[$s][0];
    }
    
    file_get_contents("https://sparta021.top/FileSend/apisend.php?send=send&token=".API_KEY."&users=".json_encode($userss));
    $sended += 200;

}

}

exit;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>