<?php
/**
 * QGIS Vector layer.
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
 * QGIS Vector layer
 *
 * @property-read string                                 $id
 * @property-read boolean                                $embedded
 * @property-read string                                 $type
 * @property-read string                                 $layername
 * @property-read SpatialRefSys                          $srs
 * @property-read string                                 $datasource
 * @property-read string                                 $provider
 * @property-read MapLayerStyleManager                   $styleManager
 * @property-read string|null                            $shortname
 * @property-read string|null                            $title
 * @property-read string|null                            $abstract
 * @property-read Array<string>|null                     $keywordList
 * @property-read string|null                            $previewExpression
 * @property-read MapLayerStyleManager                   $styleManager
 * @property-read Array<VectorLayerField>                $fieldConfiguration
 * @property-read Array<VectorLayerAlias>                $aliases
 * @property-read Array<VectorLayerConstraint>           $constraints
 * @property-read Array<VectorLayerConstraintExpression> $constraintExpressions
 * @property-read Array<VectorLayerDefault>              $defaults
 * @property-read Array<VectorLayerEditableField>        $editable
 * @property-read Array<VectorLayerJoin>                 $vectorjoins
 * @property-read AttributeTableConfig                   $attributetableconfig
 * @property-read Array<string>|null                     $excludeAttributesWMS
 * @property-read Array<string>|null                     $excludeAttributesWFS
 */
class VectorLayer extends Project\Qgis\BaseQgisObject
{
    /** @var Array<string> The instance properties*/
    protected $properties = array(
        'id',
        'embedded',
        'type',
        'layername',
        'srs',
        'datasource',
        'provider',
        'styleManager',
        'shortname',
        'title',
        'abstract',
        'keywordList',
        'previewExpression',
        'fieldConfiguration',
        'aliases',
        'defaults',
        'constraints',
        'constraintExpressions',
        'editable',
        'excludeAttributesWFS',
        'excludeAttributesWMS',
        'attributetableconfig',
        'vectorjoins',
    );

    /** @var Array<string> The not null properties */
    protected $mandatoryProperties = array(
        'id',
        'embedded',
        'type',
        'layername',
        'srs',
        'datasource',
        'provider',
        'styleManager',
    );

    /**
     * Get preview field
     *
     * @return string
     */
    function getPreviewField() {
        if ($this->previewExpression === null) {
            return '';
        }
        $previewField = $this->previewExpression;
        if (substr($previewField, 0, 8) == 'COALESCE') {
            if (preg_match('/"([\S ]+)"/', $previewField, $matches) == 1) {
                $previewField = $matches[1];
            } else {
                $previewField = $referencedField;
            }
        } elseif (substr($previewField, 0, 1) == '"' and substr($previewField, -1) == '"') {
            $previewField = substr($previewField, 1, -1);
        }
        return $previewField;
    }

    /**
     * Get field alias
     *
     * @return string|null
     */
    function getFieldAlias($fieldName) {
        if ($this->aliases === null) {
            return null;
        }
        foreach ($this->aliases as $alias) {
            if ($alias->field === $fieldName) {
                return $alias->name;
            }
        }
        return null;
    }

    /**
     * Get vector layer as key array
     *
     * @return array
     */
    function toKeyArray()
    {
        $data = array(
            'type' => $this->type,
            'id' => $this->id,
            'name' => $this->layername,
            'shortname' => $this->shortname !== null ? $this->shortname : '',
            'title' => $this->title !== null ? $this->title : $this->layername,
            'abstract' => $this->abstract !== null ? $this->abstract : '',
            'proj4' => $this->srs->proj4,
            'srid' => $this->srs->srid,
            'authid' => $this->srs->authid,
            'datasource' => $this->datasource,
            'provider' => $this->provider,
            'keywords' => $this->keywordList !== null ? $this->keywordList : array(),
        );

        $fields = array();
        $wfsFields = array();
        $aliases = array();
        $defaults = array();
        $constraints = array();
        $webDavFields = array();
        $webDavBaseUris = array();
        foreach($this->fieldConfiguration as $field) {
            if (in_array($field->name, $fields)) {
                continue; // QGIS sometimes stores them twice
            }
            $fields[] = $field->name;
            if (!$field->isHideFromWfs()) {
                $wfsFields[] = $field->name;
            }
            $aliases[$field->name] = $field->name;
            $defaults[$field->name] = null;
            $constraints[$field->name] = null;
        }
        if ($this->aliases !== null) {
            foreach ($this->aliases as $alias) {
                $aliases[$alias->field] = $alias->name;
            }
        }
        if ($this->defaults !== null) {
            foreach ($this->defaults as $default) {
                $defaults[$default->field] = $default->expression;
            }
        }
        if ($this->constraints !== null) {
            foreach ($this->constraints as $constraint) {
                $c = array(
                    'constraints' => 0,
                    'notNull' => false,
                    'unique' => false,
                    'exp' => false,
                );
                $c['constraints'] = $constraint->constraints;
                if ($c['constraints'] > 0) {
                    $c['notNull'] = $constraint->notnull_strength;
                    $c['unique'] = $constraint->unique_strength;
                    $c['exp'] = $constraint->exp_strength;
                }
                $constraints[$constraint->field] = $c;
            }
        }

        $data['fields'] = $fields;
        $data['aliases'] = $aliases;
        $data['defaults'] = $defaults;
        $data['constraints'] = $constraints;
        $data['wfsFields'] = $wfsFields;

        if ($this->excludeAttributesWFS !== null) {
            foreach ($this->excludeAttributesWFS as $eField) {
                if (!in_array($eField, $wfsFields)) {
                    continue; // QGIS sometimes stores them twice
                }
                array_splice($wfsFields, array_search($eField, $wfsFields), 1);
            }
            $data['wfsFields'] = $wfsFields;
        }

        return $data;
    }
}
