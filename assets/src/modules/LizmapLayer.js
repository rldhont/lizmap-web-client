
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