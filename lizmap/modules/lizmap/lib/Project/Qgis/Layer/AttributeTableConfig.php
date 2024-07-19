<?php
/**
 * QGIS Vector layer attribute table config.
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
 * QGIS Vector layer attribute table config
 *
 * @property-read Array<AttributeTableColumn> $columns
 */
class AttributeTableConfig extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'columns',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'columns',
    );

    /**
     * Get vector layer attribute table config as key array
     *
     * @return array
     */
    function toKeyArray()
    {
        $data = array();
        if ($this->columns) {
            foreach($this->columns as $idx => $column) {
                if ($column->hidden) {
                    continue;
                }
                $data[] = array(
                    'index' => $idx,
                    'type' => $column->type,
                    'name' => $column->name,
                );
            }
        }
        return array(
            'columns' => $data
        );
    }

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'attributetableconfig';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'columns',
    );

    static protected $childParsers = array();
}
AttributeTableConfig::registerChildParser('columns', function($oXmlReader) {
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

        if ($oXmlReader->localName == 'column') {
            $data[] = AttributeTableColumn::fromXmlReader($oXmlReader);
        }
    }
    return $data;
});
