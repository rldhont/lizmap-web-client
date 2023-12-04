<?php
/**
 * QGIS Vector layer edit widget ExternalResource config
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
 * QGIS Vector layer edit widget ExternalResource config
 * <editWidget type="ExternalResource">
 *   <config>
 *     <Option type="Map">
 *       <Option value="1" type="QString" name="DocumentViewer"/>
 *       <Option value="400" type="QString" name="DocumentViewerHeight"/>
 *       <Option value="400" type="QString" name="DocumentViewerWidth"/>
 *       <Option value="1" type="QString" name="FileWidget"/>
 *       <Option value="1" type="QString" name="FileWidgetButton"/>
 *       <Option value="Images (*.gif *.jpeg *.jpg *.png)" type="QString" name="FileWidgetFilter"/>
 *       <Option value="0" type="QString" name="StorageMode"/>
 *     </Option>
 *   </config>
 * </editWidget>
 *
 *
 * @property-read int     $DocumentViewer
 * @property-read int     $DocumentViewerHeight
 * @property-read int     $DocumentViewerWidth
 * @property-read boolean $FileWidget
 * @property-read boolean $FileWidgetButton
 * @property-read string  $FileWidgetFilter
 * @property-read boolean $UseLink
 * @property-read boolean $FullUrl
 * @property-read array   $PropertyCollection
 * @property-read int     $RelativeStorage
 * @property-read string  $StorageAuthConfigId
 * @property-read int     $StorageMode
 * @property-read string  $StorageType
 */
class ExternalResourceConfig extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'DocumentViewer',
        'DocumentViewerHeight',
        'DocumentViewerWidth',
        'FileWidget',
        'FileWidgetButton',
        'FileWidgetFilter',
        'UseLink',
        'FullUrl',
        'PropertyCollection',
        'RelativeStorage',
        'StorageAuthConfigId',
        'StorageMode',
        'StorageType',
    );
}

/*

          <editWidget type="ExternalResource">
            <config>
              <Option type="Map">
                <Option value="1" name="DocumentViewer" type="int"/>
                <Option value="0" name="DocumentViewerHeight" type="int"/>
                <Option value="0" name="DocumentViewerWidth" type="int"/>
                <Option value="true" name="FileWidget" type="bool"/>
                <Option value="true" name="FileWidgetButton" type="bool"/>
                <Option value="" name="FileWidgetFilter" type="QString"/>
                <Option name="PropertyCollection" type="Map">
                  <Option value="" name="name" type="QString"/>
                  <Option name="properties" type="Map">
                    <Option name="storageUrl" type="Map">
                      <Option value="true" name="active" type="bool"/>
                      <Option value="'http://webdav/shapeData/'||file_name(@selected_file_path)" name="expression" type="QString"/>
                      <Option value="3" name="type" type="int"/>
                    </Option>
                  </Option>
                  <Option value="collection" name="type" type="QString"/>
                </Option>
                <Option value="0" name="RelativeStorage" type="int"/>
                <Option value="k6k7lv8" name="StorageAuthConfigId" type="QString"/>
                <Option value="0" name="StorageMode" type="int"/>
                <Option value="WebDAV" name="StorageType" type="QString"/>
              </Option>
            </config>
          </editWidget>

          array(
                'DocumentViewer' => 1,
                'DocumentViewerHeight' => 0,
                'DocumentViewerWidth' => 0,
                'FileWidget' => true,
                'FileWidgetButton' => true,
                'FileWidgetFilter' => '',
                'PropertyCollection' => array(
                  'name' => '',
                  'properties' => array(
                    'storageUrl' => array (
                      'active' => true,
                      'expression' => '\'http://webdav/shapeData/\'||file_name(@selected_file_path)',
                      'type' => 3,
                    ),
                  ),
                  'type' => 'collection',
                ),
                'RelativeStorage' => 0,
                'StorageAuthConfigId' => 'k6k7lv8',
                'StorageMode' => 0,
                'StorageType' => 'WebDAV',
        );
*/
