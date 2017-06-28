<?php

/**
 * Created by PhpStorm.
 * User: Morten
 * Date: 28-06-2017
 * Time: 11:15
 */
class Array2XmlTest extends \PHPUnit\Framework\TestCase
{
    public function testWithHtmlStructure()
    {
        $array = [
            'html' => [
                'head' => [
                    'title' => 'MyTitle',
                    'style' => 'html,body{background-color:#eee;}'
                ],
                'body' => [
                    'div' => [
                        'attr' => ['class' => 'wrapper', 'id' => 'page-wrap'],
                        'span' => 'Hello world'
                    ],
                    'script' => 'console.log("Hello world");'
                ]
            ]
        ];
        $xml = \Findforsikring\Support\Converters::array2Xml($array);
        $expected = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<html><head><title>MyTitle</title><style>html,body{background-color:#eee;}</style></head><body><div class=\"wrapper\" id=\"page-wrap\"><span>Hello world</span></div><script>console.log(\"Hello world\");</script></body></html>";
        $this->assertEquals($expected, $xml);
    }
}