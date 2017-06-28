<?php
use Findforsikring\Support\DateTime;

class DateTimeTest extends \PHPUnit\Framework\TestCase
{
    public function testDanishWeekdays()
    {
        $date = new DateTime('2017-06-25'); // A Sunday
        $expected = [
            'mandag',
            'tirsdag',
            'onsdag',
            'torsdag',
            'fredag',
            'lørdag',
            'søndag'
        ];
        for ($i = 0; $i < count($expected); $i++) {
            $date->addDays(1);
            $this->assertEquals($date->getDanishWeekday(), $expected[$i]);
        }
    }

    public function testDanishMonths()
    {
        $date = new DateTime("2016-12-01");
        $expected = [
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
        for ($i = 0; $i < count($expected); $i++) {
            $date->addMonth(1);
            $this->assertEquals($date->getDanishMonth(), $expected[$i]);
        }
    }

    public function testNextWeekday()
    {
        $expected = [
            '2017-06-26' => '2017-06-26', // Monday => Monday
            '2017-06-27' => '2017-06-27', // Tuesday => Tuesday
            '2017-06-28' => '2017-06-28', // Wednesday => Wednesday
            '2017-06-29' => '2017-06-29', // Thursday => Thursday
            '2017-06-30' => '2017-06-30', // Friday => Friday
            '2017-07-01' => '2017-07-03', // Saturday => Monday
            '2017-07-02' => '2017-07-03', // Sunday => Monday
        ];
        foreach ($expected as $from => $to) {
            $date = new DateTime($from);
            $this->assertEquals($date->getNextWeekday()->format("Y-m-d"), $to);
        }
    }

    public function testReadableDateTimes()
    {
        $datetime = new DateTime("2017-06-28 10:48:55");
        $expectedMedium = "onsdag d. 28. juni";
        $expectedLong = "onsdag d. 28. juni 2017";
        $expectedWithTime = "onsdag d. 28. juni kl. 10:48";
        $this->assertEquals($datetime->toDanishDate(false), $expectedMedium);
        $this->assertEquals($datetime->toDanishDate(true), $expectedLong);
        $this->assertEquals($datetime->toDanishDateTime(), $expectedWithTime);
    }
}
