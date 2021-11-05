<?php
require_once ('config.php');
require_once ("Answer.php");
// https://api.telegram.org/bot1497141769:AAGIWCHGlLAzxFqNdY9Ch-WF0YBcBgRZBGY/setwebhook?url=https://botbasige.cptele.ir/mohamad_sharifi/m-ghafour/index.php
// define('bot_dl_url',"https://api.telegram.org/file/bot1876663810:AAGahX6GYh17Nb5CIAntKGQ-7hv6U7b3JrM") ;
$update_obj = json_decode(file_get_contents("php://input"));

$answer=null;
$textmessage=null ;
if( isset($update_obj->message->text)) {
    $chat_id = $update_obj->message->chat->id;
    $textmessage    = $update_obj->message->text;
    $answer  = new Answer(/*bot_url,*/$textmessage,$chat_id);
    $answer->select_answer($textmessage);
//    $image=new image();

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
    if(!is_null($answer)) {
        $text_replied = $update_obj->message->reply_to_message->text;
        if($text_replied=="لطفا نام گروه درسی خود،نام دانشکده،رشته را وارد کنید و با ، از هم جدا کنید")
            $answer->sabt_group($textmessage);
        else
        $answer->get_info_Handout($textmessage);
    }
}
?>
