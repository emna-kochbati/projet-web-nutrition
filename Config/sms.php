<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

class SMS
{
    private static $sid = "AC07571e6cbb2f05c4eef05c78c908f9c8";
    private static $token = "c8c164a0b9478f0cffc3274dbb23531a";
    private static $from = "+14254724073"; // numéro Twilio

    public static function send($to, $message)
    {
        try {
            $client = new Client(self::$sid, self::$token);

            $client->messages->create($to, [
                "from" => self::$from,
                "body" => $message
            ]);

            return true;
        } catch (Exception $e) {
            error_log("Erreur SMS: " . $e->getMessage());
            return false;
        }
    }
}