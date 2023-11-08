<?php
/**
 * QGIS Project Properties.
 *
 * @author    3liz
 * @copyright 2023 3liz
 *
 * @see      http://3liz.com
 *
 * @license Mozilla Public License : http://www.mozilla.org/MPL/
 */

namespace Lizmap\Project\Qgis;

use Lizmap\Project;

/**
 * QGIS Project Properties class
 *
 * @property-read string|null        $WMSServiceTitle
 * @property-read string|null        $WMSServiceAbstract
 * @property-read Array<string>|null $WMSKeywordList
 * @property-read Array<float>|null  $WMSExtent
 * @property-read string|null        $WMSOnlineResource
 * @property-read string|null        $WMSContactMail
 * @property-read string|null        $WMSContactOrganization
 * @property-read string|null        $WMSContactPerson
 * @property-read string|null        $WMSContactPhone
 * @property-read int|null           $WMSMaxWidth
 * @property-read int|null           $WMSMaxHeight
 * @property-read int|null           $WMSMaxAtlasFeatures
 * @property-read Array<string>|null $WMSRestrictedComposers
 * @property-read Array<string>|null $WMSRestrictedLayers
 * @property-read Array<string>|null $WFSLayers
 * @property-read boolean|null       $WMSUseLayerIDs
 * @property-read boolean|null       $WMSAddWktGeometry
 */
class ProjectProperties extends BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'WMSServiceTitle',
        'WMSServiceAbstract',
        'WMSKeywordList',
        'WMSExtent',
        'WMSOnlineResource',
        'WMSContactMail',
        'WMSContactOrganization',
        'WMSContactPerson',
        'WMSContactPhone',
        'WMSMaxWidth',
        'WMSMaxHeight',
        'WMSMaxAtlasFeatures',
        'WMSRestrictedComposers',
        'WMSRestrictedLayers',
        'WFSLayers',
        'WMSUseLayerIDs',
        'WMSAddWktGeometry',
        'Gui',
        'Variables',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'properties';

    /** @var Array<string> The XML element parsed children */
    static protected $children = array(
        'WMSServiceTitle',
        'WMSServiceAbstract',
        'WMSKeywordList',
        'WMSExtent',
        'WMSOnlineResource',
        'WMSContactMail',
        'WMSContactOrganization',
        'WMSContactPerson',
        'WMSContactPhone',
        'WMSMaxWidth',
        'WMSMaxHeight',
        'WMSMaxAtlasFeatures',
        'WMSRestrictedComposers',
        'WMSRestrictedLayers',
        'WFSLayers',
        'WMSUseLayerIDs',
        'WMSAddWktGeometry',
        // 'Gui',
        // 'Variables',
    );

    /**
     * Parse from an XMLReader instance at a child of an element
     *
     * @param \XMLReader $oXmlReader An XMLReader instance at a child of an element
     *
     * @return Array|boolean|int|string the result of the parsing
     */
    static protected function parseChild($oXmlReader)
    {
        $type = $oXmlReader->getAttribute('type');
        if ($type == 'QStringList') {
            if (!$oXmlReader->isEmptyElement) {
                return Project\QgisProjectParser::readValues($oXmlReader);
            }
            return array();
        }

        if ($type == 'bool') {
            return filter_var($oXmlReader->readString(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($type == 'int') {
            return (int) $oXmlReader->readString();
        }
        return $oXmlReader->readString();
    }

    static protected $childParsers = array();
}
ProjectProperties::registerChildParser('Gui', function($oXmlReader) {
    return ProjectGuiProperties::fromXmlReader($oXmlReader);
});
ProjectProperties::registerChildParser('Variables', function($oXmlReader) {
    return ProjectVariables::fromXmlReader($oXmlReader);
});
