<?php
require_once ("Answer.php");
define('bot_url',"") ;
define('bot_dl_url',"") ;
$update = file_get_contents("php://input");
$update_array = json_decode($update, true);
$answer=null;
$text=null ;
if( isset($update_array["message"]["text"])) {
    $chat_id = $update_array["message"]["chat"]["id"];
    $text = $update_array["message"]["text"];
    $answer=new Answer(bot_url,$text,$chat_id);
    $answer->select_answer($text);
}

else if( isset($update_array["callback_query"]) ) {

    $data              = $update_array["callback_query"]["data"];
    $callback_query_id = $update_array["callback_query"]["id"];
    $chat_id           = $update_array["callback_query"]["message"]["chat"]["id"];

    $answer=new Answer(bot_url,$data,$chat_id);
}

else if( isset($update_array["message"]["document"]) ){
    $chat_id= $file_id   = $update_array["message"]["chat"]["id"];
    $answer=new Answer(bot_url,"ss",$chat_id);

}

else if( isset($update_array["message"]["photo"]) ) {
    $chat_id= $file_id   = $update_array["message"]["chat"]["id"];
    $answer=new Answer(bot_url,"ss",$chat_id);
    $answer->save_photo($update_array);

}

if( isset($update_array["message"]["reply_to_message"]) ) {

    $text_replied = $update_array["message"]["reply_to_message"]["text"];

}






?>
<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$config =  json_decode(file_get_contents("config.json"));

$bot_api_key  = $config->token;
$bot_username = $config->name;

$commands_paths = [
    __DIR__ . '/Commands',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
    $telegram->addCommandsPaths($commands_paths);

    // Handle telegram webhook request
    $telegram->handle();

} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e->getMessage();
    //Save string to log, use FILE_APPEND to append.
    file_put_contents('./log_'.date("j.n.Y").'.log', date("H:i:s",time())."\t".$e->getMessage(), FILE_APPEND);
}
