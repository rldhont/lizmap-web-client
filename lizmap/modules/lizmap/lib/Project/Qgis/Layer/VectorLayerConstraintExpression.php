<?php
/**
 * QGIS Vector layer constraint expression.
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
 * QGIS Vector layer constraint expression
 *
 * @property-read string $field
 * @property-read string $exp
 * @property-read string $desc
 */
class VectorLayerConstraintExpression extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'field',
        'exp',
        'desc',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'field',
        'exp',
        'desc',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'constraint';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'field' => $oXmlReader->getAttribute('field'),
            'exp' => $oXmlReader->getAttribute('exp'),
            'desc' => $oXmlReader->getAttribute('desc'),
        );
    }
}
