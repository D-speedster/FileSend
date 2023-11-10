<?php

$Config = [
    'api_token' => "6186090739:AAGBFEtc59gfmOXRXQGIVECChdO57l_ob5U",
    'admin' => [79049016, 824024437,429273267, 703859331 , 170256094]
]; 

$Database = [
    'dbname' => "bot_filesend", // put database name
    'username' => "root", // put database username
    'password' => "lXZK7F0*1rXY9*80vK" // put database password
];

$MySQLi = mysqli_connect('localhost', $Database['username'], $Database['password'], $Database['dbname']);
// ------------------ { Functions } ------------------ //

function WSBot($method, $datas = []) {
    global $Config;
    $curl = curl_init('https://api.telegram.org/bot'.$Config['api_token'].'/'.$method);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $datas,
        CURLOPT_CUSTOMREQUEST => 'POST',
    ]);
    $response = json_decode(curl_exec($curl), true); 
    return $response;
}

$timenow = time();
$get_my_rows = mysqli_query($MySQLi, "SELECT * FROM `msgid_scheduler` WHERE `remove_in` < $timenow");

if (mysqli_num_rows($get_my_rows) == 0)
{
    exit("There Is No Any Row");
}
else
{
    foreach ($get_my_rows as $row)
    {
        WSBot('deleteMessage',[
            'chat_id' => $row['userid'],
            'message_id' => $row['msgid']
        ]);
        mysqli_query($MySQLi, "DELETE FROM `msgid_scheduler` WHERE `remove_in` = {$row['remove_in']}");
    }
}

?>