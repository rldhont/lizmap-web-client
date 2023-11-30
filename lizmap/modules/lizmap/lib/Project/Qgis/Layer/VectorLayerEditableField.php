<?php
/**
 * QGIS Vector layer default.
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
 * QGIS Vector layer default
 *
 * @property-read string  $name
 * @property-read boolean $editable
 */
class VectorLayerEditableField extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'name',
        'editable',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'name',
        'editable',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'field';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'name' => $oXmlReader->getAttribute('name'),
            'editable' => filter_var($oXmlReader->getAttribute('editable'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        );
    }
}
