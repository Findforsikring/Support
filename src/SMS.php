<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 23-06-2017
 * Time: 14:51
 */

namespace Findforsikring\Support;


use GuzzleHttp\Client;

/**
 * Class SMS
 * @package Findforsikring\Support
 * Example:
 * $sms = new Findforsikring\Support\SMS
 * $sms->sender("Forsikring")->recipient("61693354")->message("Wazzup?")->send();
 */
class SMS
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $recipient;
    /**
     * @var string
     */
    private $sender;
    /**
     * @var string
     */
    private $message;
    /**
     * @var bool
     */
    private $flash = false;

    /**
     * SMS constructor.
     * @throws \Exception
     */
    public function __construct(){
        if (!env('SMS_API_KEY')){
            throw new \Exception("SMS_API_KEY missing in .env");
        }
        $this->url = "http://api.linkmobility.dk/v2/message.json?apikey=" . env('SMS_API_KEY');
    }

    /**
     * Send the message
     * @return bool
     */
    public function send()
    {
        $this->assertValid();
        $guzzle = new Client();
        $params = [
            'message' => [
                'sender' => $this->sender,
                'recipients' => $this->recipient,
                'message' => $this->message
            ]
        ];
        if ($this->flash){
            $params['message']['class'] = 0;
        }
        $response = $guzzle->post($this->url, [
            'json' => $params,
            'connect_timeout' => 120
        ]);
        return $response->getStatusCode() == 201;
    }

    /**
     * Get or set recipient phone number
     * @param null $number
     * @return $this
     */
    public function recipient($number = null)
    {
        if ($number == null){
            return $this->recipient;
        }
        $number = trim($number);
        if (!preg_match('/^(+45)?\d{8}$/', $number)){
            throw new \InvalidArgumentException("Invalid recipient");
        }
        if (strlen($number) == 8){
            $number = '+45' . $number;
        }
        $this->recipient = $number;
        return $this;
    }

    /**
     * Get or set Sender name
     * @param null $name
     * @return $this
     */
    public function sender($name = null)
    {
        if ($name == null){
            return $this->sender;
        }
        if (strlen($name) > 11){
            throw new \InvalidArgumentException("Sender must be 11 characters or less.");
        }
        $this->sender = $name;
        return $this;
    }

    /**
     * Get or set the message
     * @param null $message
     * @return $this
     */
    public function message($message = null)
    {
        if ($message == null){
            return $this->message;
        }
        $this->message = $this->sanitizeMessage($message);
        return $this;
    }

    /**
     * Send as Flash SMS
     * @return $this
     */
    public function flash()
    {
        $this->flash = true;
        return $this;
    }

    /**
     * @throws \Exception
     */
    private function assertValid()
    {
        if ($this->recipient === null){
            throw new \Exception("Recipient missing");
        }
        if ($this->sender === null){
            throw new \Exception("Sender missing");
        }
        if ($this->message === null){
            throw new \Exception("Message missing");
        }
    }

    /**
     * @param $message
     * @return mixed|string
     */
    private function sanitizeMessage($message)
    {
        // Attempt to convert html to plain text
        // Convert <br> to \r\n
        $message = preg_replace('/<br ?\/?>/', "\r\n", $message);
        // Remove all other tags
        $message = strip_tags($message);
        return $message;
    }
}