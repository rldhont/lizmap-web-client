<?php
/**
 * QGIS Map layer.
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis\Layer;

use Lizmap\Project;

/**
 * QGIS Map layer
 *
 * @property-read string               $id
 * @property-read boolean              $embedded
 * @property-read string               $type
 * @property-read string               $layername
 * @property-read SpatialRefSys        $srs
 * @property-read string               $datasource
 * @property-read string               $provider
 * @property-read MapLayerStyleManager $styleManager
 * @property-read string|null          $shortname
 * @property-read string|null          $title
 * @property-read string|null          $abstract
 * @property-read Array<string>|null   $keywordList
 */
class MapLayer extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'id',
        'embedded',
        'type',
        'layername',
        'srs',
        'datasource',
        'provider',
        'styleManager',
        'shortname',
        'title',
        'abstract',
        'keywordList',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'id',
        'embedded',
        'type',
        'layername',
        'srs',
        'datasource',
        'provider',
        'styleManager',
    );

    /**
     * Get map layer as key array
     *
     * @return array
     */
    function toKeyArray()
    {
        return array(
            'type' => $this->type,
            'id' => $this->id,
            'name' => $this->layername,
            'shortname' => $this->shortname !== null ? $this->shortname : '',
            'title' => $this->title !== null ? $this->title : $this->layername,
            'abstract' => $this->abstract !== null ? $this->abstract : '',
            'proj4' => $this->srs->proj4,
            'srid' => $this->srs->srid,
            'authid' => $this->srs->authid,
            'datasource' => $this->datasource,
            'provider' => $this->provider,
            'keywords' => $this->keywordList !== null ? $this->keywordList : array(),
        );
    }

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'maplayer';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'id',
        'layername',
        'shortname',
        'title',
        'abstract',
        'srs',
        'datasource',
        'provider',
        'keywordList',
        'previewExpression',
    );

    static protected $childParsers = array();

    static protected function getAttributes($oXmlReader)
    {
        // The maplayer can reference an embeded layer
        $embedded = filter_var($oXmlReader->getAttribute('embedded'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($embedded) {
            return array(
                'id' => $oXmlReader->getAttribute('id'),
                'embedded' => true,
                'project' => $oXmlReader->getAttribute('project'),
            );
        }
        return array(
            'type' => $oXmlReader->getAttribute('type'),
            'embedded' => false,
        );
    }

    static protected function buildInstance($data)
    {
        if (array_key_exists('embedded', $data)
            && $data['embedded']) {
            return new EmbeddedLayer($data);
        }
        if (array_key_exists('map-layer-style-manager', $data)) {
            $data['styleManager'] = $data['map-layer-style-manager'];
            unset($data['map-layer-style-manager']);
        }
        if (array_key_exists('type', $data)
            && $data['type'] === 'vector') {
            return new VectorLayer($data);
        }
        return new MapLayer($data);
    }
}
MapLayer::registerChildParser('keywordList', function($oXmlReader) {
    return Project\QgisProjectParser::readValues($oXmlReader);
});
MapLayer::registerChildParser('srs', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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
    return Project\Qgis\SpatialRefSys::fromXmlReader($oXmlReader);
});
MapLayer::registerChildParser('map-layer-style-manager', function($oXmlReader) {
    return MapLayerStyleManager::fromXmlReader($oXmlReader);
});
MapLayer::registerChildParser('fieldConfiguration', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'field') {
            $data[] = VectorLayerField::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('aliases', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'alias') {
            $data[] = VectorLayerAlias::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('constraints', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'constraint') {
            $data[] = VectorLayerConstraint::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('constraintExpressions', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'constraint') {
            $data[] = VectorLayerConstraintExpression::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('defaults', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'default') {
            $data[] = VectorLayerDefault::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('editable', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'field') {
            $data[] = VectorLayerEditableField::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('vectorjoins', function($oXmlReader) {
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
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

        if ($oXmlReader->localName == 'join') {
            $data[] = VectorLayerJoin::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
MapLayer::registerChildParser('attributetableconfig', function($oXmlReader) {
    return AttributeTableConfig::fromXmlReader($oXmlReader);
});
MapLayer::registerChildParser('excludeAttributesWFS', function($oXmlReader) {
    return Project\QgisProjectParser::readAttributes($oXmlReader);
});
MapLayer::registerChildParser('excludeAttributesWMS', function($oXmlReader) {
    return Project\QgisProjectParser::readAttributes($oXmlReader);
});
