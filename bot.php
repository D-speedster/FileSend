<?php

if (!is_dir("botstatus")) {
    mkdir("botstatus");
}

if (!is_dir("users_multi_files")) {
    mkdir("users_multi_files");
}
date_default_timezone_set('Asia/Tehran');

set_time_limit(0);

ini_set('max_execution_time','0');

// ------------------ { Your Config } ------------------ //
$timing = 30;
if (!file_exists('channels.json'))
{
	file_put_contents("channels.json", json_encode(['channels' => []]));
}
$chnl = json_decode(file_get_contents("channels.json"), true);
$aa = file_get_contents("step.txt");

$Config = [
    'api_token' => "6186090739:AAGBFEtc59gfmOXRXQGIVECChdO57l_ob5U",
    'admin' => [79049016, 5440842664, 703859331]
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

function RandomString() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = null;
    for ($i = 0; $i < 9; $i++) {
        $randstring .= $characters[
            rand(0, strlen($characters))
        ];
    }
    return $randstring;
}
function  getUserProfilePhotos($from_id) {
    global $Config;
    $url = 'https://api.telegram.org/bot'.$Config['api_token'].'/getUserProfilePhotos?user_id='.$from_id;
    $result = file_get_contents($url);
    $result = json_decode ($result);
    $result = $result->result;
    return $result;
}
function convert($size){
    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).' '.['', 'K', 'M', 'G', 'T', 'P'][$i].'B';
}
function doc($name) {
    if ($name == "document") {
        return "پرونده ( سند )";
    }
    elseif ($name == "video") {
        return "ویدیو";
    }
    elseif ($name == "photo") {
        return "عکس";
    }
    elseif ($name == "voice") {
        return "ویس";
    }
    elseif ($name == "audio") {
        return "موزیک";
    }
    elseif ($name == "sticker") {
        return "استیکر";
    }
}
// ------------------ { Variables } ------------------ //
$update = json_decode(file_get_contents('php://input'));
if(isset($update->message)) {
    $message = $update->message;
    $text = $message->text;
    $tc = $message->chat->type;
    $chat_id = $message->chat->id;
    $from_id = $message->from->id;
    $message_id = $message->message_id;
    $first_name = $message->from->first_name;
    $last_name = $message->from->last_name;
    $username = $message->from->username?:'اکانت شما بدون یوزرنیم میباشد ... !';
    $getuserprofile = getUserProfilePhotos($from_id);
}
if (isset($update->callback_query)) {
    $callback_query = $update->callback_query;
    $data = $callback_query->data;
    $tc = $callback_query->message->chat->type;
    $chatid = $callback_query->message->chat->id;
    $fromid = $callback_query->from->id;
    $messageid = $callback_query->message->message_id;
    $firstname = $callback_query->from->first_name;
    $lastname = $callback_query->from->last_name;
    $cusername = $callback_query->from->username;
    $membercall = $callback_query->id;
}
// ------------------ { Connect MySQL } ------------------ //
$user = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `id` = '{$from_id}' LIMIT 1"));
$user2 = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `user` WHERE `id` = '{$fromid}' LIMIT 1"));
// ------------------ { Connect MySQL & Creat Table } ------------------ //
if ($MySQLi->query("SELECT * FROM `user`") == false) {
    mysqli_query($MySQLi, "CREATE TABLE IF NOT EXISTS `user` (
        `id` bigint(10) NOT NULL PRIMARY KEY,
        `step` varchar(50) NOT NULL,
        `upload` bigint(10) NOT NULL,
        `code` char(200) NOT NULL
        )"
	);
}
if ($MySQLi->query("SELECT * FROM `dbfile`") == false) {
    mysqli_query($MySQLi, "CREATE TABLE IF NOT EXISTS `dbfile` (
        `code` char(250) NOT NULL PRIMARY KEY,
        `file_id` char(200) NOT NULL,
        `file` char(200) NOT NULL,
        `password` char(200) CHARACTER SET utf8mb4 NOT NULL,
        `file_size` bigint(20) NOT NULL,
        `user_id` bigint(20) NOT NULL,
        `date` char(200) NOT NULL,
        `time` char(200) NOT NULL,
        `dl` bigint(20) NOT NULL
        )"
    );
}
if ($MySQLi->query("SELECT * FROM `dbfile2`") == false) {
    mysqli_query($MySQLi, "CREATE TABLE IF NOT EXISTS `dbfile2` (
        `code` char(250) NOT NULL PRIMARY KEY,
        `password` char(200) CHARACTER SET utf8mb4 NOT NULL,
        `user_id` bigint(20) NOT NULL,
        `files` TEXT NOT NULL,
        `date` char(200) NOT NULL,
        `time` char(200) NOT NULL,
        `dl` bigint(20) NOT NULL
        )"
    );
}
if ($MySQLi->query("SELECT * FROM `msgid_scheduler`") == false) {
    mysqli_query($MySQLi, "CREATE TABLE IF NOT EXISTS `msgid_scheduler`(
        `msgid` bigint not null,
        `userid` bigint not null,
        `remove_in` bigint not null
        )"
    );
}
// ------------------ { Informations } ------------------ //
$from_id = $message->from->id;
$ffid = $from_id ?? $fromid;
$botusername = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getme'), true)['result']['username'];
$usernamebot = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getMe'), true)['result']['username'];
$stats = ["creator","member","administrator"];

function checkMsgJoin($fid)
{
	$chnl = $GLOBALS['chnl'];
	$stats = $GLOBALS['stats'];
	$Config = $GLOBALS['Config'];
	if (count($chnl['channels']) > 0)
	{
		foreach ($chnl['channels'] as $chnl => $chlink)
		{
			$rank = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getChatMember?chat_id='.$chnl.'&user_id='.$fid), true)['result']['status'];
			if(!in_array($rank,$stats)){
				return "$chlink[1]";
			}
		}
		return true;
	}
	else
	{
		return true;
	}

}
// ------------------ { Keyboards } ------------------ //
if (in_array($from_id, $Config['admin'])) {
    $menu = json_encode(['keyboard'=>[
        [['text' => "آپلود فایل 📑"]],
        [['text' => "🔑 مدیریت"] , ['text' =>"👥 حساب کاربری"]]
        ], 'resize_keyboard' => true
    ]);
} else {
    $menu = json_encode(['keyboard'=>[
        [['text' => "آپلود فایل 📑"]],
        [['text' => "📂 تاریخچه اپلود"], ['text' =>"👥 حساب کاربری"]],
    
        ], 'resize_keyboard' => true
    ]);
}
if (in_array($from_id, $Config['admin'])) {
    $panel = json_encode(['keyboard' => [
        [['text' => "👤 امار ربات"]],
			[['text' => "📪 ارسال به همه"], ['text' => "📪 فوروارد به همه"]],
				[['text' => "ثبت کانال ♻️"],['text' => "حذف کانال ♻️"]], 
		[['text' => "💀 تنظیم کپشن فایل"]],
			[['text' => "🔙"]],
        ], 'resize_keyboard' => true
    ]);
   
    $back_panel = json_encode(['keyboard' => [
        [['text' => "برگشت 🔙"]]
        ], 'resize_keyboard' => true
    ]);
}
// ------------------ { Back Keyboards } ------------------ //
$back = json_encode(['keyboard' => [
    [['text' => "🔙 بازگشت"]],
    ], 'resize_keyboard' => true
]);
$remove = json_encode(['remove_keyboard' => [
    ], 'remove_keyboard' => true
]);
$startint = str_replace(["/start", ' '],'',$text);

function checkCode($text)
{
	$MySQLi = $GLOBALS["MySQLi"];
    $dataFile = mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `code` = '{$text}' LIMIT 1");
    if (mysqli_num_rows($dataFile) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}

// ------------------ { Start Source } ------------------ //
if($data == "join") {
    $chk = checkMsgJoin($ffid);
    if($chk === true) {
   WSBot('EditMessageText', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => "👤 سلام <code>$firstname</code> به ربات خوش آمدی",
            'parse_mode' => "html"
        ]);
        if (!$user2) {
            $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$fromid}', 'none', '0', '')");
        } else {
        file_put_contents("step.txt",'none');
            $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$fromid}' LIMIT 1");
        }
    } else {
        WSBot('answercallbackquery', [
            'callback_query_id' => $membercall,
            'text' => "❌ هنوز داخل کانال $chk عضو نیستید", 
            'message_id' => $messageid,
            'show_alert' => true
        ]);
    }
}
if(checkMsgJoin($ffid) !== true and !in_array($ffid, $Config['admin'])) {
if(checkCode($startint) === false){
	$keys = [];
	$num = 1;
	foreach ($chnl['channels'] as $chn => $chlink)
	{
		$keys[] = [['text' => "$chlink[1]", 'url' => $chlink[0]]];
		$num += 1;
	}
	$keys[] = [['text' => "👍 عضو شدم", 'callback_data' => "join"]];
	$join = json_encode(['inline_keyboard' => $keys]);
    WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال های زیر شوید

بعد از پیوستن به چنل ها {عضو شدم👍} را برای ادامه انتخاب کنید✅", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join
    ]);
}else{
	$keys = [];
	$num = 1;
	foreach ($chnl['channels'] as $chn => $chlink)
	{
		$keys[] = [['text' => "$chlink[1]", 'url' => $chlink[0]]];
		$num += 1;
	}
	$keys[] = [['text' => "👍 عضو شدم", 'url' => "https://t.me/$botusername?start=".$startint]];
	$join2 = json_encode(['inline_keyboard' => $keys]);
	WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "💡 ابتدا باید وارد کانال های زیر شوید

بعد از پیوستن به چنل ها {عضو شدم👍} را برای ادامه انتخاب کنید✅", 
        'reply_to_message_id' => $message_id,
        'reply_markup' => $join2
    ]);
}}
elseif (preg_match('/^\/(start)$/i', $text) or $text == "🔙") {
     WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "👤 سلام <code>$first_name</code>\n🤖\n\n🏷 اپلود رایگان و دائم فایل ها بدون هیچ محدودیت زمانی !\n\n🚦 شما میتوانید ( عکس , فیلم , گیف , استیکر و ... ) در ربات اپلود کنید همراه با نمایش تعداد دانلود های فایل شما که شما برای فایلتون انتخاب کردید فایل برایش ارسال میشود ... !\n\n📤 همین الان فایلاتو اپلود کن و لذتشو ببر !\n\n🤖 @$usernamebot",
        'reply_to_message_id' => $message_id,
        'parse_mode' => "html",
        'reply_markup' => $menu
    ]);
    if (!$user) {
    
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
    } else {
    file_put_contents("step.txt",'none');
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}

elseif(strpos($text, "/start ") !== false) {
    $idFile = str_replace("/start ", '', $text);
    $dataFile = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `code` = '{$idFile}' LIMIT 1"));
    $dl = number_format($dataFile['dl']);
    if ($dataFile['password']) {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ لطفا رمز فایل را ارسال کنید تا فایل برای شما ارسال شود :",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $remove
        ]);

        if (!$user) {
            $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'checkpassword', '0', '{$idFile}')");
        } else {
        file_put_contents("step.txt",'nonr');
            $MySQLi->query("UPDATE `user` SET `step` = 'checkpassword', `code` = '{$idFile}' WHERE `id` = '{$from_id}' LIMIT 1");
        }
        
    } else {
        $dl = number_format($dataFile['dl']);
        $fls = $dataFile['files'];
        $my_array = eval('return '.$fls.';');
        $getc = file_get_contents("filec.txt");
        foreach ($my_array as $my_value)
        {
            $wsbot=WSBot('copyMessage', [
                'chat_id' => $chat_id,
                'from_chat_id' => $dataFile['user_id'],
                'message_id' => $my_value,
                'reply_to_message_id' => $message_id,
                'reply_markup' => $menu,
                'caption' => $getc
            ]);
            $rem_time = time() + $timing;
            $MySQLi->query("INSERT INTO `msgid_scheduler` (`userid`, `msgid`, `remove_in`) VALUES ($from_id, {$wsbot['result']['message_id']}, $rem_time)");
        }
    WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "‼️توجه کنید عزیزان‼️
فیلم بالا تا 1 الی 2 دقیقه دیگر از ربات پاک
میشه👆❌
تعداد دانلود  : $dl
➖➖➖➖➖➖➖
لطفا فیلم هارو به پیوی دوستان یا خودتون و یا کانال خصوصی خودتون بفرستید که از ربات پاک میشه (جهت جلوگیری از فیلترینگ)
🛑دوستان در ربات اصلا دانلود نکنید🛑
✅ اول به یجایی بفرستید بعد دانلود کنید✅
سپاس از همراهی و حمایت شما 💐💐"
        ]);
        if (!$user) {
            $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
        } else {
            $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
        }
        $MySQLi->query("UPDATE `dbfile2` SET `dl` = `dl` + 1 WHERE `code` = '{$idFile}' LIMIT 1");
    }
}
elseif ($user['step'] == "checkpassword") {
    $dataFile = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `code` = '{$user['code']}' LIMIT 1"));
    if ($text == $dataFile['password']) {
        $dl = number_format($dataFile['dl']);
        $fls = $dataFile['files'];
        $my_array = eval('return '.$fls.';');
        $getc = file_get_contents("filec.txt");
        foreach ($my_array as $my_value)
        {
            $wsbot=WSBot('copyMessage', [
                'chat_id' => $chat_id,
                'from_chat_id' => $dataFile['user_id'],
                'message_id' => $my_value,
                'reply_to_message_id' => $message_id,
                'reply_markup' => $menu,
                'caption' => $getc
            ]);
            $rem_time = time() + $timing;
            $MySQLi->query("INSERT INTO `msgid_scheduler` (`userid`, `msgid`, `remove_in`) VALUES ($from_id, {$wsbot['result']['message_id']}, $rem_time)");
        }
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "‼️توجه کنید عزیزان‼️
فیلم بالا تا 1 الی 2 دقیقه دیگر از ربات پاک میشه👆❌
➖➖➖➖➖➖➖
لطفا فیلم هارو به پیوی دوستان یا خودتون و یا کانال خصوصی خودتون بفرستید که از ربات پاک میشه (جهت جلوگیری از فیلترینگ)
🛑دوستان در ربات اصلا دانلود نکنید🛑
✅ اول به یجایی بفرستید بعد دانلود کنید✅
سپاس از همراهی و حمایت شما 💐💐"
        ]);
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
        $MySQLi->query("UPDATE `dbfile2` SET `dl` = `dl` + 1 WHERE `code` = '{$dataFile['code']}' LIMIT 1");
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ پسورد اشتباه است , لطفا پسورد صحیح را ارسال کنید :\n🔸 در صورت نیاز به منوی اصلی روی /start کلیک کنید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "🔙 بازگشت") {
    WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "🌼 به منوی اصلی ربات برگشتیم \n\n🎉 برای استفاده از ربات از دکمه های زیر استفاده کنید",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $menu
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}


elseif(strpos($text, "/dl_") !== false) {
    $idFile = str_replace("/dl_", '', $text);
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `user_id` = '{$from_id}' and `code` = '{$idFile}' LIMIT 1"));
    if ($query) {
        $dl = number_format($query['dl']);
        $fls = $query['files'];
        $my_array = eval('return '.$fls.';');
        foreach ($my_array as $my_value)
        {
            WSBot('copyMessage', [
                'chat_id' => $chat_id,
                'from_chat_id' => $query['user_id'],
                'message_id' => $my_value,
                'reply_to_message_id' => $message_id,
                'reply_markup' => $menu
            ]);
        }
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "📥 تعداد دانلود ها : <code>{$dl}</code>\n▪️ شناسه : <code>{$query['code']}</code>\n\n📥 https://t.me/".$usernamebot."?start=".$idFile."\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => 'html'
        ]);
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}


elseif ($text == "🗑 حذف فایل") {
    WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "▪️لطفا شناسه فایل خود را ارسال کنید :\n📍 توجه کنید که بعد از فرستادن شناسه , فایل همان لحظه پاک میشود پس لطفا الکی شناسه فایلتون رو ارسال نکنید و فقط در صورت نیاز استفاده بکنید از این بخش ... !",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $back
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'delete', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'delete' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif($user['step'] == "delete") {
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `user_id` = '{$from_id}' and `code` = '{$text}' LIMIT 1"));
    if ($query) {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "✔️ فایل با موفقیت حذف شد ... !",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $menu
        ]);
        $MySQLi->query("UPDATE `user` SET `step` = 'none', `upload` = `upload` - 1 WHERE `id` = '{$from_id}' LIMIT 1");
        $MySQLi->query("DELETE FROM `dbfile2` WHERE `code` = '{$text}' and `user_id` = '{$from_id}' LIMIT 1");
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "🗂 کد پیگیری فایل") {
    WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "▪️لطفا شناسه فایل خود را ارسال کنید :",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $back
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'checkfile', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'checkfile' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif ($user['step'] == "checkfile") {
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `user_id` = '{$from_id}' and `code` = '{$text}' LIMIT 1"));
    if ($query) {
        $time = $query['time'];
        $date = $query['date'];
        $dl = $query['dl'];
        $password = $query['password']?$query['password']:'این فایل بدون رمز عبور است ... !';
        $fls = $query['files'];
        $my_array = eval('return '.$fls.';');
        foreach ($my_array as $my_value)
        {
            WSBot('copyMessage', [
                'chat_id' => $chat_id,
                'from_chat_id' => $query['user_id'],
                'message_id' => $my_value,
                'reply_to_message_id' => $message_id,
                'reply_markup' => $menu
            ]);
        }
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ شناسه فایل های شما : <code>$text</code>\n\n➖ تعداد دانلود : $dl\n🔐 رمز فایل : <code>$password</code>\n🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>"."\n\n📥 https://t.me/".$usernamebot."?start=".$query['code']."\n\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => $menu
        ]);
        $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "👥 حساب کاربری") {
    if ($getuserprofile->photos[0][0]->file_id != null) {
        WSBot('sendphoto', [
            'chat_id' => $chat_id,
            'photo' => $getuserprofile->photos[0][0]->file_id,
            'caption' => "💭 حساب کاربری شما در ربات ما :\n\n 📤 تعداد پکیج فایل های اپلود شده توسط شما : <b>{$user['upload']}</b> \n👤 نام کانت شما : <code>$first_name</code>\n🌟 یوزنیم اکانت شما : <code>$username</code>\n\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => json_encode(['inline_keyboard' => [
                [['text' => "▪️ تعداد پکیج فایل اپلود شده", 'callback_data' => "none"], ['text' => $user['upload'], 'callback_data' => "none"]]
                ]
            ])
        ]);
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "💭 حساب کاربری شما در ربات ما :\n\n 📤 تعداد پکیج فایل های اپلود شده توسط شما : <b>{$user['upload']}</b> \n👤 نام کانت شما : <code>$first_name</code>\n🌟 یوزنیم اکانت شما : <code>$username</code>\n\n🤖 @$usernamebot",
            'reply_to_message_id' => $message_id,
            'parse_mode' => "html",
            'reply_markup' => json_encode(['inline_keyboard' => [
                [['text' => "▪️ تعداد پکیج فایل اپلود شده", 'callback_data' => "none"], ['text' => $user['upload'], 'callback_data' => "none"]]
                ]
            ])
        ]);
    }
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'none', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif($text == "📂 تاریخچه اپلود") {
    $query = mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `user_id` = {$from_id}");
    $num = mysqli_num_rows($query);
    if($num > 0) {
        $result = "📂 تاریخچه اپلود های شما :\n📍 تعداد فایل های اپلود شده ی شما : $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
        $cnt = ($num >= 10)?10:$num;
        for ($i = 1; $i <= $cnt; $i++) {
            $fetch = mysqli_fetch_assoc($query);
            $id = $fetch['code'];
            $file_size = convert($fetch['file_size']);
            $file = doc($fetch['file']);
            $time = $fetch['time'];
            $date = $fetch['date'];
            $password = $fetch['password']?$fetch['password']:'این فایل بدون رمز عبور است ... !';
            $result .= $i.". 📥 /dl_".$id.PHP_EOL."💾 ".$file_size.PHP_EOL."▪️ نوع فایل : <b>$file</b>".PHP_EOL."🔐 رمز فایل : <code>$password</code>".PHP_EOL."🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>".PHP_EOL."➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖".PHP_EOL;
        }
        if($num > 10){
            WSBot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => $result,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html",
                'reply_markup' => json_encode(['inline_keyboard' => [
                    [['text' => "▪️ صفحه ی بعدی", 'callback_data' => "Dnext_10"]]
                    ]
                ])
            ]);
        } else {
            WSBot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => $result,
                'reply_to_message_id' => $message_id,
                'parse_mode' => "html",
            ]);
        }
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ تاریخچه اپلود شما خالی میباشد ... !",
            'reply_to_message_id' => $message_id
        ]);
    }
}
elseif ($text == "🔐 تنظیم پسورد") {
    WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => '▪️ لطفا شناسه فایل خود را ارسال کنید :',
        'reply_to_message_id' => $message_id,
        'parse_mode' => "html",
        'reply_markup' => $back
    ]);
    if (!$user) {
        $MySQLi->query("INSERT INTO `user` (`id`, `step`, `upload`, `code`) VALUES ('{$from_id}', 'setid', '0', '')");
    } else {
        $MySQLi->query("UPDATE `user` SET `step` = 'setid' WHERE `id` = '{$from_id}' LIMIT 1");
    }
}
elseif($user['step'] == "setid") {
    $query = mysqli_fetch_assoc(mysqli_query($MySQLi, "SELECT * FROM `dbfile2` WHERE `user_id` = '{$from_id}' and `code` = '{$text}' LIMIT 1"));
    if ($query) {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️لطفا پسورد دلخواه رو بفرستید تا فایل شما قفل شود :",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
        $MySQLi->query("UPDATE `user` SET `code` = '{$text}', `step` = 'setpassword' WHERE `id` = '{$from_id}' LIMIT 1");
    } else {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "▪️ خطا , این فایل در دیتابیس موجود نمیباشد یا فایل مال شخص دیگری میباشد و  شما اجازه دسترسی به این فایل را ندارید ... !\n🔐 لطفا شناسه فایل را صحیح بفرستید :",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
    }
}
elseif ($user['step'] == "setpassword") {
    WSBot('sendmessage', [
        'chat_id' => $chat_id,
        'text' => "✔️ با موفقیت فایل شما قفل شد ... !",
        'reply_to_message_id' => $message_id,
        'reply_markup' => $menu
    ]);
    $MySQLi->query("UPDATE `dbfile2` SET `password` = '{$text}' WHERE `code` = '{$user['code']}' LIMIT 1");
    $MySQLi->query("UPDATE `user` SET `step` = 'none', `code` = '' WHERE `id` = '{$from_id}' LIMIT 1");
}
elseif(strpos($data, "Dnext_") !== false) {
    $last_id = str_replace('Dnext_', '', $data);
    $query = mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$fromid}'");
    $num = mysqli_num_rows($query);
    $result = "📂 تاریخچه اپلود های شما :\n📍 تعداد فایل های اپلود شده ی شما : $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
    $records = [];
    while ($fetch = mysqli_fetch_assoc($query)) {
        $records[] = $fetch;
    }
    if($last_id + 10 < $num){
        $endponit = $last_id + 10;
    } else {
        $endponit = $num;
    }
    for ($i = $last_id; $i < $endponit; $i++) {
        $id = $records[$i]['code'];
        $file_size = convert($records[$i]['file_size']);
        $file = doc($records[$i]['file']);
        $time = $records[$i]['time'];
        $date = $records[$i]['date'];
        $password = $records[$i]['password']?$records[$i]['password']:'این فایل بدون رمز عبور است ... !';
        $result .= $i.". 📥 /dl_".$id.PHP_EOL."💾 ".$file_size.PHP_EOL."▪️ نوع فایل : <b>$file</b>".PHP_EOL."🔐 رمز فایل : <code>$password</code>".PHP_EOL."🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>".PHP_EOL."➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖".PHP_EOL;
    }
    if($num > $last_id + 10){
        WSBot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_".$endponit], ['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_".$endponit]]
                ]
            ])
        ]);
    } else {
        WSBot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_".$endponit]]
                ]
            ])
        ]);
    }
}
elseif(strpos($data, "Dprev_") !== false) {
    $last_id = str_replace('Dprev_', '', $data);
    $query = mysqli_query($MySQLi, "SELECT * FROM `dbfile` WHERE `user_id` = '{$fromid}'");
    $num = mysqli_num_rows($query);
    $result = "📂 تاریخچه اپلود های شما :\n📍 تعداد فایل های اپلود شده ی شما : $num\n➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖\n\n";
    $records = [];
    while ($fetch = mysqli_fetch_assoc($query)) {
        $records[] = $fetch;
    }
    if($last_id % 10 == 0){
        $endponit = $last_id - 10;
    } else {
        $last_id = $last_id-($last_id % 10);
        $endponit = $last_id;
    }
    for ($i = $endponit - 10; $i <= $endponit; $i++) {
        $id = $records[$i]['code'];
        $file_size = convert($records[$i]['file_size']);
        $file = doc($records[$i]['file']);
        $time = $records[$i]['time'];
        $date = $records[$i]['date'];
        $password = $records[$i]['password']?$records[$i]['password']:'این فایل بدون رمز عبور است ... !';
        $result .= $i.". 📥 /dl_".$id.PHP_EOL."💾 ".$file_size.PHP_EOL."▪️ نوع فایل : <b>$file</b>".PHP_EOL."🔐 رمز فایل : <code>$password</code>".PHP_EOL."🕓 تاریخ و زمان اپلود : <b>".$date." - ".$time."</b>".PHP_EOL."➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖ ➖".PHP_EOL;
    }
    if($num > $last_id and $endponit - 10 > 0) {
        WSBot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_".$endponit], ['text' => "➖ صفحه ی قبلی", 'callback_data' => "Dprev_".$endponit]]
                ]
            ])
        ]);
    } else {
        WSBot('editmessagetext', [
            'chat_id' => $chatid,
            'message_id' => $messageid,
            'text' => $result,
            'parse_mode' => "html",
            'reply_markup' =>  json_encode(['inline_keyboard' => [
                [['text' => "➕ صفحه بعدی", 'callback_data' => "Dnext_".$endponit]]
                ]
            ])
        ]);
    }
}

// ------------------ { Uploarder } ------------------ //
elseif ($text == "آپلود فایل 📑") {
    file_put_contents("./users_multi_files/$from_id.array", json_encode(["ids" => []], 64|128|256));
    WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "رسانه / رسانه های مورد نظر خود را ارسال کنید",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
    ]);
    $MySQLi->query("UPDATE `user` SET `step` = 'upload_filing' WHERE `id` = '{$from_id}' LIMIT 1");	
}

elseif ($user['step'] == "upload_filing") {
    $my_json = json_decode(file_get_contents("./users_multi_files/$from_id.array"), true);
    if (strtoupper($text) == "/DONE")
    {
        if (count($my_json['ids']) == 0){
            WSBot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "هنوز هیچ فایلی اپلود نکردی :|",
                'reply_to_message_id' => $message_id,
                'reply_markup' => $back
            ]);
            exit;
        }
        else
        {
            $code = RandomString();
            $files = '['.implode(', ', $my_json['ids']).']';
            WSBot('sendmessage', [
                'chat_id' => $chat_id,
                'text' => "📍 فایل های شما با موفقیت داخل دیتابیس ذخیره شده ... !
▪️ شناسه فایل شما : $code

📥 https://t.me/".$usernamebot."?start=$code",
                'reply_to_message_id' => $message_id,
                'reply_markup' => $menu
            ]);
            $MySQLi->query("INSERT INTO `dbfile2` (`code`, `password`, `user_id`, `files`, `date`, `time`, `dl`) VALUES ('$code', '', $from_id, '$files', 'none', '0', '0')");
            $MySQLi->query("UPDATE `user` SET `step` = 'none', `upload` = `upload` + 1 WHERE `id` = '{$from_id}' LIMIT 1");
            unlink("./users_multi_files/$from_id.array");
            exit;
        }
    }
    if (isset($message->document) or isset($message->video) or isset($message->photo) or isset($message->voice) or isset($message->audio) or isset($message->sticker))
    {
        $my_json['ids'][] = $message_id;
        file_put_contents("./users_multi_files/$from_id.array", json_encode($my_json, 64|128|256));
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "عالیه
اگه بخوای بازم میتونی فایل بفرستی که به صورت مولتی و فقط با یه شناسه به همش دسترسی داشته باشی
هر وقت کارت تموم شد دستور /done رو برام بفرست",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
    }
    else
    {
        WSBot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "چیزی که فرستادی رسانه قابل اپلود نیست",
            'reply_to_message_id' => $message_id,
            'reply_markup' => $back
        ]);
    }
    $MySQLi->query("UPDATE `user` SET `step` = 'upload_filing' WHERE `id` = '{$from_id}' LIMIT 1");
}
// ------------------ { Panel Admin } ------------------ //
if (in_array($from_id, $Config['admin'])) {
	if (strtolower($text) == "/panel" or $text == "🔑 مدیریت" or $text == "panel") {
	    WSBot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "👤 به منوی مدیریت ربات خود خوش امدید",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $panel
	    ]);
	file_put_contents("step.txt",'none');
	    $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
	}
	
	if ($text == "💀 تنظیم کپشن فایل") {
    	file_put_contents("step.txt",'setfilecapt');
        WSBot('sendmessage', [
    	        'chat_id' => $chat_id,
    	        'text' => "متن مورد نظر را جهت الصاق روی کپشن بفرستید",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $back_panel
	    ]);
		
    }
    
    elseif($aa == 'setfilecapt' && $text != "برگشت 🔙") {
        file_put_contents("filec.txt","$text");
        WSBot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "با موفقیت ثبت شد ♻️",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $panel
	    ]);
	    file_put_contents("step.txt",'none');
	    $MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
    }
	
	if ($text == 'ثبت کانال ♻️') {
	file_put_contents("step.txt",'setchannel');
WSBot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "یوزرنیم کانال خود را با @  یا ایدی عددی ان را همراه با - در اول ان ارسال کنید ♻️
			
درخط دوم لینک نمایشی کانال را بفرستید
و در خظ آخر نام نمایشی کانال را ست کنید",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $back_panel
	    ]);
		
}
	elseif($aa == 'setchannel') {
	$usr_id = str_replace(strchr($Config['api_token'],":"),'',$Config['api_token']);
	
	
		if ($text != "برگشت 🔙" && $text != 'ثبت کانال ♻️') {
			$ex = explode("\n", $text);
	if (count($ex) != 3)
	{
		WSBot('sendmessage', [
			'chat_id' => $chat_id,
			'text' => "لطفا اطلاعات را در 3 خط بفرستید",
			'reply_to_message_id' => $message_id
		]);
		exit;
	}
	$ch = $ex[0];
	$chlink = $ex[1];
	$namech = $ex[2];
	$rnk = json_decode(file_get_contents('https://api.telegram.org/bot'.$Config['api_token'].'/getChatMember?chat_id='.$ch.'&user_id='.$usr_id),true)['result']['status'];
		if($rnk == "administrator"){
		if (isset($chnl['channels'][$ch]))
		{
			WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "این کانال از قبل ثبت شده است",
			    'reply_to_message_id' => $message_id
			]);
			exit;
		}
		$chnl['channels'][$ch] = [$chlink, $namech];
		file_put_contents("channels.json", json_encode($chnl, 64|128|256));
		file_put_contents("step.txt",'none');
			WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "کانال ثبت شد !",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $panel
			]);
		}else{
		WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "من در این کانال ادمین نیستم !

ابتدا من رو در این کانال ادمین کرده سپس مجددا امتحان کنید",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $back_panel
			]);
		} 
	}
		}
		if ($text == 'حذف کانال ♻️') {
			if (count($chnl['channels']) == 0)
			{
				WSBot('sendmessage', [
					'chat_id' => $chat_id,
					'text' => "هیچ کانالی ثبت نکرده اید",
					'reply_to_message_id' => $message_id
				]);
				exit;
			}
			$keys = [];
			foreach ($chnl['channels'] as $chn => $chlink)
			{
				$keys[] = [['text' => $chn]];
			}
			$keys[] = [['text' => "برگشت 🔙"]];
			$back_panel = json_encode(['keyboard' => $keys, 'resize_keyboard' => true]);
			file_put_contents("step.txt",'delch');
			WSBot('sendmessage', [
				'chat_id' => $chat_id,
				'text' => "یوزرنیم کانال خود را بدون @ ارسال کنید ♻️",
				'reply_to_message_id' => $message_id,
				'reply_markup' => $back_panel
			]);
		
}
	elseif($aa == 'delch') {
		if ($text != "برگشت 🔙" && $text != 'حذف کانال ♻️') {
		if (isset($chnl['channels'][$text])){
		unset($chnl['channels'][$text]);
		file_put_contents("channels.json", json_encode($chnl, 64|128|256));
		file_put_contents("step.txt",'none');
			WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "کانال حذف شد !",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $panel
			]);
		}
	}
		}
	if ($text == "برگشت 🔙") {
	    WSBot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "▪️ به منوی مدیریت بازگشتید :",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $panel
	    ]);
	file_put_contents("step.txt",'none');
		$MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	
	if ($text == "👤 امار ربات") {
		$users = mysqli_query($MySQLi, "SELECT `id` FROM `user`");
		$alluser = mysqli_num_rows($users);
		$time = date('h:i:s');
		$date = date('Y/m/d');
		WSBot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "🕊 آمار لحظه ای ربات : \n🙎🏻‍♂️ به درخواست : $first_name\n⏰ ساعت گزارش : $time \n📅 تاریخ گزارش : $date  \n🗂 فایل های آپلود شده توسط شما : <b>{$user['upload']}</b> فایل\n \r\r\r🏆 تعداد کل کاربران ربات : <b>$alluser</b> نفر",
	        'reply_to_message_id' => $message_id,
	        'parse_mode' => "html",
	        'reply_markup' => $panel
	    ]);
	    
	}
	if ($text == '📪 ارسال به همه' ) {
	    WSBot('sendmessage', [
	        'chat_id' => $chat_id,
	        'text' => "▪️ لطفا پیام خود را ارسال کنید :",
	        'reply_to_message_id' => $message_id,
	        'reply_markup' => $back_panel
	    ]);
		$MySQLi->query("UPDATE `user` SET `step` = 'sendtoall' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	if ($user['step'] == 'sendtoall') {
		if ($text != "برگشت 🔙") {
		$MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
			file_put_contents('botstatus/sendpmuser.txt','true');
			file_put_contents("botstatus/msgid.txt",$message_id);
			file_put_contents("botstatus/userid.txt",$from_id);
			WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "پیام شما با موفقیت به عنوان ارسال همگانی تنظیم شد ✔️",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $panel
			]);
		}	
	}
	if ($text == '📪 فوروارد به همه') {
	    WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "▪️ لطفا پیام خود را فوروارد کنید :",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $back_panel
		]);
		$MySQLi->query("UPDATE `user` SET `step` = 'fortoall' WHERE `id` = '{$from_id}' LIMIT 1");	
	}
	if ($user['step'] == 'fortoall') {
		if ($text != "برگشت 🔙") {
			$MySQLi->query("UPDATE `user` SET `step` = 'none' WHERE `id` = '{$from_id}' LIMIT 1");
			file_put_contents('botstatus/sendfwduser.txt','true');
			file_put_contents("botstatus/msgid.txt",$message_id);
			file_put_contents("botstatus/userid.txt",$from_id);
			WSBot('sendmessage', [
			    'chat_id' => $chat_id,
			    'text' => "پیام شما به عنوان فوروارد همگانی تنظیم شد ✔️",
			    'reply_to_message_id' => $message_id,
			    'reply_markup' => $panel
			]);
		}
	}
}
?>