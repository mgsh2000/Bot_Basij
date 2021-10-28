<?php
require_once ("Answer.php");
// define('bot_url',"https://api.telegram.org/bot1880352321:AAEdEIQqIC8usrtGf7JM6qNaVIBLlhUJLDI") ;
// robat asli's bot token
// 2033681076:AAFpCOwyce14ooc2c2MRAoimPPxkXzvpi0w
// M. Sharifi's bot token
define('bot_dl_url',"https://api.telegram.org/file/bot1497141769:AAGIWCHGlLAzxFqNdY9Ch-WF0YBcBgRZBGY") ;
$update = file_get_contents("php://input");
// $update_obj = json_decode($update, true);
$update_obj = json_decode($update);
$answer=null;
$text=null ;
if( isset($update_obj->message->text)) {
    $chat_id = $update_obj->message->chat->id;
    $text = $update_obj->message->text;
    $answer=new Answer(/*bot_url,*/$text,$chat_id);
    $answer->select_answer($text);
 //   $image=new image();

}

else if( isset($update_obj->callback_query) ) {

    $data              = $update_obj->callback_query->data;
    $callback_query_id = $update_obj->callback_query->id;
    $chat_id           = $update_obj->callback_query->message->chat->id;

    $answer=new Answer(/*bot_url,*/$data,$chat_id);
    $answer->get_point();
}

else if( isset($update_obj->message->document) ){
    $chat_id= $file_id   = $update_obj->message->chat->id;
    $answer=new Answer(/*bot_url,*/"ss",$chat_id);
    $answer->save_pdf($update_obj);

}

else if( isset($update_obj->message->photo) ) {
    $chat_id= $file_id   = $update_obj->message->chat->id;
    $answer=new Answer(/*bot_url,*/"ss",$chat_id); // useless bot_url
    $answer->save_photo($update_obj);

}

if( isset($update_obj->message->reply_to_message) ) {

    $text_replied = $update_obj->message->reply_to_message->text;
    if($text_replied=="لطفا نام گروه درسی خود،نام دانشکده،رشته را وارد کنید و با ، از هم جدا کنید")
        $answer->sabt_group($text);
    else
        $answer->get_info_Handout($text);

}
?>
