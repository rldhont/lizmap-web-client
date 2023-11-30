<?php
/**
 * QGIS Vector layer join.
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
 * @property-read string $joinLayerId
 * @property-read string $joinFieldName
 * @property-read string $targetFieldName
 */
class VectorLayerJoin extends Project\Qgis\BaseQgisXmlObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'joinLayerId',
        'joinFieldName',
        'targetFieldName',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'joinLayerId',
        'joinFieldName',
        'targetFieldName',
    );

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'join';

    static protected function getAttributes($oXmlReader)
    {
        return array(
            'joinLayerId' => $oXmlReader->getAttribute('joinLayerId'),
            'joinFieldName' => $oXmlReader->getAttribute('joinFieldName'),
            'targetFieldName' => $oXmlReader->getAttribute('targetFieldName'),
        );
    }
}
