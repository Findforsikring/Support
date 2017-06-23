<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 23-06-2017
 * Time: 13:12
 */

namespace Findforsikring\Support;


use Carbon\Carbon;

class DateTime extends Carbon
{
    const DATE_DANISH_MEDIUM = "%A d. %e. %B";
    const DATE_DANISH_LONG = "%A d. %e. %B %Y";
    const DATETIME_DANISH_MEDIUM = "%A d. %e. %B kl. %H:%m";

    protected static $danishWeekdays = array(
        'mandag',
        'tirsdag',
        'onsdag',
        'torsdag',
        'fredag',
        'lørdag',
        'søndag'
    );

    protected static $danishMonths = array(
        'januar',
        'februar',
        'marts',
        'april',
        'maj',
        'juni',
        'juli',
        'august',
        'september',
        'oktober',
        'november',
        'december',
    );

    public function getDanishWeekday()
    {
        return static::$danishWeekdays[$this->format('N')];
    }

    public function getDanishMonth()
    {
        return static::$danishMonths[$this->format('n')];
    }

    public function getNextWeekday()
    {
        if ($this->isWeekday()){
            return $this;
        }
        // Weekdays are never more than 3 days away
        for ($i = 1; $i <= 3; $i++) {
            $candidate = (new static($this))->addDays($i);
            if ($candidate->isWeekday()){
                return $candidate;
            }
        }
        return null;
    }

    public function toDanishDate($includeYear = false)
    {
        static::setLocale('da');
        return $this->formatLocalized($includeYear ? self::DATE_DANISH_LONG : self::DATE_DANISH_MEDIUM);
    }

    public function toDanishDateTime()
    {
        static::setLocale('da');
        return $this->formatLocalized(self::DATETIME_DANISH_MEDIUM);
    }
}