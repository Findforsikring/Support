<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 26-06-2017
 * Time: 11:55
 */

namespace Findforsikring\Support;


/**
 * Class Validator
 * @package Findforsikring\Support
 */
class Validator
{
    /**
     * @var mixed value to validate
     */
    private $value;

    /**
     * Validator constructor.
     * @param $value
     */
    public function __construct($value){
        $this->value = $value;
    }

    /**
     * Check if domain name is valid
     * @return bool
     */
    public function isValidDomainName()
    {
        return (bool) preg_match("/^([-a-z0-9æøå]{2,100})\.([a-z\.]{2,8})$/i", $this->value);
    }

    /**
     * Checks if the domain name is set up for Mailgun
     * @return bool
     */
    public function isRegisteredMailgunDomain()
    {
        $txt_records = dns_get_record($this->value, DNS_TXT);
        foreach ($txt_records as $txt_record) {
            $txt = $txt_record['txt'];
            if (preg_match('/mailgun\.org/', $txt)){
                return true;
            }
        }
        return false;
    }
}