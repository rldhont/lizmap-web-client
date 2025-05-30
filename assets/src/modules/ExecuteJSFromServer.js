/**
 * LizDict
 * @typedef {object}  lizDict
 * @property {object} string All strings translated in user interface
 */

/**
 * Execute JS from server
 */
export default function executeJSFromServer() {
    lizMap.events.on({
        uicreated: () => {
            displayWarningsAdministrator();
            checkInvalidLayersCfgFile();

            if (document.body.dataset.lizmapHideLegend) {
                document.querySelector('li.switcher.active #button-switcher')?.click();
            }
        }
    });

    if (document.body.dataset.lizmapEmbed) {
        lizMap.events.on({
            uicreated: () => {
                // it's an embedded content
                $('#content').addClass('embed');

                // move tooltip placement to bottom
                const tooltipTriggerList = document.querySelectorAll(
                    '#mapmenu .nav-list > li > a');
                [...tooltipTriggerList].map(tooltipTriggerEl => {
                    bootstrap.Tooltip.getInstance(tooltipTriggerEl).dispose();
                    new bootstrap.Tooltip(tooltipTriggerEl, { placement: 'bottom', trigger: 'hover' });
                });

                // move search tool
                var search = $('#nominatim-search');
                if (search.length != 0) {
                    $('#mapmenu').append(search);
                    $(
                        '#nominatim-search div.dropdown-menu'
                    ).removeClass('pull-right').addClass('pull-left');
                }

                //calculate dock position and size
                $('#dock').css('top', ($('#mapmenu').height() + 10) + 'px');
                lizMap.updateContentSize();

                // force mini-dock and sub-dock position
                $('#mini-dock').css('top', $('#dock').css('top'));
                $('#sub-dock').css('top', $('#dock').css('top'));

                // Force display popup on the map
                lizMap.config.options.popupLocation = 'map';

                // Force close tools
                if ($('#mapmenu li.locate').hasClass('active')){
                    document.getElementById('button-locate')?.click();
                }
                if ($('#mapmenu li.switcher').hasClass('active')){
                    document.getElementById('button-switcher')?.click();
                }

                $('#mapmenu .nav-list > li.permaLink a').attr(
                    'data-bs-toggle', 'tooltip'
                ).attr(
                    'data-bs-title', lizDict['embed.open.map']
                );
            },
            dockopened: () => {
                // one tool at a time
                var activeMenu = $('#mapmenu ul li.nav-minidock.active a');
                if (activeMenu.length != 0)
                    activeMenu.click();
            },
            minidockopened: evt => {
                // one tool at a time
                var activeMenu = $('#mapmenu ul li.nav-dock.active a');
                if (activeMenu.length != 0)
                    activeMenu.click();

                // adapte locateByLayer display
                if (evt.id == 'locate') {
                    // autocompletion items for locatebylayer feature
                    $('div.locate-layer select').hide();
                    $('span.custom-combobox').show();
                    $('#locate div.locate-layer input.custom-combobox-input'
                    ).autocomplete('option', 'position', { my: 'left top', at: 'left bottom' });
                }

                if (evt.id == 'permaLink') {
                    window.open(window.location.href.replace('embed', 'map'));
                    $('#mapmenu ul li.nav-minidock.active a').click();
                    return false;
                }
            }
        });
    }
}

/**
 * Display a message about invalid layers, detected on the client side.
 *
 * A message is logged in the console in English.
 * Another message is translated and displayed for GIS administrators.
 */
function checkInvalidLayersCfgFile(){
    const invalidLayers = lizMap.mainLizmap.initialConfig.invalidLayersNotFoundInCfg;
    if (invalidLayers.length === 0) {
        return;
    }

    let message = `WMS layers "${invalidLayers.join(', ')}" are not found in the Lizmap configuration. `;
    message += `Is the Lizmap configuration file "${lizUrls.params.project}.qgs.cfg" up to date ?`;
    console.warn(message);

    if (!document.body.dataset.lizmapAdminUser){
        return;
    }

    const layersNotFound = `
        ${lizDict['project.has.not.found.layers']}<br>
        <code>${invalidLayers.join(', ')}</code>`;

    lizMap.addMessage(
        layersNotFound,
        'warning',
        true
    ).attr('id', 'lizmap-invalid-layers');
}

/**
 * Display messages to the administrator about deprecated features or outdated versions.
 *
 * The message is translated and displayed for GIS administrators.
 * A message is logged in the console in English.
 */
function displayWarningsAdministrator() {
    if (document.body.dataset.lizmapPluginUpdateWarning) {
        console.warn('The plugin in QGIS is not up to date.');

        if (document.body.dataset.lizmapPluginUpdateWarningUrl) {
            let messageOutdatedWarning = lizDict['project.plugin.outdated.warning'];
            messageOutdatedWarning += `<br><a href="${document.body.dataset.lizmapPluginUpdateWarningUrl}">`;
            messageOutdatedWarning += lizDict['visit.admin.panel.project.page'];
            messageOutdatedWarning += '</a>';
            messageOutdatedWarning += '<br>';
            messageOutdatedWarning += lizDict['project.admin.panel.info'];

            // The plugin can be easily updated, the popup can not be closed
            lizMap.addMessage(
                messageOutdatedWarning,
                'warning',
                false
            ).attr('id', 'lizmap-outdated-plugin');
        }

    }

    if (document.body.dataset.lizmapPluginWarningsCount) {
        console.warn(
            `The project has ${document.body.dataset.lizmapPluginWarningsCount} warning(s) in the Lizmap plugin.`);

        if (document.body.dataset.lizmapPluginHasWarningsUrl) {
            let messageHasWarnings = lizDict['project.has.warnings'];
            messageHasWarnings += `<br><a href="${document.body.dataset.lizmapPluginHasWarningsUrl}">`;
            messageHasWarnings += lizDict['visit.admin.panel.project.page'];
            messageHasWarnings += '</a>';
            messageHasWarnings += '<br>';
            messageHasWarnings += lizDict['project.admin.panel.info'];

            // It can take times to fix these issues, the popup can be closed
            lizMap.addMessage(
                messageHasWarnings,
                'warning',
                true
            ).attr('id', 'lizmap-project-warnings');
        }
    }

    if (document.body.dataset.lizmapActionWarningOld) {
        let message = 'The project uses deprecated Action JSON format. ';
        message += 'Read https://docs.lizmap.com/current/en/publish/lizmap_plugin/actions.html for more information';
        console.warn(message);

        if (document.body.dataset.lizmapAdminUser) {
            lizMap.addMessage(
                document.body.dataset.lizmapActionWarningOld,
                'info',
                true
            ).attr('id','lizmap-action-message');
        }
    }
}
