<?php
/**
 * Created by PhpStorm.
 * User: morten
 * Date: 6/27/17
 * Time: 8:50 PM
 */

namespace Findforsikring\Support;


use DOMDocument;

/**
 * Class Converters
 * @package Findforsikring\Support
 */
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
     * @return string
     */
    public static function array2Xml(array $array, $xmlVersion = "1.0", $encoding = "utf-8")
    {
        $xml = new DOMDocument($xmlVersion, $encoding);
        $root = self::appendXml($array, $xml);
        $xml->appendChild($root);
        $xml->normalizeDocument();
        return trim($xml->saveXml());
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

    /**
     * Convert an XML string to associative array
     * @param $xml_string
     * @return mixed
     */
    public static function xml2Array($xml, $sxclass = 'SimpleXMLElement', $nsattr = false, $flags = null)
    {
        // Validate arguments first
        if(!is_string($sxclass) or empty($sxclass) or !class_exists($sxclass)){
            trigger_error('$sxclass must be a SimpleXMLElement or a derived class.', E_USER_WARNING);
            return false;
        }
        if(!is_string($xml) or empty($xml)){
            trigger_error('$xml must be a non-empty string.', E_USER_WARNING);
            return false;
        }
        // Load XML if URL is provided as XML
        if(preg_match('~^https?://[^\s]+$~i', $xml) || file_exists($xml)){
            $xml = file_get_contents($xml);
        }
        // Let's drop namespace definitions
        if(stripos($xml, 'xmlns=') !== false){
            $xml = preg_replace('~[\s]+xmlns=[\'"].+?[\'"]~i', null, $xml);
        }
        // I know this looks kind of funny but it changes namespaced attributes
        if(preg_match_all('~xmlns:([a-z0-9]+)=~i', $xml, $matches)){
            foreach(($namespaces = array_unique($matches[1])) as $namespace){
                $escaped_namespace = preg_quote($namespace, '~');
                $xml = preg_replace('~[\s]xmlns:'.$escaped_namespace.'=[\'].+?[\']~i', null, $xml);
                $xml = preg_replace('~[\s]xmlns:'.$escaped_namespace.'=["].+?["]~i', null, $xml);
                $xml = preg_replace('~([\'"\s])'.$escaped_namespace.':~i', '$1'.$namespace.'_', $xml);
            }
        }
        // Let's change <namespace:tag to <namespace_tag ns="namespace"
        $regexfrom = sprintf('~<([a-z0-9]+):%s~is', !empty($nsattr) ? '([a-z0-9]+)' : null);
        $regexto = strlen($nsattr) ? '<$1_$2 '.$nsattr.'="$1"' : '<$1_';
        $xml = preg_replace($regexfrom, $regexto, $xml);
        // Let's change </namespace:tag> to </namespace_tag>
        $xml = preg_replace('~</([a-z0-9]+):~is', '</$1_', $xml);
        // Default flags I use
        if(empty($flags)) $flags = LIBXML_COMPACT | LIBXML_NOBLANKS | LIBXML_NOCDATA;
        // Now load and return (namespaceless)
        $xml = simplexml_load_string($xml, $sxclass, $flags);
        return \GuzzleHttp\json_decode(\GuzzleHttp\json_encode($xml));
    }

    public static function xml2ArrayStripNamespaces($xml, $sxclass = 'SimpleXMLElement', $nsattr = false, $flags = null){

    }


}