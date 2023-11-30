<?php
/**
 * QGIS Layout item map grid
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
 * QGIS Layout item map grid
 *
 * @property-read boolean $show
 */
class LayoutItemMapGrid extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'show',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'show',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'ComposerMapGrid';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'show' => filter_var($oXmlReader->getAttribute('show'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
        );
    }
}
