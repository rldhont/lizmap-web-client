<?php
/**
 * QGIS Map layer style manager.
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

class MapLayerStyleManager extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'current',
        'styles',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'map-layer-style-manager';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'map-layer-style',
    );

    static protected $childParsers = array();

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'current' => $oXmlReader->getAttribute('current'),
        );
    }

    static protected function buildInstance($data)
    {
        if (array_key_exists('map-layer-style', $data)) {
            $data['styles'] = $data['map-layer-style'];
            unset($data['map-layer-style']);
        }
        return new MapLayerStyleManager($data);
    }
}
MapLayerStyleManager::registerChildParser('map-layer-style', function($oXmlReader) {
    return array($oXmlReader->getAttribute('name'));
});
