<?php

if (!function_exists('implode_danish_list')) {
    function implode_danish_list(array $input)
    {
        $output = "";
        for ($i = 0; $i < count($input); $i++) {
            if ($i == count($input) - 1) {
                $output .= " og ";
            } else if ($i > 0) {
                $output .= ", ";
            }
            $output .= $input[$i];
        }
        return $output;
    }
}
if (!function_exists('ordinal_suffix')) {
    function ordinal_suffix($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }
}
if (!function_exists('parse_danish_address')) {
    function parse_danish_address($address)
    {
        $regex = '/^([^0-9]*)([0-9]*)\s*([A-Z]?)[,\s]*([0-9]*)?(st|kl)?[\.,\s]*([a-zæøå]*)?(\d{0,4})?.*$/mi';
        preg_match_all($regex, $address, $matches);
        $result = array();
        for ($i = 1; $i < count($matches); $i++) {
            if (isset($matches[$i][0])) {
                $result[] = $matches[$i][0];
            }
        }
        $cleaned = array();
        $components = count($result);
        if ($components > 0) {
            $cleaned['street'] = trim($result[0]);
        }
        if ($components > 1) {
            $cleaned['number'] = $result[1];
        }
        if ($components > 2) {
            $cleaned['letter'] = $result[2];
        }
        if ($components > 3) {
            $cleaned['floor'] = $result[3] == '' ? $result[4] : $result[3];
        }
        if ($components > 5) {
            $cleaned['door'] = $result[5];
        }
        foreach ($cleaned as $name => $value) {
            if (is_null($value) || $value == '') {
                unset($cleaned[$name]);
            }
        }
        return $cleaned;
    }

    if (!function_exists('sms')) {
        /**
         * Danish phone number
         * @param string $recipient
         *
         * Sender name to display to the recipient
         * Max 11 characters, no spaces
         * @param string $sender
         *
         * Plaintext message
         * Use \r\n for newlines
         * @todo add validation rules
         * @param $message
         *
         * Send as Flash SMS
         * The SMS will be displayed in a modal and not stored in history
         * @param bool $flash
         *
         * Optional endpoint that will receive status updates
         * @todo add validation rules
         * @param null $statusUrl
         *
         * Optional return data which will be included in status updates
         * @todo add validation rules
         * @param null $returnData
         *
         * Return value indicates only if the SMS was accepted by the provider
         * To know whether or not it is received, use status URL and unique return data
         * @return bool
         */
        function sms($recipient, $sender, $message, $flash = false, $statusUrl = null, $returnData = null)
        {
            $sms = new \Findforsikring\Support\SMS();
            return $sms->recipient($recipient)
                       ->sender($sender)
                       ->message($message)
                       ->flash($flash)
                       ->statusUrl($statusUrl)
                       ->returnData($returnData)
                       ->send();
        }

        if (!function_exists('dump')) {
            /**
             * Dumps a variable ot the browser as plaintext using print_r()
             * @param $variable
             */
            function dump($variable)
                {
                    echo '<pre>';
                    print_r($variable);
                }
            }
    }
}