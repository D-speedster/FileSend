<?php
ini_set('memory_limit' , '-1');
ini_set('max_execution_time', '0');

function http_respond_200()
{
  // Apache2 and Nginx webservers
  if(is_callable('fastcgi_finish_request'))
  {
    session_write_close();
    fastcgi_finish_request();
    return;
  }
  // litespeed webservers
  elseif(is_callable('litespeed_finish_request'))
  {
    session_write_close();
    litespeed_finish_request();
    return;
  }
  // if finish_request is not callable
  ignore_user_abort(true);
  ob_start();
  $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
  header($serverProtocol . ' 200 OK');
  header('Content-Encoding: none');
  header('Content-Length: ' . ob_get_length());
  header('Connection: close');
  @ob_end_flush();
  @ob_flush();
  @flush();
  sleep(1);
}

http_respond_200();

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

$a=bot('forwardMessage', [
       'chat_id' => '703859331',
       'from_chat_id' => '703859331',
       'message_id'=> 5
    ]);
    echo json_encode($a, 64|128|256);
exit;  


$ii = 0;
$msg = $_GET['msg'];
while ($ii != 300){
    bot('forwardMessage', [
       'chat_id' => '703859331',
       'from_chat_id' => '@amirtestnow',
       'message_id'=> 28465
    ]);
    $ii++;
}

?>