<?php
/**
 * QGIS Vector layer edit widget CheckBox config
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis\Layer\EditWidget;

use Lizmap\Project;

/**
 * QGIS Vector layer edit widget CheckBox config
 * <editWidget type="CheckBox">
 *   <config>
 *     <Option type="Map">
 *         <Option value="1" type="QString" name="CheckedState"/>
 *         <Option value="0" type="QString" name="UncheckedState"/>
 *     </Option>
 *   </config>
 * </editWidget>
 *
 * @property-read string $CheckedState
 * @property-read string $UncheckedState
 * @property-read int    $TextDisplayMethod
 *
 */
class CheckBoxConfig extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'CheckedState',
        'UncheckedState',
        'TextDisplayMethod',
    );

    /** @var Array The default values */
    protected $defaultValues = array(
        'CheckedState' => '',
        'UncheckedState' => '',
        'TextDisplayMethod' => 0,
    );
}
