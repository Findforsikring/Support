<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 29-06-2017
 * Time: 14:47
 */

namespace Findforsikring\Support;


class Number
{
    public static function formatDanishKroner($amount, $prefix = 'kr. ', $postfix = null)
    {
        return ($prefix ?: '') . number_format($amount, 2, ',', '.') . ($postfix ?: '');
    }
}