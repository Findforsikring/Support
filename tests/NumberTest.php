<?php

/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 29-06-2017
 * Time: 14:52
 */
class NumberTest extends \PHPUnit\Framework\TestCase
{
    public function testDanishKroner()
    {
        $tests = [
            [0.75, "kr. 0,75"],
            [1254768.25, "kr. 1.254.768,25"]
        ];
        foreach ($tests as $test) {
            $converted = \Findforsikring\Support\Number::formatDanishKroner($test[0]);
            $this->assertEquals($converted, $test[1]);
        }
    }
}