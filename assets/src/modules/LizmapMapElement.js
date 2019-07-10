import 'ol/ol.css';

// OLMap and not Map to avoid collision with global object Map
import OLMap from 'ol/Map.js';
import View from 'ol/View.js';
import TileLayer from 'ol/layer/Tile.js';
import OSM from 'ol/source/OSM.js';

import LizmapLayer from './LizmapOlLayer.js';
import LizmapOlLayerGroup from './LizmapOlLayerGroup.js';

export default class LizmapMapElement extends HTMLElement {
    constructor() {
        super();

        this._OLMap; // ol/Map
        this._layers; // Array of LizmapLayer
        this._baseLayers // Array of LizmapLayerDef
        this._baseLayersGroup; // LizmapLayersGroup
    }

    connectedCallback() {
        const map  = new OLMap({
            target: this,
            view: new View({
                center: [0, 0],
                zoom: 2
            })
        });
        this._OLMap = map;
    }

    /**
    * @param layers Array of LizmapLayerDef
    * @return success bool
    **/
    set baseLayers(layers){
        if (this._baseLayerGroup != null) {
            this._baseLayerGroup.removeMap();
        }

        const baseLayersGroup = new LizmapOlLayersGroup({
            mutuallyExclusive: true,
            layersList: layers
        });

        baseLayerGroup.setMap(this._OLMap);
        this._baseLayerGroup = baseLayerGroup;
    }

    /**
    * @return layers Array of LizmapLayerDef
    **/
    get baseLayers(){
        this._baseLayers;
    }

    /**
    * @return layers Array of LizmapLayerDef
    **/
    get baseLayersGroup(){
        return this._baseLayerGroup;
    }
}

window.customElements.define('lizmap-map', LizmapMapElement);