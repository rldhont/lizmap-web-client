import { BaseObjectConfig } from './Base.js';

const optionalProperties = {
    'lizmap_plugin_version_str': {type: 'string', default: '3.1.8'},
    'lizmap_plugin_version': {type: 'number', default: 30108},
    'lizmap_web_client_target_version': {type: 'number', default: 30200},
    'project_valid': {type: 'boolean', nullable: true, default: null},
    'qgis_desktop_version': {type: 'null', default: 30000}
};

export class MetadataConfig extends BaseObjectConfig {

    /**
     * @param {Object} cfg - the lizmap config object
     */
    constructor(cfg = {}) {
        super(cfg, {}, optionalProperties)
    }

    get lizmap_plugin_version_str() {
        return this._lizmap_plugin_version_str;
    }

    get lizmap_plugin_version() {
        return this._lizmap_plugin_version;
    }

    get lizmap_web_client_target_version() {
        return this._lizmap_web_client_target_version;
    }

    get project_valid() {
        return this._project_valid;
    }

    get qgis_desktop_version() {
        return this._qgis_desktop_version;
    }
}
