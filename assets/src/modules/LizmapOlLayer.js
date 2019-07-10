import LizmapLayer from './LizmapLayer.js';

import TileLayer from 'ol/layer/Tile.js';
import OSM from 'ol/source/OSM.js';
import Stamen from 'ol/source/Stamen.js';

export default class LizmapOlLayer extends LizmapLayer {
    constructor(layerId, renderingConfig, olGroup=null) {
        this._layerId = layerId;

        this._ollayer;
        this._olGroup = olGroup;

        // build Ol layer
        if (renderingConfig.type == 'XYZ') {
            if (layerId === 'osmMapnik') {
                this._ollayer = new TileLayer({
                    layerId: layerId,
                    visible: renderingConfig.visible,
                    source: new OSM()
                })
            } else if (layerId === 'osmStamenToner') {
                this._ollayer = new TileLayer({
                    layerId: layerId,
                    visible: renderingConfig.visible,
                    source: new Stamen({
                        layer: 'toner'
                    })
                });
            }
        }

        if (this._olGroup != null){
            this._olGroup.layers.push(this_olLayer);
        }
    }

    setMap(map) {
        map.addLayer(this_olLayer);
    }

    get layerId() {
        return this._layerId;
    }

    get visibility() {
        this._olLayer.getVisible();
    }

    set visibility(visible) {
        this._ollayer.setVisible(visible);
    }
}