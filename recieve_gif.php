<?php
// save in Telegram URLs
// commentating the the whole thing

include_once 'config.php';

// $update = file_get_contents("php://input");
// $update_obj = json_decode($update);
    
$please_send_us_gifname = "لطفا نام پیشنهادی برای گیف را به صورت پاسخ به همین پیام برای ما بفرستید";
$please_send_us_the_gif = "لطفا فایل gif مد نظر رو به صورت پاسخی به همین پیام برای ما بفرستید :";
$mg_file_recieved = "فایل gif مد نظر شما با موفقیت بارگذاری شد";
$create_sharifi_tables = 
// "CREATE DATABASE IF NOT EXISTS botbasig_sharif
// CHARACTER SET utf8
// COLLATE utf8_general_ci;

// USE botbasig_sharif;

"CREATE TABLE IF NOT EXISTS users(
`userID`        INT(15)     NOT NULL AUTO_INCREMENT PRIMARY KEY,
`name`          VARCHAR(50) NOT NULL,
`step`          INT(11)     NOT NULL DEFAULT '0',
`gif_id`        INT(11)     NOT NULL DEFAULT '0',
`isRegistered`  TINYINT(1)  NOT NULL DEFAULT '0',
`isAdmin`       TINYINT(1)  NOT NULL DEFAULT '0'
);


CREATE TABLE IF NOT EXISTS gif_table(
`id`        INT(11)         NOT NULL AUTO_INCREMENT PRIMARY KEY,
`chat_id`   VARCHAR(20)     NOT NULL,
`url` 	    VARCHAR(150)    NOT NULL,
`gif_name`  VARCHAR(50)     NOT NULL
);";

if( isset($update_obj->message) ) {
    $user_id = $update_obj->message->from->id;
    $chat_id = $update_obj->message->chat->id;
    $type2 = $update_obj->message->chat->type; // != update
    $textmessage = isset($update_obj->message->text) ? $update_obj->message->text : "";
    // issue the step
    $connection = connect_to_db();
    try {
        $result     = $connection -> prepare("SELECT * FROM users WHERE userID = :user_id");
        $result -> bindParam(":user_id", $user_id);
        // $user_id    = $chat_id;
        $result ->execute();
        
        // the reasult of the fetch is an array
		$row        = $result -> fetch(); 
        $status       = $row['step'];
    } catch (Exception $e) { // SQLException 
        echo 'Caught exception: ',  $e->getMessage(), "\n";                 
    }

    $text_replied = isset($update_obj->message->reply_to_message->text) ? 
                    $update_obj->message->reply_to_message->text : "";
    if (isset($update_obj->message->reply_to_message)) {
        $message_id = $update_obj->message->message_id;
        $firstname = $update_obj->message->from->first_name;
        $lastname = $update_obj->message->from->last_name;
        $username = $update_obj->message->from->username;
        $replied_message_id = $update_obj->message->reply_to_message->message_id;
        
        if ( strpos($textmessage, $botusername) ) {
            $gif_name = str_replace ($botusername , "" , $textmessage);
            $gif_name = trim($gif_name);
            $url = search_gif ( $gif_name , $chat_id) ; 
            if ($url != "0") {
                $post_params = [
                                    'chat_id' => $chat_id , 
                                    'animation' => new CURLFILE(realpath("$url")) , 
                                    'reply_to_message_id' => $replied_message_id ,
                                    'caption' => "$firstname $lastname (@$username), requested the GIF : '$gif_name'" , 
                                ];
                send_reply('sendAnimation', $post_params);
                
                // delete original msg
                $post_params = ['chat_id'=> $chat_id , 'message_id' => $message_id];
                send_reply ('deleteMessage' , $post_params);
                } else {
                $post_params = ['chat_id' => $chat_id , 'text' => "🤷‍♂️", 'reply_to_message_id' => $message_id];
            }
            send_reply('sendMessage' , $post_params);
        }
    }
    

    if (isset ($update_obj->message->animation)) {
        $file_id        = $update_obj->message->animation->file_id;
        $mime_type	      = $update_obj->message->animation->mime_type;
    }

	if(substr($textmessage , 0 , 1) == "/")
		command_manager ($status , $textmessage , $type2 , $chat_id);

	else /*if ($textmessage != "/start" . $GLOBALS['botusername'])*/ {
		// continuer
		step_manager($status);
	}
	
	// making fun of one of my friends
	if ($user_id == "486272895" && trim($textmessage) == "...") {
		send_reply('sendMessage' , ['chat_id' => $chat_id , 'text' => "تا سه ثانیه دیگه این پیام آقای اخلاقی پاک میشه..." 
		            , 'reply_to_message_id'=> $update_obj->message->message_id]);
		sleep(3);
		send_reply('deletemessage', ['chat_id' => $chat_id , 'message_id' => $update_obj->message->message_id]);
		$akhlaghi_txt = "پیام بی مزه سه نقطه اخلاقی پاک شد😂😂\n" . "با تشکر ربات مدیر گروه 451";
		send_reply('sendMessage' , ['chat_id' => $chat_id , 'text' => $akhlaghi_txt]);
	}
} 


// starters of clients' commands
function command_manager ($step , $textmessage , $type2 , $chat_id) {
	if (substr($textmessage , 0 , 6) == "/start" && substr($textmessage , 6) == $GLOBALS['botusername'] && 
		($type2 == "supergroup" || $type2 == "group")) {

		initial_user();
	}

	if (substr($textmessage , 0 , 7) == '/newgif' && substr($textmessage , 7) == $GLOBALS['botusername']  /*&& $status < 70*/ && 
		($type2 == "supergroup" || $type2 == "group") ) { 
		    
		// starter of the function 
		get_gifs_details($step);
		


		// $connection = connect_to_db();
		// $result = $connection -> prepare ("SHOW TABLES LIKE 'users'"); 
		// $result -> execute();
		// $rr = $result -> rowCount();
		// if ($rr == "1") 
		// $post_params = ['chat_id' => $chat_id , 'text' => $rr];
		// send_reply ('sendMessage' , $post_params);
	// 		$result -> bindParam(":gif_name" , $gif_name);
	} else if ($textmessage == '/newgif' && $type2 == "private") {
		$post_params    = [ 'chat_id' =>  $chat_id , 'text' => 'قابلیت ذخیره گیف فقط در داخل گروه ها امکان پذیره'];
		send_reply("sendMessage" , $post_params);
	}
}

// continue
// we impliment this function just for get_gifs_details() but
// we'll have no problem on adding more similar functions in the future
function step_manager ($step) { 
	if ($step < 60)
		$dummy = 12;
    else if ($step < 70) // 60 - 69
        get_gifs_details($step);
}

function initial_user() {
    
// save chat id for everyuser
	$connection = connect_to_db();
	try {
		$result = $connection -> prepare($GLOBALS['create_sharifi_tables']);  
		$result -> execute();
	} catch (Exception $e) { 
		echo 'Caught exception: ',  $e->getMessage(), "\n";               
	}
	$user_id = $GLOBALS['user_id']; 
	try {
		$result = $connection -> prepare("INSERT INTO users (userID) VALUES (:user_id)");  
		$result -> bindParam(":user_id", $user_id); // is our primary key column
		$result -> execute();
	} catch (Exception $e) { 
		echo 'Caught exception: ',  $e->getMessage(), "\n";               
	}
	
// zero step
	$result = $connection -> prepare("UPDATE users SET step = 0 WHERE userID = :user_id"); 
	$result -> bindParam(":user_id" , $user_id);
	$result -> execute();
	
// delete each incomplete Database row
	$result = $connection -> prepare("SELECT * FROM gif_table"); 
    $result -> execute();
    $row = $result -> fetch();
    if (isset($row['id'])) {
        do {
            if ($row['url'] == "" || $row['gif_name'] == "") {
                // delte
            	$result = $connection -> prepare("DELETE FROM gif_table WHERE id = :id");
	    		$result -> bindParam(':id', $row['id']);
                $result -> execute();
                // $result = $connection -> query ("DELETE FROM gif_table WHERE id = $row['id']); 
            }
        } while ( $row = $result -> fetch() ) ;
    }
}


function get_gifs_details($step) {
	$user_id   = $GLOBALS['user_id'];
    $chat_id   = $GLOBALS['chat_id'];
	if ( isset($GLOBALS['textmessage'])) 
	    $text      = trim($GLOBALS['textmessage']);
	$replied_mg_id = 0;
	if ($step != 0 && $GLOBALS['text_replied'] != "" ) { 
		$text_replied   = $GLOBALS['text_replied'];
		$DB_id      = strtok ($text_replied , "\n");
        $replied_mg_id = isset($GLOBALS['update_obj']->message->reply_to_message->from->id)? 
            $GLOBALS['update_obj']->message->reply_to_message->from->id : "";
	}
    // if we're at the first step, we'll specify the $step for our own agenda
	if ($step == 0) {
		$step = 60;
	}

	$connection = connect_to_db();
	$result = $connection -> prepare("SELECT * FROM users WHERE userID = :user_id");
	$result -> bindParam(':user_id', $user_id);
	$result -> execute();
	$row    = $result -> fetch(); 
	$gif_id = $row['gif_id'];

	$force_reply = ['force_reply' => true , 'selective' => true];
    $reply = "";
	switch ($step) {
		case 60 :
			$reply = $GLOBALS['please_send_us_gifname'];
			$step = 61; 
			break;
		case 61 :  // $recieve_the_gif_name  
		    if(isset($GLOBALS['update_obj']->message->text) && substr($text , 0 , 1) != "/" 
		        && $replied_mg_id == $GLOBALS['idbot'] ) { // ** if it's a proper text
		        
				//  send_reply ('sendMessage' , ['chat_id' => $chat_id , 'text' => "61"]);
		        if (is_unique_name($text , $chat_id , $connection)) {
					$result = $connection -> prepare("INSERT INTO gif_table (gif_name , chat_id) VALUES (:text , :chat_id)");
					$result -> bindParam(':text', $text);
					$result -> bindParam(':chat_id', $chat_id);
					$result -> execute();
					
					$gif_id = $connection ->lastInsertId();
					$reply = $gif_id  . "\n" . $GLOBALS['please_send_us_the_gif'];
					$step = 62;
				} else {
					$reply = "نمیتوانید نام تکراری انتخاب کنید\n" . $GLOBALS['please_send_us_gifname'];
				}

		    } else if ($replied_mg_id == $GLOBALS['idbot'] ) { // any stronger condition to ensure if user has replied to the right msg?
		        $reply = "لطفا یک نام استاندارد برای گیف خود بگذارید\n" . $GLOBALS['please_send_us_gifname'];
		    }
			break;
		case 62 :  // $recive_the_file
			if ($GLOBALS['mime_type'] == "video/mp4" && $replied_mg_id == $GLOBALS['idbot'] && $gif_id == $DB_id) { 
				// save_gif($GLOBALS['file_id'] , $DB_id , $connection);
				save_gif($GLOBALS['file_id'] , $gif_id , $connection);
				$reply = $GLOBALS['mg_file_recieved'];
				$force_reply = ['force_reply' => false];
				$step = 0;
				$gif_id = 0;
			}
			else if ($replied_mg_id == $GLOBALS['idbot'] && $gif_id == $DB_id) {
				$warning = "لطفا به پیام ربات مربوطه، یک فایل gif بفرستید 😤";
				$post_params = [ 'chat_id' =>  $chat_id , 'text' => $warning];
				send_reply("sendMessage", $post_params);
				$reply = $gif_id . "\n" . $GLOBALS['please_send_us_the_gif'];
			} else {
			    $reply = "else";
			}
			break;			
	}
	if ($reply != "") {
        $json_fr        = json_encode($force_reply); 
        $post_params    = [ 'chat_id' =>  $chat_id , 'text' => $reply, 
                            'reply_to_message_id' => $GLOBALS['message_id'] , 'reply_markup' => $json_fr ];
        send_reply("sendMessage", $post_params);	 
        
        
    	// update step & gif_id in DB
    	try {
    		$result = $connection -> prepare("UPDATE users SET step = :step , gif_id = :gif_id WHERE userID = :user_id");
    		$result -> bindParam(':step', $step);
    		$result -> bindParam(':gif_id' , $gif_id);
    		$result -> bindParam(':user_id', $user_id);
    		$result -> execute();
    	} catch (Exception $e) { // SQLException 
    		echo 'Caught exception: ',  $e->getMessage(), "\n";                 
    	}
	}
	
	if ($step == 0) {
	    // delete incompletes
	    initial_user();
	}
}

function is_unique_name ($gif_name , $chat_id , $connection) {
	$result = $connection -> prepare("SELECT * FROM gif_table WHERE gif_name = :gif_name && chat_id = :chat_id"); 
	$result -> bindParam(":gif_name" , $gif_name);
	$result -> bindParam(":chat_id" , $chat_id);
	$result -> execute();
	$counter = $result -> rowCount();
	if ($counter != 0) { // is not unique
		return false; 
	} else  // there is no gif_name like $gif_name so it is unique
		return true; 
}

function save_gif($file_id , $DB_id , $connection) {
	$post_params = ['file_id' => $file_id];
	$file_instance = send_reply('getFile' , $post_params);
	$file_instance_array = json_decode($file_instance , true);
	
	$gif_path = $file_instance_array["result"]["file_path"];
	
	$url = $GLOBALS['bot_dl_url'] . "/$gif_path";
	$file_data = file_get_contents($url);
    
	$gif_path = "gif/$DB_id.mp4";
	$new_file = fopen($gif_path , 'w');
	fwrite($new_file , $file_data);
	fclose($new_file);

// we needed this kinda addressing only for inline searching for the gifs
// 	$gif_path = $GLOBALS['gif_saving_path'] . "/$DB_id.mp4";

	$result = $connection -> prepare("UPDATE gif_table SET url = :gif_path WHERE id = :DB_id"); 
	$result -> bindParam(":gif_path" , $gif_path);
	$result -> bindParam(":DB_id" , $DB_id);
	$result -> execute();
}



function search_gif ( $gif_name , $chat_id ) {
    $connection = connect_to_db();
    
	$result = $connection -> prepare ("SELECT * FROM gif_table WHERE gif_name = :gif_name AND chat_id = :chat_id"); 
	$result -> bindParam(":chat_id" , $chat_id);
	$result -> bindParam(":gif_name" , $gif_name);
	$result -> execute();
    
	$url = "0";
	$row = $result -> fetch();
    if (isset($row['id'])) { // we are sure we have just one result on the database
		$url = $row['url'];
	}
	return $url;
    // $result_array = build_result_array ( $result);
    // return $result_array;
}

// function request_gif($inline_query_id , $inline_query , $user_id) {
//     // chetori user ha ro ba ham ghati nemikone?
//     // va /start robat haye mokhtalef ro?
//     $gif_name = $inline_query;
	
//     $result = search_gif ($user_id , $gif_name);
//     $result_json = json_encode($result);
    
//     $post_params = ["inline_query_id" => $inline_query_id , 'results' => $result_json ];
//     return $post_params;
// }

// function build_result_array ( $result ) { 
//     $result_array = array();   
//     $row = $result -> fetch();
//     if (isset($row['id'])) {
//         do {
//             $result_array[] = 
//                 [
//                     'type'        => "video" ,
//                     'id'          => $row['id']  ,        
//                     'video_url'   => $row['url'] ,
//                     'mime_type'   => "video/mp4" ,
//                     'thumb_url'   => $GLOBALS['gif_saving_path'] . "/image2.jpg" ,
//                     'title'       => $row['gif_name']  ,
//                     'description' => "" ,
//                 ];
//         } while ($row = $result -> fetch());
//     }
    
//     return $result_array;
// }


// function send_reply($method, $post_params = []) {
//     $url = $GLOBALS['bot_url'] . "/" . $method;

//     $cu = curl_init();
//     curl_setopt($cu, CURLOPT_URL, $url);
//     curl_setopt($cu, CURLOPT_POSTFIELDS, $post_params);
//     curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);  // gets the result
//     $result = curl_exec($cu);

//     curl_close($cu);
//     return $result;
// }

function connect_to_db() {
    $DB_username    = $GLOBALS['DB_username'];
    $DB_password    = $GLOBALS['DB_password'];
    $DNS            = $GLOBALS['DNS']        ;
    
	$options = [
                    // از این به بعد همیشه باید ترای کچ بزنم
					PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES   => false,
				];
	$connection = new PDO(	$DNS, $DB_username, $DB_password , $options);
	$connection -> query("SET NAMES utf8mb4"); // and not utf8mb4
	return $connection;
}
?>