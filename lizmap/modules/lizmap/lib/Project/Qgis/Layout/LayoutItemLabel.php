<?php
/**
 * QGIS Layout item label.
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
 * QGIS Layout item label
 *
 * @property-read int     $type
 * @property-read string  $typeName
 * @property-read int     $width
 * @property-read int     $height
 * @property-read int     $x
 * @property-read int     $y
 * @property-read string  $id
 * @property-read boolean $htmlState
 * @property-read string  $text
 */
class LayoutItemLabel extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'type',
        'typeName',
        'width',
        'height',
        'x',
        'y',
        'id',
        'htmlState',
        'text',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'type',
        'typeName',
        'width',
        'height',
        'x',
        'y',
        'id',
        'htmlState',
        'text',
    );
}
