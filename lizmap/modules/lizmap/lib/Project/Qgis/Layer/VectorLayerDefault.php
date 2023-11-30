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
 * @property-read string  $field
 * @property-read string  $expression
 * @property-read boolean $applyOnUpdate
 */
class VectorLayerDefault extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'field',
        'expression',
        'applyOnUpdate',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'field',
        'expression',
        'applyOnUpdate',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'default';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'field' => $oXmlReader->getAttribute('field'),
            'expression' => $oXmlReader->getAttribute('expression'),
            'applyOnUpdate' => filter_var($oXmlReader->getAttribute('applyOnUpdate'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        );
    }
}
