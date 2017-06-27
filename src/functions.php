<?php
/**
 * Helper functions
 * @global
 */
if (!function_exists('implode_danish_list')) {
    if (!function_exists('is_assoc')) {
        /**
         * @param array $array
         * @return bool
         */
        function is_assoc(array $array)
        {
            // Check if the keys are a zero-based index
            return array() === $array ? false : array_keys($array) !== range(0, count($array) - 1);
        }
    }
    /**
     * Returns a string with the elements concatenated as:
     * 1 element: "Element1"
     * 2 elements: "Element1 og Element2"
     * 3 or more elements: "Element1, Element2 og Element3"
     * @param array $input
     * @return string
     */
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
    /**
     * Adds an ordinal suffix to a number
     * Examples: 1 => 1st, 2 => 2nd, 3 => 3rd
     *
     * @param $number
     * @return string
     */
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
    /**
     * Parses a danish address into these components (if possible)
     * Street, Number, Letter, Floor, Door
     *
     * @param $address
     * @return array
     */
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
         * Sends and SMS to a Danish recipient
         *
         * @param string $recipient Phone number
         *
         * @param string $sender Sender name to display to the recipient
         *                       Max 11 characters, no spaces
         *
         * @todo add validation rules
         * @param $message Plaintext message
         *                 Use \r\n for newlines
         *
         * @param bool $flash Send as Flash SMS
         *                    The SMS will be displayed in a modal and not stored in history
         *
         * @todo add validation rules
         * @param null $statusUrl Optional endpoint that will receive status updates
         *
         * @todo add validation rules
         *
         * @param null $returnData Optional return data which will be included in status updates
         *
         * @return bool Return value indicates only if the SMS was accepted by the provider
         *              To know whether or not it is received, use status URL and unique return data
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
    if (!function_exists('danish_holidays')) {
        /**
         * Returns an associative array
         * Key: Name of the holiday
         * Value: Date string (Y-m-d), defaults to current year
         *
         * @param null $year
         * @return array
         */
        function danish_holidays($year = null)
        {
            if (is_null($year)) {
                $year = (int)date('Y');
            }
            $dayLength = 24 * 60 * 60; // seconds
            $easter = mktime(0, 0, 0, 3, (21 + (easter_days($year))), $year);
            $holidays = [];
            $holidays['Nytårsdag'] = date('Y-m-d', mktime(0, 0, 0, 1, 1, $year));
            $holidays['Palmesøndag'] = date('Y-m-d', $easter - (6.5 * $dayLength));
            $holidays['Skærtorsdag'] = date('Y-m-d', $easter - (3 * $dayLength));
            $holidays['Langfredag'] = date('Y-m-d', $easter - (2 * $dayLength));
            $holidays['Påskedag'] = date('Y-m-d', $easter);
            $holidays['2. påskedag'] = date('Y-m-d', $easter + (1 * $dayLength));
            $holidays['Store bededag'] = date('Y-m-d', $easter + (26 * $dayLength));
            $holidays['Kristi himmelfart'] = date('Y-m-d', $easter + (39 * $dayLength));
            $holidays['Pinsedag'] = date('Y-m-d', $easter + (49 * $dayLength));
            $holidays['2. pinsedag'] = date('Y-m-d', $easter + (50 * $dayLength));
//		$holidays['Grundlovsdag'] = date('Y-m-d', mktime(0, 0, 0, 6, 5, $year));
            $holidays['Juleaften'] = date('Y-m-d', mktime(0, 0, 0, 12, 24, $year));
            $holidays['Juledag'] = date('Y-m-d', mktime(0, 0, 0, 12, 25, $year));
            $holidays['2. juledag'] = date('Y-m-d', mktime(0, 0, 0, 12, 26, $year));
            $holidays['Nytårsaften'] = date('Y-m-d', mktime(0, 0, 0, 12, 31, $year));
            return $holidays;
        }
    }
}