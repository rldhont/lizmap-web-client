<?php
/**
 * QGIS Vector layer edit widget RelationReference config
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
 * QGIS Vector layer edit widget RelationReference config
 * <editWidget type="RelationReference">
 *   <config>
 *     <Option type="Map">
 *       <Option type="bool" value="false" name="AllowAddFeatures"/>
 *       <Option type="bool" value="true" name="AllowNULL"/>
 *       <Option type="bool" value="false" name="MapIdentification"/>
 *       <Option type="bool" value="false" name="OrderByValue"/>
 *       <Option type="bool" value="false" name="ReadOnly"/>
 *       <Option type="QString" value="service=lizmap sslmode=disable key=\'fid\' checkPrimaryKeyUnicity=\'0\' table=&quot;lizmap_data&quot;.&quot;risque&quot;" name="ReferencedLayerDataSource"/>
 *       <Option type="QString" value="risque_66cb8d43_86b7_4583_9217_f7ead54463c3" name="ReferencedLayerId"/>
 *       <Option type="QString" value="risque" name="ReferencedLayerName"/>
 *       <Option type="QString" value="postgres" name="ReferencedLayerProviderKey"/>
 *       <Option type="QString" value="tab_demand_risque_risque_66c_risque" name="Relation"/>
 *       <Option type="bool" value="false" name="ShowForm"/>
 *       <Option type="bool" value="true" name="ShowOpenFormButton"/>
 *       </Option>
 *   </config>
 * </editWidget>
 *
 * @property-read boolean $AllowAddFeatures
 * @property-read boolean $AllowNULL
 * @property-read boolean $MapIdentification
 * @property-read boolean $OrderByValue
 * @property-read boolean $ReadOnly
 * @property-read string  $ReferencedLayerDataSource
 * @property-read string  $ReferencedLayerId
 * @property-read string  $ReferencedLayerName
 * @property-read string  $ReferencedLayerProviderKey
 * @property-read string  $Relation
 * @property-read boolean $ShowForm
 * @property-read boolean $ShowOpenFormButton
 *
 */
class RelationReferenceConfig extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'AllowAddFeatures',
        'AllowNULL',
        'MapIdentification',
        'OrderByValue',
        'ReadOnly',
        'ReferencedLayerDataSource',
        'ReferencedLayerId',
        'ReferencedLayerName',
        'ReferencedLayerProviderKey',
        'Relation',
        'ShowForm',
        'ShowOpenFormButton',
    );
}

/*
<field name="father_id" configurationFlags="None">
<editWidget type="RelationReference">
  <config>
    <Option/>
  </config>
</editWidget>
</field>
*/
