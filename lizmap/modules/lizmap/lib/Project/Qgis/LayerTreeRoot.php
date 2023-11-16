<?php
/**
 * QGIS Layer tree root.
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
 * QGIS Layer tree root
 *
 * @property-read LayerTreeCustomOrder $customOrder
 */
class LayerTreeRoot extends BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'customOrder',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'customOrder',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'layer-tree-group';

    static protected $childParsers = array();

    static protected function buildInstance($data)
    {
        if (array_key_exists('custom-order', $data)) {
            $data['customOrder'] = $data['custom-order'];
            unset($data['custom-order']);
        }
        return new LayerTreeRoot($data);
    }
}
LayerTreeRoot::registerChildParser('custom-order', function($oXmlReader) {
    $data = array(
        'enabled' => filter_var($oXmlReader->getAttribute('enabled'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        'items' => array(),
    );
    if ($data['enabled']) {
        $data['items'] = Project\QgisProjectParser::readItems($oXmlReader);
    } else {
        $oXmlReader->next();
    }
    return new LayerTreeCustomOrder($data);
});
