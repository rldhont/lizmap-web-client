<?php
/**
 * QGIS Vector layer alias.
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
 * QGIS Vector layer alias
 *
 * @property-read int    $index
 * @property-read string $field
 * @property-read string $name
 */
class VectorLayerAlias extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'index',
        'field',
        'name',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'index',
        'field',
        'name',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'alias';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'index' => (int) $oXmlReader->getAttribute('index'),
            'field' => $oXmlReader->getAttribute('field'),
            'name' => $oXmlReader->getAttribute('name'),
        );
    }
}
