<?php
/**
 * QGIS Layout item map.
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
 * QGIS Layout item map
 *
 * @property-read int         $type
 * @property-read string      $typeName
 * @property-read int         $width
 * @property-read int         $height
 * @property-read int         $x
 * @property-read int         $y
 * @property-read string      $uuid
 * @property-read boolean     $grid
 * @property-read string|null $overviewMap
 */
class LayoutItemMap extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'type',
        'typeName',
        'width',
        'height',
        'x',
        'y',
        'uuid',
        'grid',
        'overviewMap',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'type',
        'typeName',
        'width',
        'height',
        'x',
        'y',
        'uuid',
        'grid',
    );
}
