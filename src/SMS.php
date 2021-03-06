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
 */
class SMS
{
    /*
     * Example:
     * $sms = new Findforsikring\Support\SMS
     * $sms->sender("Forsikring")->recipient("61693354")->message("Wazzup?")->send();
     */

    /**
     * API endpoint
     * @var string
     */
    private $url;
    /**
     * Recipient phone number
     * @var string
     */
    private $recipient;
    /**
     * Sender name or phone number
     * @var string
     */
    private $sender;
    /**
     * Message content
     * @var string
     */
    private $message;

    /**
     * Status URL endpoint
     * @var string
     */
    private $statusUrl;

    /**
     * Unique identifier
     * @var string
     */
    private $returnData;
    /**
     * Send as flash SMS
     * @var bool
     */
    private $flash = false;

    /**
     * SMS constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!array_key_exists('SMS_API_KEY', $_ENV)) {
            throw new \Exception("SMS_API_KEY missing in .env");
        }
        $this->url = "http://api.linkmobility.dk/v2/message.json?apikey=" . $_ENV['SMS_API_KEY'];
    }

    /**
     * Hej
     * Send the message
     * @return bool Whether or not the SMS was accepted by the provider
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
        if ($this->statusUrl) {
            $params['message']['statusurl'] = 0;
        }
        if ($this->returnData) {
            $params['message']['status'] = true;
            $params['message']['returndata'] = $this->returnData;
        }
        if ($this->flash) {
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
     * @param string $number Danish phone number
     * @return $this
     */
    public function recipient($number)
    {
        $number = trim($number);
        if (!preg_match('/^(\+45)?\d{8}$/', $number)) {
            throw new \InvalidArgumentException("Invalid recipient");
        }
        if (strlen($number) == 8) {
            $number = '+45' . $number;
        }
        $this->recipient = $number;
        return $this;
    }

    /**
     * Set Sender name
     * @param string $name Max 11 characters, no spaces
     * @return $this
     */
    public function sender($name)
    {
        if (strlen($name) > 11) {
            throw new \InvalidArgumentException("Sender must be 11 characters or less.");
        }
        $this->sender = $name;
        return $this;
    }

    /**
     * Set the message
     * @param string $message SMS content
     * @return $this
     */
    public function message($message)
    {
        $this->message = $this->sanitizeMessage($message);
        return $this;
    }

    /**
     * Set the status URL
     * This URL will receive a POST requestwhen the SMS changes status
     * Possible statuses: pending, received, rejected
     *
     * @param string $statusUrl Endpoint for status updates
     * @return SMS
     */
    public function statusUrl($statusUrl)
    {
        $this->statusUrl = $statusUrl;
        return $this;
    }

    /**
     * Get or set the return data
     * This string will be included in status updates
     *
     * @param string $returnData Unique identifier
     * @return SMS
     */
    public function returnData($returnData)
    {
        $this->returnData = $returnData;
        return $this;
    }

    /**
     * Send as Flash SMS
     * @param bool $flash
     * @return $this
     */
    public function flash($flash = true)
    {
        $this->flash = $flash;
        return $this;
    }

    /**
     * Throws an exception if mandatory data is missing
     * @throws \Exception
     */
    private function assertValid()
    {
        if ($this->recipient === null) {
            throw new \Exception("Recipient missing");
        }
        if ($this->sender === null) {
            throw new \Exception("Sender missing");
        }
        if ($this->message === null) {
            throw new \Exception("Message missing");
        }
    }

    /**
     * Attempts to convert html to plaintext
     * @param $message
     * @return string
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