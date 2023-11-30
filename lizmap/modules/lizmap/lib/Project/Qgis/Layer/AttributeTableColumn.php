<?php
/**
 * QGIS Vector layer attribute table column.
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
 * QGIS Vector layer attribute table column
 *
 * @property-read string  $type
 * @property-read string  $name
 * @property-read boolean $hidden
 */
class AttributeTableColumn extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'type',
        'name',
        'hidden',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'type',
        'name',
        'hidden',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'column';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'type' => $oXmlReader->getAttribute('type'),
            'name' => $oXmlReader->getAttribute('name'),
            'hidden' => filter_var($oXmlReader->getAttribute('hidden'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        );
    }
}
