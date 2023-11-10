<?php

header("HTTP/1.1 200 OK");
http_response_code(200);

$API_KEY = $_GET['token'];
$send = $_GET['send'];
define('API_KEY', $API_KEY);
$users = json_decode($_GET['users']);

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

$msgid = file_get_contents("botstatus/msgid.txt");
$userid = file_get_contents("botstatus/userid.txt");

if ($send == 'send')
{
    foreach ($users as $user)
    {
        bot('copyMessage',[
            'chat_id' => $user,
            'from_chat_id' => $userid,
            'message_id' => $msgid
        ]);
    }
}
elseif ($send == 'forward')
{
    foreach ($users as $user)
    {
        bot('forwardMessage',[
            'chat_id' => $user,
            'from_chat_id' => $userid, 
            'message_id' => $msgid
        ]);
    }
}

exit;

?>