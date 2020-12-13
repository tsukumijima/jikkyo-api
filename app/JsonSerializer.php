<?php

namespace App;

use SimpleXmlElement;
use JsonSerializable;

class JsonSerializer extends SimpleXmlElement implements JsonSerializable
{
    const CONTENT_NAME = "content";

    /**
     * SimpleXMLElement JSON serialization
     * https://stackoverflow.com/a/31273676 を参考に一部改変
     *
     * @return array
     *
     * @link http://php.net/JsonSerializable.jsonSerialize
     * @see JsonSerializable::jsonSerialize
     * @see https://stackoverflow.com/a/31276221/36175
     */
    function jsonSerialize()
    {
        $array = [];

        if ($this->count()) {
            // serialize children if there are children
            /**
             * @var string $tag
             * @var JsonSerializer $child
             */
            foreach ($this as $tag => $child) {
                $temp = $child->jsonSerialize();
                $attributes = [];

                foreach ($child->attributes() as $name => $value) {
                    $attributes["$name"] = (string) $value;
                }

                $array[][$tag] = array_merge($attributes, $temp);
            }
        } else {
            // serialize attributes and text for a leaf-elements
            $temp = (string) $this;

            // if only contains empty string, it is actually an empty element
            if (trim($temp) !== "") {
                $array[self::CONTENT_NAME] = $temp;
            }
        }

        if ($this->xpath('/*') == array($this)) {
            // the root element needs to be named
            $array = [$this->getName() => $array];
        }

        return $array;
    }
}
