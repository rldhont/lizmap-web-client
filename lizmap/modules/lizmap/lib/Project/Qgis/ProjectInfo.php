<?php
/**
 * QGIS Project
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis;

use Lizmap\Project;

/**
 * QGIS Project info class
 *
 * @property-read string            $version
 * @property-read string            $projectname
 * @property-read string            $title
 * @property-read SpatialRefSys     $projectCrs
 * @property-read ProjectProperties $properties
 */
class ProjectInfo extends BaseQgisXmlObject
{
    protected $properties = array(
        'version',
        'projectname',
        'saveDateTime',
        'title',
        'projectCrs',
        'properties',
        'layer-tree-group',
        'visibility-presets',
        'relations',
        'projectlayers',
        'Layouts',
    );

    protected $mandatoryProperties = array(
        'version',
        'projectname',
        'title',
        'projectCrs',
        'properties',
    );

    static protected $children = array(
        'title',
        'projectCrs',
        'properties',
    );

    static protected $mandatoryChildren = array(
        'title',
        'projectCrs',
        'properties',
    );

    /**
     * Get the visibility presets as key array
     *
     * @return array
     */
    function getVisibilityPresetsAsKeyArray()
    {
        $data = array();
        foreach ($this->{'visibility-presets'} as $visibilityPreset) {
            $data[$visibilityPreset->name] = $visibilityPreset->toKeyArray();
        }
        return $data;
    }

    /**
     * Get proj as key array
     *
     * @return array
     */
    function getProjAsKeyArray()
    {
        $data = array();
        $data[$this->projectCrs->authid] = $this->projectCrs->proj4;
        foreach ($this->projectlayers as $layer) {
            if ($layer->embedded) {
                continue;
            }
            $data[$layer->srs->authid] = $layer->srs->proj4;
        }
        return $data;
    }

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'qgis';

    /**
     * Get attributes from an XMLReader instance at an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at an element
     *
     * @return Array{'version': string, 'projectname': string} the element attributes as keys / values
     */
    static protected function getAttributes($oXmlReader)
    {
        return array(
            'version' => $oXmlReader->getAttribute('version'),
            'projectname' => $oXmlReader->getAttribute('projectname'),
            'saveDateTime' => $oXmlReader->getAttribute('saveDateTime'),
        );
    }

    /**
     * Parse from an XMLReader instance at a child of an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at a child of an element
     *
     * @return Array|boolean|int|string the result of the parsing
     */
    static protected function parseChild($oXmlReader)
    {
    }

    static protected $childParsers = array();
}

ProjectInfo::registerChildParser('title', function($oXmlReader) {
    return $oXmlReader->readString();
});
ProjectInfo::registerChildParser('projectCrs', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
    if ($oXmlReader->isEmptyElement) {
        return null;
    }
    while ($oXmlReader->read()) {
        if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
            && $oXmlReader->localName == $localName
            && $oXmlReader->depth == $depth) {
            break;
        }

        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            continue;
        }

        if ($oXmlReader->depth != $depth + 1) {
            continue;
        }

        if ($oXmlReader->localName == 'spatialrefsys') {
            break;
        }
    }
    return SpatialRefSys::fromXmlReader($oXmlReader);
});
ProjectInfo::registerChildParser('properties', function($oXmlReader) {
    return ProjectProperties::fromXmlReader($oXmlReader);
});
ProjectInfo::registerChildParser('layer-tree-group', function($oXmlReader) {
    return LayerTreeRoot::fromXmlReader($oXmlReader);
});
ProjectInfo::registerChildParser('visibility-presets', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    while ($oXmlReader->read()) {

        if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
            && $oXmlReader->localName == $localName
            && $oXmlReader->depth == $depth) {
            break;
        }

        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            continue;
        }

        if ($oXmlReader->depth != $depth + 1) {
            continue;
        }

        if ($oXmlReader->localName == 'visibility-preset') {
            $data[] = ProjectVisibilityPreset::fromXmlReader($oXmlReader);
        }
    }

    return $data;
});
ProjectInfo::registerChildParser('relations', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    while ($oXmlReader->read()) {

        if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
            && $oXmlReader->localName == $localName
            && $oXmlReader->depth == $depth) {
            break;
        }

        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            continue;
        }

        if ($oXmlReader->depth != $depth + 1) {
            continue;
        }

        if ($oXmlReader->localName == 'relation') {
            $data[] = ProjectRelation::fromXmlReader($oXmlReader);
        }
    }

    return $data;
});
ProjectInfo::registerChildParser('projectlayers', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    while ($oXmlReader->read()) {

        if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
            && $oXmlReader->localName == $localName
            && $oXmlReader->depth == $depth) {
            break;
        }

        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            continue;
        }

        if ($oXmlReader->depth != $depth + 1) {
            continue;
        }

        if ($oXmlReader->localName == 'maplayer') {
            $data[] = Layer\MapLayer::fromXmlReader($oXmlReader);
        }
    }

    return $data;
});
ProjectInfo::registerChildParser('Layouts', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    while ($oXmlReader->read()) {

        if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
            && $oXmlReader->localName == $localName
            && $oXmlReader->depth == $depth) {
            break;
        }

        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            continue;
        }

        if ($oXmlReader->depth != $depth + 1) {
            continue;
        }

        if ($oXmlReader->localName == 'Layout') {
            $data[] = Layout\Layout::fromXmlReader($oXmlReader);
        }
    }

    return $data;
});
