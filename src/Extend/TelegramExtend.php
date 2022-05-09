<?php

namespace WeiQing\Library\Extend;


use GuzzleHttp\Client;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramExtend
{

    protected $bot_username;

    protected $bot_token;

    public static function make(string $bot_username, string $bot_token): TelegramExtend
    {

        $self = new self();
        $self->bot_token = $bot_token;
        $self->bot_username = $bot_username;

        new Telegram($self->bot_token, $self->bot_username);
        Request::setClient(new Client(['base_uri' => 'https://api.telegram.org','verify' => false]));
        return $self;
    }

    /**
     * 发送消息
     * @param $chat_id
     * @param $content
     * @param $reply_to_message
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendMessage($chat_id, $content, $reply_to_message = null)
    {
        $message = [
            "chat_id" => $chat_id,
            "text" => $content,
            "parse_mode" => "HTML"
        ];
        if ($reply_to_message) {
            $message['reply_to_message_id'] = $reply_to_message;
        }
        $ret = Request::sendMessage($message);
        var_dump($ret);
    }

    /**
     * @param $chat_id
     * @param $content
     * @param $markup
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendBtnMessage($chat_id, $content, $markup)
    {
        Request::sendMessage([
            "chat_id" => $chat_id,
            "text" => $content,
            "parse_mode" => "HTML",
            'reply_markup' => new InlineKeyboard($markup)
        ]);
    }

    /**
     * @param $id
     * @param $txt
     */
    public function answerCallbackQuery($id, $txt)
    {
        Request::answerCallbackQuery([
            'callback_query_id' => $id,
            'text' => $txt
        ]);
    }

    public function setWebHook($url)
    {
        $ret = Request::setWebhook([
            "url" => $url,
            "max_connections" => 100
        ]);
        var_dump($ret);
        return json_decode($ret, true);
    }

    public function deleteWebHook(): \Longman\TelegramBot\Entities\ServerResponse
    {
        return Request::deleteWebhook([]);
    }

    /**
     * 编辑消息
     * @param $data
     */
    public function editMessageText($data){
        $res = Request::editMessageReplyMarkup($data);
        var_dump($res);
    }
}