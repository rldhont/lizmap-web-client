import TileLayer from 'ol/layer/Tile.js';
import OSM from 'ol/source/OSM.js';
import Stamen from 'ol/source/Stamen.js';

export default class LizmapLayer {
    constructor(layerId, renderingConfig) {
        this._layerId = layerId;
        this._layerVisible = renderingConfig.visible;
    }

    get layerId() {
        return this._layerId;
    }

    get visibility() {
        return this._layerVisible;
    }

    set visibility(visible) {
        this._layerVisible = visible;
        return visible;
    }
}