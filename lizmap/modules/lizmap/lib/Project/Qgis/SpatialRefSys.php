<?php
/**
 * QGIS Spatial Reference System.
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
 * QGIS Spatial Reference System classes
 *
 * @property-read string      $authid
 * @property-read string      $proj4
 * @property-read int|null    $srid
 * @property-read string|null $description
 *
 * @phpstan-type SpatialRefSysData array{'authid': string, 'proj4'?: string, 'srid'?: int, 'description'?: string}
 */
class SpatialRefSys extends BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'authid',
        'proj4',
        'srid',
        'description',
    );

    /** @var Array<string> The not null properties*/
    protected $mandatoryProperties = array(
        'authid',
        'proj4',
    );

    /**
     * @var Array<SpatialRefSys> The stored instances
     *
     * @see SpatialRefSys::getInstance()
     */
    static private $instances = array(
    );

    /**
     * Get all Spatial Reference System instance stored
     *
     * @return Array<SpatialRefSys>
     */
    static public function allInstances()
    {
        return array_values(self::$instances);
    }

    /**
     * Get all Spatial Reference System instance stored
     *
     * @return Array<SpatialRefSys>
     */
    static public function clearInstances()
    {
        return self::$instances = array();
    }

    /**
     * Get a Spatial Reference System instance from an array.
     * if the `authid` is already stored, the Spatial Reference System in memory will be returned
     * else a new Spatial Reference System instance is constructed, stored and returned
     *
     * @param SpatialRefSysData $data An array describing Spatial Reference System
     *
     * @return SpatialRefSys the Spatial Reference System instance corresponding to the array
     */
    static public function getInstance($data)
    {
        if (array_key_exists($data['authid'], self::$instances)) {
            return self::$instances[$data['authid']];
        }
        $inst = new self($data);
        self::$instances[$inst->authid] = $inst;
        return $inst;
    }

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'spatialrefsys';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'authid',
        'proj4',
        'srid',
        'description',
    );

    /** @var Array<string> The XML element needed children */
    static protected $mandatoryChildren = array(
        'authid',
        'proj4',
    );
    /**
     * Parse from an XMLReader instance at a child of an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at a child of an element
     *
     * @return int|string the result of the parsing
     */
    static protected function parseChild($oXmlReader)
    {
        if ($oXmlReader->localName == 'srid') {
            return (int) $oXmlReader->readString();
        }
        return $oXmlReader->readString();
    }

    /**
     * Build and instance with data as an array
     *
     * @param Array $data the instance data
     *
     * @return SpatialRefSys the instance
     */
    static protected function buildInstance($data)
    {
        if (array_key_exists($data['authid'], self::$instances)) {
            return self::$instances[$data['authid']];
        }
        return self::getInstance($data);
    }
}
