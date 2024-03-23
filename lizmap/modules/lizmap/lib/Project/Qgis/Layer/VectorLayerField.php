<?php
/**
 * QGIS Vector layer field.
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
 * QGIS Vector layer join
 *
 * @property-read string      $name
 * @property-read string|null $configurationFlags
 */
class VectorLayerField extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'name',
        'configurationFlags',
        'editWidget',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'name',
    );

    /**
     * @return boolean
     */
    public function isHideFromWms() {
        if (!isset($this->configurationFlags)) {
            return False;
        }
        return (strpos($this->configurationFlags, 'HideFromWms') !== False);
    }

    /**
     * @return boolean
     */
    public function isHideFromWfs() {
        if (!isset($this->configurationFlags)) {
            return False;
        }
        return (strpos($this->configurationFlags, 'HideFromWfs') !== False);
    }

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'field';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'name' => $oXmlReader->getAttribute('name'),
            'configurationFlags' => $oXmlReader->getAttribute('configurationFlags'),
        );
    }

    static protected $childParsers = array();
}
VectorLayerField::registerChildParser('editWidget', function($oXmlReader) {
    return VectorLayerEditWidget::fromXmlReader($oXmlReader);
});
