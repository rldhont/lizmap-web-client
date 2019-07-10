import LizmapMapElement from './LizmapMapElement.js';

export class LizmapBaseLayersElement extends HTMLElement {
    constructor() {
        super();

        const shadowRoot = this.attachShadow({ mode: 'open' });

        this._mapElement;
    }

    connectedCallback() {

        const self = this;

        // TODO addeventlistener
        window.onload = function() {
            const mapSelector = self.getAttribute('map-selector');

            if (mapSelector) {
                const mapElement = document.querySelector(mapSelector);

                if (mapElement) {
                    if (mapElement instanceof LizmapMapElement) {
                        self._mapElement = mapElement;
                        if (mapElement.baseLayers != null && mapElement.baseLayers.length != 0) {
                            self.render();
                        } else {
                            console.warn("Element lizmap-map has no baselayers yet.");
                        }
                    } else {
                        console.warn("Element is not a lizmap-map element.");
                    }
                } else {
                    console.warn("map-selector does not reference an element.");
                }
            } else {
                console.warn("map-selector undefined.");
            }
        };
    }

    render() {
        let newSelect = document.createElement('select');

        for (let layerDef of this._mapElement.baseLayers) {
            let newNode = document.createElement('option');
            newNode.setAttribute('value', layerDef.id);
            if (config.visible) {
                newNode.setAttribute('selected', 'selected');
            }
            newNode.innerText = layerDef.name;

            newSelect.appendChild(newNode);
        }

        // Event change
        newSelect.onchange = (event) => {
            this._mapElement.baseLayersGroup.layerVisible = event.target.value;
        };

        this.shadowRoot.appendChild(newSelect);
    }
}

window.customElements.define('lizmap-baselayers-element', LizmapBaseLayersElement);