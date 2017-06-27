<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 6/27/17
 * Time: 8:50 PM
 */

namespace Findforsikring\Support;


use DOMDocument;

class Converters
{

    /**
     * Turns an associative array into xml
     * Add a key named 'attr' to add attributes to the parent element
     * Example:
     * [
     *    'div' => [
     *       'attr' => [
     *          'width' => '100%',
     *          'height' => '50px',
     *          'style' => 'color: red'
     *       ],
     *       'div' => [...]
     *    ]
     * ]
     * Results in
     * <div width="100%" height="50px" style="color: red">
     *    <div>...</div>
     * </div>
     * @param array $array
     * @param string $xmlVersion
     * @param string $encoding
     */
    public static function array2Xml(array $array, $xmlVersion = "1.0", $encoding = "utf-8")
    {
        $xml = new DOMDocument($xmlVersion, $encoding);
        $root = self::appendXml($array, $xml);
        $xml->appendChild($root);
        $xml->normalizeDocument();
        echo $xml->saveXml();
    }

    /**
     * Appends child nodes recursively
     * @param array $data
     * @param DOMDocument $document
     * @param \DOMElement|null $parent
     * @return \DOMElement
     */
    private static function appendXml(array $data, DOMDocument &$document, \DOMElement &$parent = null)
    {
        foreach ($data as $k => $v) {
            $node = $document->createElement($k);
            if ($k === 'attr') {
                // Don't append anything, just add the attributes to the parent and continue
                if (is_assoc($v)) {
                    foreach ($v as $name => $value) {
                        $parent->setAttribute($name, $value);
                    }
                } else {
                    $parent->setAttribute($v[0], $v[1]);
                }
                continue;
            }
            if (is_array($v)) {
                self::appendXml($v, $document, $node);
            } else {
                $text = $document->createTextNode($v);
                $node->appendChild($text);
            }
            if ($parent === null) {
                $parent = $node;
            } else {
                $parent->appendChild($node);
            }
        }
        return $parent;
    }
}