<?php
/**
 * QGIS Vector layer constraint.
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
 * QGIS Vector layer constraint
 *
 * @property-read string  $field
 * @property-read int     $constraints
 * @property-read boolean $notnull_strength
 * @property-read boolean $unique_strength
 * @property-read boolean $exp_strength
 */
class VectorLayerConstraint extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'field',
        'constraints',
        'notnull_strength',
        'unique_strength',
        'exp_strength',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'field',
        'constraints',
        'notnull_strength',
        'unique_strength',
        'exp_strength',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'constraint';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'field' => $oXmlReader->getAttribute('field'),
            'constraints' => (int) $oXmlReader->getAttribute('constraints'),
            'notnull_strength' => filter_var($oXmlReader->getAttribute('notnull_strength'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'unique_strength' => filter_var($oXmlReader->getAttribute('unique_strength'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'exp_strength' => filter_var($oXmlReader->getAttribute('exp_strength'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        );
    }
}
