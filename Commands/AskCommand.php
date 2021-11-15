<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;

require_once __DIR__ . '/../Entities/Query.php';
use Query;

class AskCommand extends UserCommand
{
    protected $name = 'ask';                      // Your command's name
    protected $description = 'A command for asking a query'; // Your command description
    protected $usage = '/ask';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();            // Get Message object

        $quest = $message->getReplyToMessage();
        
        if(!$quest || !$quest->getProperty('text')){
            return $this->replyToChat("You should reply this command to another message.");
        }

        $chat_id    = $message->getChat()->getId();
        
        $cat = Query::get_cat($chat_id);
        if($cat){

            $query = Query::insert(
                $quest->getFrom()->getId(),
                $quest->getFrom()->getUsername(), 
                $chat_id, $quest->getProperty('text'), 
                $cat);

            $data = [                                  // Set up the new message data
                'chat_id' => Query::get_config()[$cat],                 // Set Chat ID to send the message to
                'text'    => $query->get_message() // Set message to send
            ];
    
            Request::sendMessage($data)->send();        // Send message!

            return $this->replyToChat('done!');        // Send message!

        }else{
            $buttons = [];
            foreach (array_keys(Query::get_config()) as $cat){
                $buttons[] = ['text' => $cat, 'callback_data' => 'set_cat ' . $chat_id . ' ' . $cat];
            }

            $inline_keyboard = new InlineKeyboard($buttons);
    
            return $this->replyToChat('Inline Keyboard', [
                'text' => 'cant recognize this chats category...
                choose one:',
                'reply_markup' => $inline_keyboard,
            ]);
        }
    }
}
