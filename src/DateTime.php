<?php
/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 23-06-2017
 * Time: 13:12
 */

namespace Findforsikring\Support;


use Carbon\Carbon;
use Carbon\CarbonInterval;

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

    protected static $businessHours = ["08:00", "16:00"];
    protected static $businessOpenDuringWeekends = false;

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
            /** @var static $candidate */
            $candidate = (new static($this))->addDays($i);
            if ($candidate->isWeekday()){
                return $candidate;
            }
        }
        return null;
    }

    /**
     * Returns a date $hours business hours in the future
     * @param int $hours
     */
    public function addBusinessHours($hours)
    {
        $remainingMinutes = $hours * 60;
        $dateToProcess = $this;
        while ($remainingMinutes > 0){
            $endOfBusiness = $dateToProcess->getBusinessEnd();
            $minutesSpentThisDate = self::businessMinutesBetween($dateToProcess, $endOfBusiness);
            if ($remainingMinutes - $minutesSpentThisDate <= 0){
                return $dateToProcess->addMinutes($remainingMinutes);
            }
            $remainingMinutes -= $minutesSpentThisDate;
            $dateToProcess = $dateToProcess->addDay()->getBusinessStart();
        }
    }

    public function subtractBusinessHours($hours)
    {
        $remainingMinutes = $hours * 60;
        $dateToProcess = $this;
        while ($remainingMinutes > 0){
            $startOfBusiness = $dateToProcess->getBusinessStart();
            $minutesSpentThisDate = self::businessMinutesBetween($startOfBusiness, $dateToProcess);
            if ($remainingMinutes - $minutesSpentThisDate <= 0){
                return $dateToProcess->subMinutes($remainingMinutes);
            }
            $remainingMinutes -= $minutesSpentThisDate;
            $dateToProcess = $dateToProcess->subDay()->getBusinessEnd();
        }
    }

    public function lengthOfBusinessDayInMinutes()
    {
        return $this->getBusinessStart()->diffInMinutes($this->getBusinessEnd());
    }

    public static function businessMinutesBetween(\DateTime $from, \DateTime $to)
    {
        list($fromHour, $fromMinute) = explode(':', self::$businessHours[0]);
        list($toHour, $toMinute) = explode(':', self::$businessHours[1]);
        $weekends = self::$businessOpenDuringWeekends;
        if (!$from instanceof Carbon){
            $from = new Carbon($from->format("Y-m-d H:i:s"));
        }
        if (!$to instanceof Carbon){
            $to = new Carbon($to->format("Y-m-d H:i:s"));
        }
        return $from->diffFiltered(CarbonInterval::minute(), function (Carbon $dt) use ($fromHour, $fromMinute, $toHour, $toMinute, $weekends){
            // Return true if this minute should be counted
            if (!$weekends && $dt->isWeekend()){
                return false;
            }
            if ($dt->hour < $fromHour){
                return false;
            }
            if ($dt->hour == $fromHour){
                return $dt->minute <= $toMinute;
            }
            if ($dt->hour > $toHour){
                return false;
            }
            if ($dt->hour == $toHour){
                return $dt->minute <= $toMinute;
            }
            return true;
        }, $to);
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

    public static function setBusinessHours(array $businessHours)
    {
        self::$businessHours = $businessHours;
    }

    /**
     * @param bool $isOpen
     */
    public static function setBusinessOpenDuringWeekends($isOpen)
    {
        self::$businessOpenDuringWeekends = $isOpen;
    }

    public function getBusinessStart()
    {
        $start = explode(':', self::$businessHours[0]);
        return (clone $this)->setTime($start[0], $start[1]);
    }

    public function getBusinessEnd()
    {
        $end = explode(':', self::$businessHours[1]);
        return (clone $this)->setTime($end[0], $end[1]);
    }
}