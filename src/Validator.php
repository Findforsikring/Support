<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 26-06-2017
 * Time: 11:55
 */

namespace Findforsikring\Support;


/**
 * Applies custom validation rules
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
     * @return int
     */
    public function isValidDomainName()
    {
        return preg_match("/^([-a-z0-9æøå]{2,100})\.([a-z\.]{2,8})$/i", $this->value);
    }
}