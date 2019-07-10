import LizmapLayerGroup from './LizmapLayerGroup.js';
import LizmapOlLayer from './LizmapOlLayer.js';
import LayerGroup from 'ol/layer/Group';

// attribut selected
export default class LizmapOlLayerGroup extent LizmapLayerGroup {
    constructor(opt_options) {

        this._mutuallyExclusive = opt_options.mutuallyExclusive;

        this._olLayerGroup = new LayerGroup({
        });

        this._lizmapLayers = [];

        for (let layerDef of opt_options.layersList) {
            const lizmapLayer = new LizmapOlLayer(layerDef.id, layerDef.renderingConfig, this_olLayerGroup);

            this._lizmapLayers.push(lizmapLayer);
        }

    }

    // Make class iterable
    [Symbol.iterator]() {
        let index = -1;

        return {
            next: () => ({ value: this._lizmapLayers[++index], done: !(index in this._lizmapLayers) })
        };
    };

    get setMap(map) {
        map.addLayer(this._olLayerGroup);
    }

    set layerVisible(layerId) {
        for (let i = 0; i < this._lizmapLayers.length; i++) {
            // Set visibility to false when mutually exclusive
            if (this._mutuallyExclusive) {
                this._lizmapLayers[i].layerVisible = false;
            }
            if (this._lizmapLayers[i].layerId === layerId) {
                this._lizmapLayers[i].layerVisible = true;
            }
        }
    }
}