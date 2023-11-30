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
 * QGIS Project Visibility preset class
 *
 * @property-read string                              $name
 * @property-read Array<ProjectVisibilityPresetLayer> $layers
 * @property-read Array<string>                       $checkedGroupNodes
 * @property-read Array<string>                       $expandedGroupNodes
 */
class ProjectVisibilityPreset extends BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'name',
        'layers',
        'checkedGroupNodes',
        'expandedGroupNodes',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'name',
        'layers',
    );

    protected function set(array $data) : void
    {
        if (!array_key_exists('checkedGroupNodes', $data)) {
            $data['checkedGroupNodes'] = array();
        }
        if (!array_key_exists('expandedGroupNodes', $data)) {
            $data['expandedGroupNodes'] = array();
        }
        parent::set($data);
    }

    /**
     * Get visibility preset as key array
     *
     * @return array
     */
    function toKeyArray()
    {
        $data = array(
            'layers' => array(),
            'checkedGroupNodes' => $this->checkedGroupNodes,
            'expandedGroupNodes' => $this->expandedGroupNodes,
        );
        foreach ($this->layers as $layer) {
            // Since QGIS 3.26, theme contains every layers with visible attributes
            // before only visible layers are in theme
            // So do not keep layer not visible
            if (!$layer->visible) {
                continue;
            }
            $data['layers'][$layer->id] = array(
                'style' => $layer->style,
                'expanded' => $layer->expanded,
            );
        }
        return $data;
    }

    /** @var string The XML element local name */
    static protected $qgisLocalName = 'visibility-preset';

    static public function fromXmlReader($oXmlReader)
    {
        if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
            throw new \Exception('Provide an XMLReader::ELEMENT!');
        }
        $localName = static::$qgisLocalName;
        if ($oXmlReader->localName != $localName) {
            throw new \Exception('Provide a `'.$localName.'` element not `'.$oXmlReader->localName.'`!');
        }

        $depth = $oXmlReader->depth;
        $data = array(
            'name' => $oXmlReader->getAttribute('name'),
            'layers' => array(),
            'checkedGroupNodes' => array(),
            'expandedGroupNodes' => array(),
        );
        while ($oXmlReader->read()) {
            if ($oXmlReader->nodeType == \XMLReader::END_ELEMENT
                && $oXmlReader->localName == $localName
                && $oXmlReader->depth == $depth) {
                break;
            }

            if ($oXmlReader->nodeType != \XMLReader::ELEMENT) {
                continue;
            }

            if ($oXmlReader->depth > $depth + 2) {
                continue;
            }

            $tagName = $oXmlReader->localName;
            if ( $tagName == 'layer' ) {
                $data['layers'][] = new ProjectVisibilityPresetLayer(
                    array(
                        'id' => $oXmlReader->getAttribute('id'),
                        'visible' => filter_var($oXmlReader->getAttribute('visible'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                        'style' => $oXmlReader->getAttribute('style'),
                        'expanded' => filter_var($oXmlReader->getAttribute('expanded'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                    ),
                );
            } else if ( $tagName == 'checked-group-node' ) {
                $data['checkedGroupNodes'][] = $oXmlReader->getAttribute('id');
            } else if ( $tagName == 'expanded-group-node' ) {
                $data['expandedGroupNodes'][] = $oXmlReader->getAttribute('id');
            }
        }
        return new ProjectVisibilityPreset($data);
    }
}
