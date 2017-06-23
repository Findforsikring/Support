<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 23-06-2017
 * Time: 13:12
 */

namespace Findforsikring\Support;


use Carbon\Carbon;

/**
 * Class DateTime
 * Helps converting dates and times into Danish readable strings
 *
 * @package Findforsikring\Support
 */
class DateTime extends Carbon
{
    /*
     * Format codes
     */
    const DATE_DANISH_MEDIUM = "%A d. %e. %B";
    const DATE_DANISH_LONG = "%A d. %e. %B %Y";
    const DATETIME_DANISH_MEDIUM = "%A d. %e. %B kl. %H:%m";

    /**
     * Names of Danish weekdays
     * @var array
     */
    protected static $danishWeekdays = [
        'mandag',
        'tirsdag',
        'onsdag',
        'torsdag',
        'fredag',
        'lørdag',
        'søndag'
    ];

    /**
     * Names of Danish months
     * @var array
     */
    protected static $danishMonths = [
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
    ];

    /**
     * Get weekday string in Danish
     * @return string
     */
    public function getDanishWeekday()
    {
        return static::$danishWeekdays[$this->format('N')];
    }

    /**
     * Get month string in Danish
     * @return string
     */
    public function getDanishMonth()
    {
        return static::$danishMonths[$this->format('n')];
    }

    /**
     * Finds the following weekday
     * @return static|null
     */
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

    /**
     * Format as Danish date
     * @param bool $includeYear
     * @return string
     */
    public function toDanishDate($includeYear = false)
    {
        static::setLocale('da');
        return $this->formatLocalized($includeYear ? self::DATE_DANISH_LONG : self::DATE_DANISH_MEDIUM);
    }

    /**
     * Format as Danish date including time
     * @return string
     */
    public function toDanishDateTime()
    {
        static::setLocale('da');
        return $this->formatLocalized(self::DATETIME_DANISH_MEDIUM);
    }
}