<?php
/**
 * QGIS Embedded layer.
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
 * QGIS Embedded layer
 *
 * @property-read string  $id
 * @property-read boolean $embedded
 * @property-read string  $project
 */
class EmbeddedLayer extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'id',
        'embedded',
        'project',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'id',
        'embedded',
        'project',
    );

    /**
     * Get embedded layer as key array
     *
     * @return array
     */
    function toKeyArray()
    {
        return array(
            'id' => $this->id,
            'embedded' => $this->embedded,
            'project' => $this->project,
        );
    }
}
