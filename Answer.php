<?php


class Answer
{
    var $text;
    var $chat_id;
    var $bot_url;
    var $db;
   var $url_pdf="";
     var  $bot_dl_url="https://api.telegram.org/file/bot2033681076:AAFpCOwyce14ooc2c2MRAoimPPxkXzvpi0w" ;

    /**
     * Answer constructor.
     */
    public function __construct($bot_url, $text, $chat_id)
    {
        require_once("Db.php");
        require_once("image.php");
        $this->text = $text;
        $this->chat_id = $chat_id;

        $this->bot_url = $bot_url;
        $this->db = new Db($this->chat_id);

    }


    function select_answer($text)
    {

        $key1 = '';
        $key2 = 'ارتباط با ما';
        $key3 = '';
        $key4 = 'comscaner';
        $key5 = 'درباره ما';
        $key6 = '';
        $reply_keyboard = [
            [$key5, $key2],
            [$key6, $key1],
            [$key4, $key3],

        ];
        $reply_kb_options = [
            'keyboard' => $reply_keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];


        switch ($text) {
            case "/start" :
                $this->show_menu($reply_kb_options);
                break;
            case "ارتباط با ما":
                $this->contect_us();
                $this->db->step_user("contect_us",$this->chat_id);
                break;
            case "comscaner":
                $this->send_message("لطفا عکس هاتون رو بفرستید و بعد از اتمام کلمه پایان رو بفرستید");
                $this->db->step_user("comscaner",$this->chat_id);
                break;
            case "درباره ما":
                $this->Abot_us();
                $this->db->step_user("Abot_us",$this->chat_id);
                break;

            case"پایان":
                $this->comscaner();
                break;



        }
    }



    function comscaner()
    {
        $name_pdf="pdf/".$this->chat_id.".pdf";
        $img=$this->db->get_url_photo($this->chat_id);
        $this->send_message($name_pdf);
        new image($img,$this->chat_id);
        $url = bot_url . "/sendDocument";
        $post_params = [
            'chat_id' => $GLOBALS['chat_id'],
            'document' => new CURLFILE(realpath("$name_pdf")),
            'caption' => "خدمت شما ",  // optional
            'parse_mode' => 'HTML',

        ];
        $this->send_reply($url, $post_params);


    }

function   save_photo($update_array1){
    $update_array =$update_array1;

    $diff_size_count = sizeof($update_array["message"]["photo"]);

    for($i = $diff_size_count - 1 ; $i >= 0 ; $i--) {

        $file_size = $update_array["message"]["photo"][$i]["file_size"];

        if($file_size < 1000000) {  // 1 MB

            $file_id = $update_array["message"]["photo"][$i]["file_id"];
            break;
        }
    }

    $url = bot_url . "/getFile";
    $post_params = [ 'file_id' => $file_id ];
    $result = $this->send_reply($url, $post_params);

    $result_array = json_decode($result, true);
    $file_path    = $result_array["result"]["file_path"];

    $url = $this->bot_dl_url . "/$file_path";
    $file_data = file_get_contents($url);
    $url_img=$this->db->url_photo($this->chat_id,$this->chat_id);
    $img_path =$url_img;
    $my_file  = fopen($img_path, 'w');
    fwrite($my_file, $file_data);
    fclose($my_file);
$this->send_message("تصویر با موفقیت آپلود شد");
}


    function send_message($tex)
    {
        $reply = $tex;
        $url = $this->bot_url . "/sendMessage";
        $post_params = ['chat_id' => $GLOBALS['chat_id'], 'text' => $reply, 'parse_mode' => 'HTML'];
        $this->send_reply($url, $post_params);
    }
    function send_message_reply($tex, $json_fr)
    {
        $reply = $tex;
        $url = $this->bot_url . "/sendMessage";
        $post_params = ['chat_id' => $GLOBALS['chat_id'], 'text' => $reply, 'parse_mode' => 'HTML', 'reply_markup' => $json_fr];
        $this->send_reply($url, $post_params);
    }
    function send_message_alert($tex)
    {
        $reply = $tex;
        $url = $this->bot_url . "/sendMessage";
        $post_params = ['chat_id' => $GLOBALS['chat_id'], 'text' => $reply, 'parse_mode' => 'HTML','show_alert' => true];
        $this->send_reply($url, $post_params);
    }//alert kar nemikone

    function show_menu($reply_kb_options)
    {

        $json_kb = json_encode($reply_kb_options);
        $reply = "<b>" . "یکی از گزینه های زیر را انتخاب کنید" . "</b>";
        $url = bot_url . "/sendMessage";
        $post_params = ['chat_id' => $GLOBALS['chat_id'], 'text' => $reply, 'reply_markup' => $json_kb, 'parse_mode' => 'HTML'];
        $this->send_reply($url, $post_params);
    }

    function contect_us()
    {
        $url = $this->bot_url . "/sendContact";
        $post_params = [
            'chat_id' => $this->chat_id,
            'phone_number' => "<b>" . "09131234567" . "</b>",
            'first_name' => "basige",
            'last_name' => "daneshjoii", // optional
            'parse_mode' => 'HTML'
        ];
        $this->send_reply($url, $post_params);
    }

    function Abot_us()
    {
        $about = "<b>" . "بسیج دانشجویی دانشگاه فردوسی" . "</b>";
        $about .= "\n" . "ربات نظر سنجی و دریافت جزوه با ثبت نام در ربات ";
        $this->send_message("$about");
    }

    function send_reply($url, $post_params)
    {

        $cu = curl_init();
        curl_setopt($cu, CURLOPT_URL, $url);
        curl_setopt($cu, CURLOPT_POSTFIELDS, $post_params);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, true); // get result
        $result = curl_exec($cu);
        curl_close($cu);
        return $result;
    }


}