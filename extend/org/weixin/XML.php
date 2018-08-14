<?php
namespace org\weixin;

class XML
{

    public static function xml2array($xml)
    {
        $xml = new \SimpleXMLElement($xml);
        if (! $xml) {
            error_log("[ERROR_XML] ");
            exit();
        }
        $data = array();
        foreach ($xml as $key => $value) {
            if ($value->count() > 0) {
                foreach ($value as $k => $v) {
                    $data[$k] = strval($v);
                }
            } else {
                $data[$key] = strval($value);
            }
        }
        return $data;
    }

    public static function array2xml($data, $item = 'item')
    {
        $xml=new \SimpleXMLElement('<xml></xml>');
        foreach ($data as $key => $value) {
            is_numeric($key) && $key = $item;
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                self::array2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
        return $xml;
    }
}

?>