<?php
/**
 * QGIS Project Visibility Preset.
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis;

/**
 * QGIS Project Visibility preset layer class
 *
 * @property-read string  $id
 * @property-read boolean $visible
 * @property-read string  $style
 * @property-read boolean $expanded
 */
class ProjectVisibilityPresetLayer extends BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'id',
        'visible',
        'style',
        'expanded',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'id',
        'visible',
        'style',
        'expanded',
    );
}
