<?php
require_once "Message.php";

class MessageManager
{

    static public function Get($code, $key)
    {
        return isset(Message::$messages[$code][$key]) ? Message::$messages[$code][$key] : '--NO-MESSAGE--';
    }
}
