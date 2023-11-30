<?php
/**
 * QGIS Layout item map overview
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
 * QGIS Layout item map overview
 *
 * @property-read boolean $show
 * @property-read string  $frameMap
 */
class LayoutItemMapOverview extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'show',
        'frameMap',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'show',
        'frameMap',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'ComposerMapOverview';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'show' => filter_var($oXmlReader->getAttribute('show'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            'frameMap' => $oXmlReader->getAttribute('frameMap'),
        );
    }
}
