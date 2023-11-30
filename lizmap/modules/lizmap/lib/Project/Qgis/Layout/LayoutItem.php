<?php
/**
 * QGIS Layout item.
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis\Layout;

use Lizmap\Project;

/**
 * QGIS Layout item
 *
 * @property-read int $type
 * @property-read int $width
 * @property-read int $height
 * @property-read int $x
 * @property-read int $y
 */
class LayoutItem extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'type',
        'width',
        'height',
        'x',
        'y',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'type',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'LayoutItem';

    static protected $childParsers = array();

    /**
     * Get attributes from an XMLReader instance at an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at an element
     *
     * @return Array{'type': int, 'width': int, 'height': int, 'x': int, 'y': int} the element attributes as keys / values
     */
    static protected function getAttributes($oXmlReader)
    {
        $size = explode(',', $oXmlReader->getAttribute('size'));
        $position = explode(',', $oXmlReader->getAttribute('position'));
        $data = array(
            'type' => (int) $oXmlReader->getAttribute('type'),
            'width' => (int) $size[0],
            'height' => (int) $size[1],
            'x' => (int) $position[0],
            'y' => (int) $position[1],
        );

        if ($data['type'] ===  65638) {
            $data['typeName'] ='page';
        } else if ($data['type'] === 65639) {
            $data += array(
                'typeName' => 'map',
                'uuid' => $oXmlReader->getAttribute('uuid'),
                'grid' => False,
            );
        } else if ($data['type'] === 65641) {
            $data += array(
                'typeName' => 'label',
                'id' => $oXmlReader->getAttribute('id'),
                'htmlState' => filter_var($oXmlReader->getAttribute('htmlState'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                'text' => $oXmlReader->getAttribute('labelText'),
            );
        }

        return $data;
    }

    /**
     * Build and instance with data as an array
     *
     * @param Array $data the instance data
     *
     * @return LayoutItem|LayoutItemPage|LayoutItemLabel|LayoutItemMap the instance
     */
    static protected function buildInstance($data)
    {
        if (!array_key_exists('typeName', $data)) {
            return new LayoutItem($data);
        } else if ($data['typeName'] == 'page') {
            return new LayoutItemPage($data);
        } else if ($data['typeName'] == 'label') {
            return new LayoutItemLabel($data);
        } else if ($data['typeName'] == 'map') {
            if (array_key_exists('ComposerMapOverview', $data)) {
                foreach ($data['ComposerMapOverview'] as $overview) {
                    if ($overview->show && $overview->frameMap != '-1') {
                        $data['overviewMap'] = $overview->frameMap;
                        break;
                    }
                }
                unset($data['ComposerMapOverview']);
            }
            if (array_key_exists('ComposerMapGrid', $data)) {
                foreach ($data['ComposerMapGrid'] as $grid) {
                    if ($grid->show) {
                        $data['grid'] = true;
                    }
                    break;
                }
                unset($data['ComposerMapGrid']);
            }
            return new LayoutItemMap($data);
        }
        return new LayoutItem($data);
    }
}
LayoutItem::registerChildParser('ComposerMapOverview', function($oXmlReader) {
    return array(
        LayoutItemMapOverview::fromXmlReader($oXmlReader),
    );
});
LayoutItem::registerChildParser('ComposerMapGrid', function($oXmlReader) {
    return array(
        LayoutItemMapGrid::fromXmlReader($oXmlReader),
    );
});
