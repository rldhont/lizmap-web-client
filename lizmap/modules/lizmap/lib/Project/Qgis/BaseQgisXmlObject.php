<?php
/**
 * Base QGIS XML Object.
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis;

/**
 * Base QGIS XML Object to be extended
 */
class BaseQgisXmlObject extends BaseQgisObject
{
    /** @var string The XML element local name */
    static protected $qgisLocalName = 'qgis';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array();

    /** @var Array<string> The XML element needed children */
    static protected $mandatoryChildren = array();

    /**
     * Get an QGIS object instance from an XMLReader instance at an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at an element
     *
     * @return BaseQgisObject the QGIS object instance corresponding to the element
     */
    static public function fromXmlReader($oXmlReader)
    {
        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            throw new \Exception('Provide an XMLReader::ELEMENT!');
        }
        if ($oXmlReader->localName != static::$qgisLocalName) {
            throw new \Exception('Provide a `'.static::$qgisLocalName.'` element not `'.$oXmlReader->localName.'`!');
        }

        $depth = $oXmlReader->depth;
        $data = static::getAttributes($oXmlReader);

        if ($oXmlReader->isEmptyElement) {
            return static::buildInstance($data);
        }

        while ($oXmlReader->read()) {

            if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
                && $oXmlReader->localName == static::$qgisLocalName
                && $oXmlReader->depth == $depth) {
                break;
            }

            if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
                continue;
            }

            if ($oXmlReader->depth != $depth + 1) {
                continue;
            }

            $tagName = $oXmlReader->localName;
            $value = null;

            $childParserKeys = array();
            if (static::$childParsers !== null) {
                $childParserKeys = array_keys(static::$childParsers);
            }
            if (!empty($childParserKeys)
                && in_array($tagName, $childParserKeys)) {
                $value = call_user_func(static::$childParsers[$tagName], $oXmlReader);
            }

            if ($value === null
                && !empty(static::$children)
                && in_array($tagName, static::$children)) {
                $value = static::parseChild($oXmlReader);
            }

            if ($value === null
                && static::$children !== null
                && empty(static::$children)) {
                $value = static::parseChild($oXmlReader);
            }

            if ($value !== null) {
                if (!array_key_exists($tagName, $data)) {
                    $data[$tagName] = $value;
                } else if (is_array($data[$tagName])) {
                    $data[$tagName] = array_merge($data[$tagName], $value);
                }
            }
        }
        if (count(array_diff_key(array_flip(static::$mandatoryChildren), $data)) !== 0) {
            $exStr = '`'.static::$qgisLocalName.'` element has to contain `'.implode('`, `', static::$mandatoryChildren).'` elements!';
            if (static::$childParsers !== null) {
                $exStr .= ' `'.implode('`, `', array_keys(static::$childParsers)).'` parsers provided!';
            }
            $exStr .= ' Missing elements: '.implode(', ', array_keys(array_diff_key(array_flip(static::$mandatoryChildren), $data))).'!';
            throw new \Exception($exStr);
        }
        return static::buildInstance($data);
    }

    /**
     * Get attributes from an XMLReader instance at an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at an element
     *
     * @return Array the element attributes as keys / values
     */
    static protected function getAttributes($oXmlReader)
    {
        return array();
    }

    static protected $childParsers = null;

    static public function registerChildParser($localName, $parser)
    {
        if (static::$childParsers === null) {
            throw new \Exception("Not available register child parser!");
        }
        if (!is_callable($parser)) {
            throw new \Exception("Not callable parser '$localName'.");
        }
        static::$childParsers[$localName] = $parser;
    }

    static public function unRegisterChildParser($localName, $parser)
    {
        if (isset(static::$childParsers[$localName])) {
            unset(static::$childParsers[$localName]);
            return true;
        }
        return false;
    }

    /**
     * Parse from an XMLReader instance at a child of an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at a child of an element
     *
     * @return mixed the result of the parsing
     */
    static protected function parseChild($oXmlReader)
    {
        return $oXmlReader->readString();
    }

    /**
     * Build and instance with data as an array
     *
     * @param Array $data the instance data
     *
     * @return BaseQgisObject the instance
     */
    static protected function buildInstance($data)
    {
        return new static($data);
    }
}
