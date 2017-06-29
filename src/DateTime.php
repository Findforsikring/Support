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
 * Helps converting dates and times into Danish readable strings
 *
 * @package Findforsikring\Support
 */
class DateTime extends Carbon
{
    /*
     * Format codes
     */
    const DATE_DANISH_SHORT = "%e. %B";
    const DATE_DANISH_MEDIUM = "%A d. %e. %B";
    const DATE_DANISH_LONG = "%A d. %e. %B %Y";
    const DATETIME_DANISH_MEDIUM = "%A d. %e. %B kl. %H:%M";

    /**
     * Names of Danish weekdays
     * @var array
     */
    protected static $danishWeekdays = [
        'søndag',
        'mandag',
        'tirsdag',
        'onsdag',
        'torsdag',
        'fredag',
        'lørdag',
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
        return static::$danishWeekdays[$this->format('w')];
    }

    /**
     * Get month string in Danish
     * @return string
     */
    public function getDanishMonth()
    {
        return static::$danishMonths[$this->format('n') - 1];
    }

    /**
     * If this date is not a weekday, return the following weekday
     * Otherwise return this
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
     * @param string $type
     * @return string
     */
    public function toDanishDate($type = 'medium')
    {
        setlocale(LC_TIME, 'da_DK.utf8');
        switch ($type){
            case 'short':
                $format = static::DATE_DANISH_SHORT;
                break;
            case 'medium':
                $format = static::DATE_DANISH_MEDIUM;
                break;
            case 'long':
                $format = static::DATE_DANISH_LONG;
                break;
            default:
                throw new \InvalidArgumentException("Unknown format: $type");
        }
        return $this->formatLocalized($format);
    }

    /**
     * Format as Danish date including time
     * @return string
     */
    public function toDanishDateTime()
    {
        setlocale(LC_TIME, 'da_DK.utf8');
        return $this->formatLocalized(self::DATETIME_DANISH_MEDIUM);
    }
}