<?php

/**
 * This file is part of the PHP Telegram Bot example-bot package.
 * https://github.com/php-telegram-bot/example-bot/
 *
 * (c) PHP Telegram Bot Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;

require_once __DIR__ . '/../Entities/Query.php';
use Query;

class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Handle the callback query';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws \Exception
     */
    public function execute(): ServerResponse
    {
        // Callback query data can be fetched and handled accordingly.
        $callback_query = $this->getCallbackQuery();
        $callback_data  = $callback_query->getData();
        
        
        if(startsWith($callback_data, "set_cat")){
            $args = array_slice(explode(" ",$callback_data), 1);
            Query::set_cat($args[0], $args[1]);
        }

        return $callback_query->answer([
            'text'       => 'Content of the callback data: ' . $callback_data,
            'cache_time' => 5,
        ]);
    }
}

function startsWith ($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}