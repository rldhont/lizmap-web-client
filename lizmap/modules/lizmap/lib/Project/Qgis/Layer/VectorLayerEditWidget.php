<?php
/**
 * QGIS Vector layer edit widget.
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
 * QGIS Vector layer edit widget
 *
 * @property-read string    $type
 * @property-read array-key $config
 */
class VectorLayerEditWidget extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'type',
        'config',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'type',
        'config',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'editWidget';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'type' => $oXmlReader->getAttribute('type'),
        );
    }

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'config',
    );

    static protected $childParsers = array();

    static protected function buildInstance($data)
    {
        if ($data['type'] == 'TextEdit') {
            $data['config'] = new EditWidget\TextEditConfig($data['config']);
        } else if ($data['type'] == 'CheckBox') {
            $data['config'] = new EditWidget\CheckBoxConfig($data['config']);
        } else if ($data['type'] == 'DateTime') {
            $data['config'] = new EditWidget\DateTimeConfig($data['config']);
        } else if ($data['type'] == 'Range') {
            $data['config'] = new EditWidget\RangeConfig($data['config']);
        }
        return new VectorLayerEditWidget($data);
    }
}
VectorLayerEditWidget::registerChildParser('config', function($oXmlReader) {
    $depth = $oXmlReader->depth;
    $localName = $oXmlReader->localName;
    $data = array();
    if ($oXmlReader->isEmptyElement) {
        return $data;
    }
    while ($oXmlReader->read()) {
        if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
            && $oXmlReader->localName == $localName
            && $oXmlReader->depth == $depth) {
            break;
        }

        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            continue;
        }

        if ($oXmlReader->depth != $depth + 1) {
            continue;
        }

        if ($oXmlReader->localName == 'Option') {
            $data += Project\QgisProjectParser::readOption($oXmlReader);;
        }
    }
    return $data;
    /*
    <fieldConfiguration>
      <field name="OGC_FID">
        <editWidget type="TextEdit">
          <config>
            <Option type="Map">
              <Option value="0" type="QString" name="IsMultiline"/>
              <Option value="0" type="QString" name="UseHtml"/>
            </Option>
          </config>
        </editWidget>
      </field>
    </fieldConfiguration>
    */
});
