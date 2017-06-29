<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 29-06-2017
 * Time: 14:47
 */

namespace Findforsikring\Support;


/**
 * Class Number
 * @package Findforsikring\Support
 */
class Number
{
    /**
     * Formats a float value as Danish currency
     * @param $amount
     * @param string $prefix
     * @param null $postfix
     * @return string
     */
    public static function formatDanishKroner($amount, $prefix = 'kr. ', $postfix = null)
    {
        return ($prefix ?: '') . number_format($amount, 2, ',', '.') . ($postfix ?: '');
    }
}