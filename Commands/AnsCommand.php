<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\InlineKeyboard;

require_once __DIR__ . '/../Entities/Query.php';
use Query;

class AnsCommand extends UserCommand
{
    protected $name = 'ans';                      // Your command's name
    protected $description = 'A command for answering a query'; // Your command description
    protected $usage = '/ans <id>';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();            // Get Message object

        $answer = $message->getReplyToMessage();
        
        if(!$answer->getProperty('text')){
            return $this->replyToChat("You should reply this command to another message.");
        }

        if(!$message->getText(true)){
            return $this->replyToChat("Usage : " . $this->getUsage());
        }
        

        $query    = new Query((int)$message->getText(true));

        $data = [                                  // Set up the new message data
            'chat_id' => $query->get_from_key('chat_id'),                 // Set Chat ID to send the message to
            'text'    => $answer->getProperty('text') . PHP_EOL . "@" .  $query->get_from_key('sender_name')// Set message to send
        ];

        Request::sendMessage($data)->send();        // Send message!
        return $this->replyToChat('done!');        // Send message!

        

    }
}
