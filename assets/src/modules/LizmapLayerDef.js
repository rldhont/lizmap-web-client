export default class LizmapLayerRenderingDef {
    constructor(cfg) {
        this._type;  //['WMS', 'WMTS', 'XYZ']
        this._source; // URL or key value
        this._layer; // The layer name used in source

        this._visible; // layer visibility at the startup
    }

    get type() {
        return this._type;
    }

    get source() {
        return this._source;
    }

    get layer() {
        return this._layer;
    }

    get visible() {
        return this._visible;
    }
}

export default class LizmapLayerDef {
    constructor(cfg) {
        this._id; // Layer id
        this._name; // Layer name in QGIS
        this._shortname; // Layer short name, used in OGC web services
        this._typename; // Layer type name, used in WFS (can be equal to name or shortname)

        this._title; // Layer title
        this._description; // Layer description

        this._styles; // Layer styles array of named styles

        this._rendering_config; // LizmapLayerRenderingDef: the configuration to build the rendering
    }

    get id() {
        return this._id;
    }

    get name() {
        return this._name;
    }

    get shortname() {
        return this._shortname;
    }

    get typename() {
        return this._typename;
    }

    get title() {
        return this._title;
    }

    get description() {
        return this._description;
    }

    get styles() {
        return this._styles;
    }

    get renderingConfig() {
        return this._rendering_config;
    }

}