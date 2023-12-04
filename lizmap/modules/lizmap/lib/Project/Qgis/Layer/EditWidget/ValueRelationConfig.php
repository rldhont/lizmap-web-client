<?php
/**
 * QGIS Vector layer edit widget ValueRelation config
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
 * QGIS Vector layer edit widget ValueRelation config
 * <editWidget type="ValueRelation">
 *   <config>
 *     <Option type="Map">
 *         <Option value="0" type="QString" name="AllowMulti"/>
 *         <Option value="1" type="QString" name="AllowNull"/>
 *         <Option value="" type="QString" name="FilterExpression"/>
 *         <Option value="osm_id" type="QString" name="Key"/>
 *         <Option value="tramway20150328114206278" type="QString" name="Layer"/>
 *         <Option value="1" type="QString" name="OrderByValue"/>
 *         <Option value="0" type="QString" name="UseCompleter"/>
 *         <Option value="test" type="QString" name="Value"/>
 *     </Option>
 *   </config>
 * </editWidget>
 *
 * @property-read string  $Layer
 * @property-read string  $LayerName
 * @property-read string  $LayerSource
 * @property-read string  $LayerProviderName
 * @property-read string  $Key
 * @property-read string  $Value
 * @property-read string  $Description
 * @property-read boolean $AllowMulti
 * @property-read string  $NofColumns
 * @property-read boolean $AllowNull
 * @property-read boolean $OrderByValue
 * @property-read string  $FilterExpression
 * @property-read boolean $UseCompleter
 *
 */
class ValueRelationConfig extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'Layer',
        'LayerName',
        'LayerSource',
        'LayerProviderName',
        'Key',
        'Value',
        'Description',
        'AllowMulti',
        'NofColumns',
        'AllowNull',
        'OrderByValue',
        'FilterExpression',
        'UseCompleter',
    );
}
