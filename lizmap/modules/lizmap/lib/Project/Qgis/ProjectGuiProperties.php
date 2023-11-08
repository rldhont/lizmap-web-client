<?php
/**
 * QGIS Project Gui Properties.
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
 * QGIS Project Gui Properties class
 *
 * @property-read int      $CanvasColorBluePart
 * @property-read int      $CanvasColorGreenPart
 * @property-read int      $CanvasColorRedPart
 * @property-read int|null $SelectionColorAlphaPart
 * @property-read int|null $SelectionColorBluePart
 * @property-read int|null $SelectionColorGreenPart
 * @property-read int|null $SelectionColorRedPart
 */
class ProjectGuiProperties extends BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'CanvasColorBluePart',
        'CanvasColorGreenPart',
        'CanvasColorRedPart',
        'SelectionColorAlphaPart',
        'SelectionColorBluePart',
        'SelectionColorGreenPart',
        'SelectionColorRedPart',
        // 'Identify', // it contains disabledLayers
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'CanvasColorBluePart',
        'CanvasColorGreenPart',
        'CanvasColorRedPart',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'Gui';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'CanvasColorBluePart',
        'CanvasColorGreenPart',
        'CanvasColorRedPart',
        'SelectionColorAlphaPart',
        'SelectionColorBluePart',
        'SelectionColorGreenPart',
        'SelectionColorRedPart',
        // 'Identify', // it contains disabledLayers
    );

    /** @var Array<string> The XML element needed children */
    static protected $mandatoryChildren = array(
        'CanvasColorBluePart',
        'CanvasColorGreenPart',
        'CanvasColorRedPart',
    );

    /**
     * Get the canvas color as RGB string
     *
     * @return string The variables key / value array
     */
    function getCanvasColor()
    {
        return 'rgb('.$this->CanvasColorRedPart.', '.$this->CanvasColorGreenPart.', '.$this->CanvasColorBluePart.')';
    }

    /**
     * Parse from an XMLReader instance at a child of an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at a child of an element
     *
     * @return Array|int|string the result of the parsing
     */
    static protected function parseChild($oXmlReader)
    {
        $type = $oXmlReader->getAttribute('type');
        if ($type == 'QStringList') {
            if (!$oXmlReader->isEmptyElement) {
                return Project\QgisProjectParser::readValues($oXmlReader);
            }
            return array();
        }

        if ($type == 'int') {
            return (int) $oXmlReader->readString();
        }
        return $oXmlReader->readString();
    }
}
