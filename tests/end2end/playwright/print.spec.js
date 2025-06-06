// @ts-check
import { dirname } from 'path';
import * as fs from 'fs/promises'
import { existsSync } from 'node:fs';
import { test, expect } from '@playwright/test';
import { PrintPage } from "./pages/printpage";
import { DrawPage } from "./pages/drawpage";
import {
    gotoMap,
    expectParametersToContain,
    getAuthStorageStatePath,
    expectToHaveLengthCompare,
    playwrightTestFile,
    qgisVersionFromProjectApi,
} from './globals';

// To update OSM and GeoPF tiles in the mock directory
// IMPORTANT, this must not be set to `true` while committing, on GitHub. Set to `false`.
const UPDATE_MOCK_FILES = false;


test.describe('Print', () => {

    test.beforeEach(async ({ page }) => {
        const url = '/index.php/view/map/?repository=testsrepository&project=print';
        await gotoMap(url, page)

        await page.locator('#button-print').click();

        await page.locator('#print-scale').selectOption('100000');
    });

    test('Print UI', async ({ page }) => {
        // Scales
        await expect(page.locator('#print-scale > option')).toHaveCount(6);
        await expect(page.locator('#print-scale > option')).toContainText(
            ['500,000', '250,000', '100,000', '50,000', '25,000', '10,000']);
        // Templates
        await expect(page.locator('#print-template > option')).toHaveCount(3);
        await expect(page.locator('#print-template > option')).toContainText(['print_labels', 'print_map']);

        // Test `print_labels` template

        // Format and DPI are not displayed as there is only one value
        await expect(page.locator('#print-format')).toHaveCount(0);
        await expect(page.locator('.print-dpi')).toHaveCount(0);

        // Test `print_map` template
        await page.locator('#print-template').selectOption('1');

        // Format and DPI lists exist as there are multiple values
        await expect(page.locator('#print-format > option')).toHaveCount(2);
        await expect(page.locator('#print-format > option')).toContainText(['JPEG', 'PNG']);
        await expect(page.locator('.btn-print-dpis > option')).toHaveCount(2);
        await expect(page.locator('.btn-print-dpis > option')).toContainText(['100', '200']);

        // PNG is default
        expect(await page.locator('#print-format').inputValue()).toBe('jpeg');
        // 200 DPI is default
        expect(await page.locator('.btn-print-dpis').inputValue()).toBe('200');
    });

    test('Print requests', async ({ request, page }) => {
        // Required GetPrint parameters
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrint',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'CRS': 'EPSG:2154',
            'DPI': '100',
            'TEMPLATE': 'print_labels',
        }
        // Test `print_labels` template
        let getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        let getPrintRequest = await getPrintPromise;
        // Extend GetPrint parameters
        const expectedParameters1 = Object.assign({}, expectedParameters, {
            'map0:EXTENT': /759249.\d+,6271892.\d+,781949.\d+,6286892.\d+/,
            'map0:SCALE': '100000',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'simple_label': 'simple label',
            // Disabled because of the migration when project is saved with QGIS >= 3.32
            // 'multiline_label': 'Multiline label',
        })
        let expectedLength = 15;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 14;
        }
        let name = "Print requests";
        let getPrintParams = await expectParametersToContain(
            name, getPrintRequest.postData() ?? '', expectedParameters1);
        await expectToHaveLengthCompare(name, Array.from(getPrintParams.keys()), expectedLength, Object.keys(expectedParameters1));

        // Test `print_map` template
        await page.locator('#print-template').selectOption('1');

        getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );
        await page.locator('#print-launch').click();
        getPrintRequest = await getPrintPromise;
        // Extend and update GetPrint parameters
        const expectedParameters2 = Object.assign({}, expectedParameters, {
            'FORMAT': 'jpeg',
            'DPI': '200',
            'TEMPLATE': 'print_map',
            'map0:EXTENT': /765699.\d+,6271792.\d+,775499.\d+,6286992.\d+/,
            'map0:SCALE': '100000',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
        })
        name = 'Print requests 2';
        getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters2);
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            13, Object.keys(expectedParameters2)
        );

        // Test `print_overview` template
        await page.locator('#print-template').selectOption('2');
        getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        getPrintRequest = await getPrintPromise;
        // Extend and update GetPrint parameters
        const expectedParameters3 = Object.assign({}, expectedParameters, {
            'TEMPLATE': 'print_overview',
            'map1:EXTENT': /757949.\d+,6270842.\d+,783249.\d+,6287942.\d+/,
            'map1:SCALE': '100000',
            'map1:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map1:STYLES': 'default,défaut,défaut',
            'map1:OPACITIES': '204,255,255',
            'map0:EXTENT': /761864.\d+,6274266.\d+,779334.\d+,6284518.\d+/,
        })
        name = 'Print requests 3';
        getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters3);
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            14,
            Object.keys(expectedParameters3)
        );

        // Redlining with circle
        await page.locator('#button-draw').click();
        await page.getByRole('button', { name: 'Toggle Dropdown' }).click();
        await page.locator('#draw .digitizing-circle > svg').click();
        await page.locator('#newOlMap').click({
            position: {
                x: 610,
                y: 302
            }
        });
        await page.locator('#newOlMap').click({
            position: {
                x: 722,
                y: 300
            }
        });

        await page.locator('#button-print').click();
        await page.locator('#print-scale').selectOption('100000');

        getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        getPrintRequest = await getPrintPromise;
        // Extend and update GetPrint parameters
        /* eslint-disable no-useless-escape, @stylistic/js/max-len --
         * Block of SLD
        **/
        const expectedParameters4 = Object.assign({}, expectedParameters, {
            'TEMPLATE': 'print_labels',
            'map0:EXTENT': /759249.\d+,6271892.\d+,781949.\d+,6286892.\d+/,
            'map0:SCALE': '100000',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'map0:HIGHLIGHT_GEOM': /CURVEPOLYGON\(CIRCULARSTRING\(\n +772265.\d+ 6279008.\d+,\n +775229.\d+ 6281972.\d+,\n +778193.\d+ 6279008.\d+,\n +775229.\d+ 6276044.\d+,\n +772265.\d+ 6279008.\d+\)\)/,
            'map0:HIGHLIGHT_SYMBOL': `<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <StyledLayerDescriptor xmlns=\"http://www.opengis.net/sld\" xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" version=\"1.1.0\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xsi:schemaLocation=\"http://www.opengis.net/sld http://schemas.opengis.net/sld/1.1.0/StyledLayerDescriptor.xsd\" xmlns:se=\"http://www.opengis.net/se\">
            <UserStyle>
                <FeatureTypeStyle>
                    <Rule>
                        <PolygonSymbolizer>
                <Stroke>
            <SvgParameter name=\"stroke\">#ff0000</SvgParameter>
            <SvgParameter name=\"stroke-opacity\">1</SvgParameter>
            <SvgParameter name=\"stroke-width\">2</SvgParameter>
        </Stroke>
        <Fill>
            <SvgParameter name=\"fill\">#ff0000</SvgParameter>
            <SvgParameter name=\"fill-opacity\">0.2</SvgParameter>
        </Fill>
            </PolygonSymbolizer>
                    </Rule>
                </FeatureTypeStyle>
            </UserStyle>
        </StyledLayerDescriptor>`,
            'simple_label': 'simple label',
            // Disabled because of the migration when project is saved with QGIS >= 3.32
            // 'multiline_label': 'Multiline label',
        })
        /* eslint-enable no-useless-escape, @stylistic/js/max-len */
        expectedLength = 17
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 16;
        }
        name = 'Print requests 4';
        getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? '', expectedParameters4
        );
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            expectedLength,
            Object.keys(expectedParameters4)
        );
    });

    test('Print requests with selection', async ({ request, page }) => {
        // Select a feature
        await page.locator('#button-attributeLayers').click();
        await page.getByRole('button', { name: 'Detail' }).click();
        await page.locator(
            'lizmap-feature-toolbar:nth-child(1) > div:nth-child(1) > button:nth-child(1)').first().click();
        await page.locator('#bottom-dock-window-buttons .btn-bottomdock-clear').click();

        const getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        const getPrintRequest = await getPrintPromise;
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrint',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'CRS': 'EPSG:2154',
            'DPI': '100',
            'TEMPLATE': 'print_labels',
            'map0:EXTENT': /759249.\d+,6271892.\d+,781949.\d+,6286892.\d+/,
            'map0:SCALE': '100000',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'simple_label': 'simple label',
            'SELECTIONTOKEN': /[a-z\d]+/,
        }
        const name = "Print requests with selection";
        const getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters);
        let expectedLength = 16;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 15;
        }
        await expectToHaveLengthCompare(name, Array.from(getPrintParams.keys()), expectedLength, Object.keys(expectedParameters));

    });

    test('Print requests with filter', async ({ request, page }) => {
        // Select a feature
        await page.locator('#button-attributeLayers').click();
        await page.getByRole('button', { name: 'Detail' }).click();
        await page.locator('lizmap-feature-toolbar:nth-child(1) > div:nth-child(1) > button:nth-child(1)').first().click();
        await page.locator('#bottom-dock-window-buttons .btn-bottomdock-clear').click();

        // Filter selected feature
        await page.locator('#button-attributeLayers').click();
        const responseMatchGetFilterTokenFunc = function (response) {
            return (response.request().method() == 'POST' && response.request().postData().match(/GetFilterToken/i));
        };
        await page.locator('.btn-filter-attributeTable').click();
        let getFilterTokenPromise = page.waitForResponse(responseMatchGetFilterTokenFunc);
        await getFilterTokenPromise;

        await page.locator('#bottom-dock-window-buttons .btn-bottomdock-clear').click();
        const getPrintPromise = page.waitForRequest(request => request.method() === 'POST' && request.postData()?.includes('GetPrint') === true);

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        const getPrintRequest = await getPrintPromise;
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrint',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'CRS': 'EPSG:2154',
            'DPI': '100',
            'TEMPLATE': 'print_labels',
            'map0:EXTENT': /759249.\d+,6271892.\d+,781949.\d+,6286892.\d+/,
            'map0:SCALE': '100000',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'simple_label': 'simple label',
            'FILTERTOKEN': /[a-z\d]+/,
        }
        let expectedLength = 16;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 15;
        }
        const name = 'Print requests with filter';
        const getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters);
        await expectToHaveLengthCompare(name, Array.from(getPrintParams.keys()), expectedLength, Object.keys(expectedParameters));
    });
});

test.describe(
    'Print opacities',
    {
        tag: ['@readonly'],
    },() =>
    {

        test('Group as layer', async ({ page }) => {

            const printPage = new PrintPage(page, 'group_as_layer_opacity');
            await printPage.open();
            await printPage.openPrintPanel();
            await printPage.setPrintScale('50000');

            // set opacity of `Group_opacity` (group as layer) layer to 60%
            await printPage.setLayerOpacity('Group_opacity','60');

            // opacity notes:
            // the `raster` layer already has 80% opacity (inherited from QGIS project)
            // setting the opacity to 60% on the `Group_opacity` causes:
            // - `raster` layer to have a final total opacity of 48% (0.8*0.6)
            //          -> OPACITIES PARAM = 255*0.48 ~= 122
            // - `single_wms_polygons` layer to have a final total opacity of 60% (1*0.6)
            //          -> OPACITIES PARAM = 255*0.6 = 153
            // other layers have opacity 100% (255)

            const mapLayers = [
                'OpenStreetMap',
                'raster',
                'single_wms_polygons',
                'single_wms_polygons_group_as_layer',
                'single_wms_points_group',
            ]

            const expectedParameters = {
                'SERVICE': 'WMS',
                'REQUEST': 'GetPrint',
                'VERSION': '1.3.0',
                'FORMAT': 'pdf',
                'TRANSPARENT': 'true',
                'CRS': 'EPSG:3857',
                'DPI': '100',
                'TEMPLATE': 'test',
                'map0:EXTENT': /425189.\d+,5401412.\d+,439539.\d+,5411262.\d+/,
                'map0:SCALE': '50000',
                'map0:LAYERS': mapLayers.join(','),
                'map0:STYLES': 'default,default,default,default,default',
                'map0:OPACITIES': '255,122,153,255,255',
            }
            // Test `test` template
            let getPrintPromise = page.waitForRequest(
                request =>
                    request.method() === 'POST' &&
                    request.postData()?.includes('GetPrint') === true
            );

            // Launch print
            await printPage.launchPrint();

            // check request
            let getPrintRequest = await getPrintPromise;

            // check response parameters
            let name = "Group as layer opacity requests";
            let getPrintParams = await expectParametersToContain(
                name, getPrintRequest.postData() ?? '', expectedParameters);

            await expectToHaveLengthCompare(
                name,
                Array.from(getPrintParams.keys()),
                13,
                Object.keys(expectedParameters)
            );
        })

        test('Layers in group with opacity', async ({ page }) => {
            const printPage = new PrintPage(page, 'group_as_layer_opacity');
            await printPage.open();
            await printPage.openPrintPanel();
            await printPage.setPrintScale('50000');

            // set opacity of `Group_a` group opacity to 60%
            await printPage.setLayerOpacity('Group_a','60');
            // set opacity of `single_wms_points_group` (belonging to `Group_a`) layer to 40%
            await printPage.setLayerOpacity('single_wms_points_group','40');
            // set opacity of `single_wms_polygons_group_as_layer` (belonging to `Group_a`) layer to 80%
            await printPage.setLayerOpacity('single_wms_polygons_group_as_layer','80');

            // opacity notes:
            // setting the opacity to 60% on `Group_a` causes:
            // - `single_wms_points_group` layer to have a final total opacity of 24% (0.4*0.6)
            //              -> OPACITIES PARAM = 255*0.24 ~= 61
            // - `single_wms_polygons_group_as_layer` layer to have a final total opacity of 48% (0.8*0.6)
            //              -> OPACITIES PARAM = 255*0.48 ~= 122
            // other layers have opacity 100%, except for `raster` layer which has a 80% opacity by default,
            // resulting in a OPACITIES PARAM = 255*0.8 = 204

            const mapLayers = [
                'OpenStreetMap',
                'raster',
                'single_wms_polygons',
                'single_wms_polygons_group_as_layer',
                'single_wms_points_group',
            ]

            const expectedParameters = {
                'SERVICE': 'WMS',
                'REQUEST': 'GetPrint',
                'VERSION': '1.3.0',
                'FORMAT': 'pdf',
                'TRANSPARENT': 'true',
                'CRS': 'EPSG:3857',
                'DPI': '100',
                'TEMPLATE': 'test',
                'map0:EXTENT': /425189.\d+,5401412.\d+,439539.\d+,5411262.\d+/,
                'map0:SCALE': '50000',
                'map0:LAYERS': mapLayers.join(','),
                'map0:STYLES': 'default,default,default,default,default',
                'map0:OPACITIES': '255,204,255,122,61',
            }
            // Test `test` template
            let getPrintPromise = page.waitForRequest(
                request =>
                    request.method() === 'POST' &&
                    request.postData()?.includes('GetPrint') === true
            );

            // Launch print
            await printPage.launchPrint();

            // check request
            let getPrintRequest = await getPrintPromise;

            // check response parameters
            let name = "Layers in group with opacity request";
            let getPrintParams = await expectParametersToContain(
                name, getPrintRequest.postData() ?? '', expectedParameters);
            await expectToHaveLengthCompare(
                name,
                Array.from(getPrintParams.keys()),
                13,
                Object.keys(expectedParameters)
            );
        })
    }
);

test.describe('Print in popup', () => {
    test.beforeEach(async ({ page }) => {
        const url = '/index.php/view/map/?repository=testsrepository&project=print';
        await gotoMap(url, page)
        let getFeatureInfoRequestPromise = page.waitForRequest(request => request.method() === 'POST' && request.postData()?.includes('GetFeatureInfo') === true);
        await page.locator('#newOlMap').click({ position: { x: 409, y: 186 } });
        let getFeatureInfoRequest = await getFeatureInfoRequestPromise;
        expect(getFeatureInfoRequest.postData()).toMatch(/GetFeatureInfo/);
    });

    test('Popup content print', async ({ page }) => {
        const featureAtlasQuartiers = page.locator('#popupcontent lizmap-feature-toolbar[value="quartiers_cc80709a_cd4a_41de_9400_1f492b32c9f7.1"] .feature-print');
        await expect(featureAtlasQuartiers).toHaveCount(1);

        const featureAtlasSousQuartiers = page.locator('#popupcontent lizmap-feature-toolbar[value="sousquartiers_e27e6af0_dcc5_4700_9730_361437f69862.2"] .feature-print');
        await expect(featureAtlasSousQuartiers).toHaveCount(1);
    });

    test('Atlas print in popup UI', async ({ page }) => {
        // "quartiers" layer has one atlas (name "atlas_quartiers") button configured with a custom icon
        const featureAtlasQuartiers = page.locator('#popupcontent lizmap-feature-toolbar[value="quartiers_cc80709a_cd4a_41de_9400_1f492b32c9f7.1"] .feature-atlas');
        await expect(featureAtlasQuartiers).toHaveCount(1);
        await expect(featureAtlasQuartiers.locator('button')).toHaveAttribute('data-bs-title', 'atlas_quartiers');
        await expect(featureAtlasQuartiers.locator('img')).toHaveAttribute('src', '/index.php/view/media/getMedia?repository=testsrepository&project=print&path=media/svg/tree-fill.svg');

        // "sousquartiers" layer has one atlas (name "atlas_sousquartiers") button configured with the default icon
        const featureAtlasSousQuartiers = page.locator('#popupcontent lizmap-feature-toolbar[value="sousquartiers_e27e6af0_dcc5_4700_9730_361437f69862.2"] .feature-atlas');
        await expect(featureAtlasSousQuartiers).toHaveCount(1);
        await expect(featureAtlasSousQuartiers.locator('button')).toHaveAttribute('data-bs-title', 'atlas_sousquartiers');
        await expect(featureAtlasSousQuartiers.locator('svg use')).toHaveAttribute('xlink:href', '#map-print');
    });

    test('Atlas print in popup requests', async ({ page }) => {
        // Test `atlas_quartiers` print atlas request
        const featureAtlasQuartiers = page.locator('#popupcontent lizmap-feature-toolbar[value="quartiers_cc80709a_cd4a_41de_9400_1f492b32c9f7.1"] .feature-atlas');

        const getPrintPromise = page.waitForRequest(request => request.method() === 'POST' && request.postData()?.includes('GetPrint') === true);
        await featureAtlasQuartiers.locator('button').click();
        const getPrintRequest = await getPrintPromise;
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrintAtlas',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'DPI': '100',
            'TEMPLATE': 'atlas_quartiers',
            'LAYER': 'quartiers',
            'EXP_FILTER': '$id IN (1)',
        }
        const name = 'Atlas print in popup requests';
        const getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters);
        await expectToHaveLengthCompare(name, Array.from(getPrintParams.keys()), 10, Object.keys(expectedParameters));

        await expect(getPrintParams.has('CRS')).toBe(false)
        await expect(getPrintParams.has('LAYERS')).toBe(false)
        await expect(getPrintParams.has('ATLAS_PK')).toBe(false)

        // Test `atlas_quartiers` print atlas response
        const response = await getPrintRequest.response();
        await expect(response?.status()).toBe(200)

        await expect(response?.headers()['content-type']).toBe('application/pdf');
        await expect(response?.headers()['content-disposition']).toBe('attachment; filename="print_atlas_quartiers.pdf"');
    });
});

test.describe('Print - user in group a', () => {
    test.use({ storageState: getAuthStorageStatePath('user_in_group_a') });

    test.beforeEach(async ({ page }) => {
        const url = '/index.php/view/map/?repository=testsrepository&project=print';
        await gotoMap(url, page)

        await page.locator('#button-print').click();

        await page.locator('#print-scale').selectOption('100000');
    });

    test('Print UI', async ({ page }) => {
        // Templates
        await expect(page.locator('#print-template > option')).toHaveCount(3);
        await expect(page.locator('#print-template > option')).toContainText(['print_labels', 'print_map']);

        // Test `print_labels` template

        // Format and DPI are not displayed as there is only one value
        await expect(page.locator('#print-format')).toHaveCount(0);
        await expect(page.locator('.print-dpi')).toHaveCount(0);

        // Test `print_map` template
        await page.locator('#print-template').selectOption('1');

        // Format and DPI lists exist as there are multiple values
        await expect(page.locator('#print-format > option')).toHaveCount(2);
        await expect(page.locator('#print-format > option')).toContainText(['JPEG', 'PNG']);
        await expect(page.locator('.btn-print-dpis > option')).toHaveCount(2);
        await expect(page.locator('.btn-print-dpis > option')).toContainText(['100', '200']);

        // PNG is default
        expect(await page.locator('#print-format').inputValue()).toBe('jpeg');
        // 200 DPI is default
        expect(await page.locator('.btn-print-dpis').inputValue()).toBe('200');
    });
});

test.describe('Print - admin', () => {
    test.use({ storageState: getAuthStorageStatePath('admin') });

    test.beforeEach(async ({ page }) => {
        const url = '/index.php/view/map/?repository=testsrepository&project=print';
        await gotoMap(url, page)

        await page.locator('#button-print').click();

        await page.locator('#print-scale').selectOption('100000');
    });

    test('Print UI', async ({ page }) => {
        // Templates
        await expect(page.locator('#print-template > option')).toHaveCount(4);
        await expect(page.locator('#print-template > option')).toContainText(['print_labels', 'print_map', 'print_allowed_groups']);

        // Test `print_labels` template

        // Format and DPI are not displayed as there is only one value
        await expect(page.locator('#print-format')).toHaveCount(0);
        await expect(page.locator('.print-dpi')).toHaveCount(0);

        // Test `print_map` template
        await page.locator('#print-template').selectOption('1');

        // Format and DPI lists exist as there are multiple values
        await expect(page.locator('#print-format > option')).toHaveCount(2);
        await expect(page.locator('#print-format > option')).toContainText(['JPEG', 'PNG']);
        await expect(page.locator('.btn-print-dpis > option')).toHaveCount(2);
        await expect(page.locator('.btn-print-dpis > option')).toContainText(['100', '200']);

        // PNG is default
        expect(await page.locator('#print-format').inputValue()).toBe('jpeg');
        // 200 DPI is default
        expect(await page.locator('.btn-print-dpis').inputValue()).toBe('200');

        // Test `print_allowed_groups` template
        await page.locator('#print-template').selectOption('2');

        // Format and DPI lists exist as there are multiple values
        await expect(page.locator('#print-format > option')).toHaveCount(4);
        await expect(page.locator('#print-format > option')).toContainText(['PDF', 'SVG', 'PNG', 'JPEG']);
        await expect(page.locator('.btn-print-dpis > option')).toHaveCount(3);
        await expect(page.locator('.btn-print-dpis > option')).toContainText(['100', '200', '300']);

        // PNG is default
        expect(await page.locator('#print-format').inputValue()).toBe('pdf');
        // 200 DPI is default
        expect(await page.locator('.btn-print-dpis').inputValue()).toBe('100');
    });
});

test.describe('Print 3857', () => {

    test.beforeEach(async ({ page }) => {
        const url = '/index.php/view/map/?repository=testsrepository&project=print_3857';
        await gotoMap(url, page)

        await page.locator('#button-print').click();

        await page.locator('#print-scale').selectOption('72224');
    });

    test('Print UI', async ({ page }) => {
        // Scales
        await expect(page.locator('#print-scale > option')).toHaveCount(5);
        await expect(page.locator('#print-scale > option')).toContainText(['288,895', '144,448', '72,224', '36,112', '18,056']);
        // Templates
        await expect(page.locator('#print-template > option')).toHaveCount(2);
        await expect(page.locator('#print-template > option')).toContainText(['print_labels', 'print_map']);

        // Test `print_labels` template

        // Format and DPI are not displayed as there is only one value
        await expect(page.locator('#print-format')).toHaveCount(0);
        await expect(page.locator('.print-dpi')).toHaveCount(0);

        // Test `print_map` template
        await page.locator('#print-template').selectOption('1');

        // Format and DPI lists exist as there are multiple values
        await expect(page.locator('#print-format > option')).toHaveCount(2);
        await expect(page.locator('#print-format > option')).toContainText(['JPEG', 'PNG']);
        await expect(page.locator('.btn-print-dpis > option')).toHaveCount(2);
        await expect(page.locator('.btn-print-dpis > option')).toContainText(['100', '200']);

        // PNG is default
        expect(await page.locator('#print-format').inputValue()).toBe('jpeg');
        // 200 DPI is default
        expect(await page.locator('.btn-print-dpis').inputValue()).toBe('200');
    });

    test('Print requests', async ({ request, page }) => {
        // Required GetPrint parameters
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrint',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'CRS': 'EPSG:3857',
            'DPI': '100',
            'TEMPLATE': 'print_labels',
        }
        // Test `print_labels` template
        let getPrintPromise = page.waitForRequest(request => request.method() === 'POST' && request.postData()?.includes('GetPrint') === true);

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        let getPrintRequest = await getPrintPromise;
        // Extend GetPrint parameters
        const expectedParameters1 = Object.assign({}, expectedParameters, {
            'map0:EXTENT': /423093.\d+,5399873.\d+,439487.\d+,5410707.\d+/,
            'map0:SCALE': '72224',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'simple_label': 'simple label',
            // Disabled because of the migration when project is saved with QGIS >= 3.32
            // 'multiline_label': 'Multiline label',
        })
        let name = "Print requests 1";
        let getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? '',
            expectedParameters1
        );
        let expectedLength = 15;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 14;
        }
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            expectedLength,
            Object.keys(expectedParameters1)
        );

        // Test `print_map` template
        await page.locator('#print-template').selectOption('1');
        getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        getPrintRequest = await getPrintPromise;
        // Extend and update GetPrint parameters
        const expectedParameters2 = Object.assign({}, expectedParameters, {
            'FORMAT': 'jpeg',
            'DPI': '200',
            'TEMPLATE': 'print_map',
            'map0:EXTENT': /427751.\d+,5399801.\d+,434829.\d+,5410779.\d+/,
            'map0:SCALE': '72224',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
        })
        name = 'Print requests 2';
        getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters2)
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            13,
            Object.keys(expectedParameters2)
        );
    });

    test('Print requests with redlining', async ({ request, page }) => {
        const printPage = new PrintPage(page, 'draw');
        const drawProject = new DrawPage(page, 'draw');
        // Close left dock
        await drawProject.closeLeftDock();
        // open draw panel
        await drawProject.openDrawPanel();

        // select circle to draw
        await drawProject.selectGeometry('circle');

        // Draw circle
        await drawProject.clickOnMap(610, 302);
        await drawProject.clickOnMap(722, 300);


        await printPage.openPrintPanel();
        await printPage.setPrintScale('72224');

        let getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await printPage.launchPrint();

        // check request
        let getPrintRequest = await getPrintPromise;

        // Required GetPrint parameters
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrint',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'CRS': 'EPSG:3857',
            'DPI': '100',
            'TEMPLATE': 'print_labels',
        }

        // Expected GetPrint parameters
        /* eslint-disable no-useless-escape, @stylistic/js/max-len --
         * Block of SLD
        **/
        const expectedParameters1 = Object.assign({}, expectedParameters, {
            'map0:EXTENT': /423093.\d+,5399873.\d+,439487.\d+,5410707.\d+/,
            'map0:SCALE': '72224',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'map0:HIGHLIGHT_GEOM': /CURVEPOLYGON\(CIRCULARSTRING\(\n +433697.\d+ 5404736.\d+,\n +437978.\d+ 5409017.\d+,\n +442259.\d+ 5404736.\d+,\n +437978.\d+ 5400455.\d+,\n +433697.\d+ 5404736.\d+\)\)/,
            'map0:HIGHLIGHT_SYMBOL': `<?xml version=\"1.0\" encoding=\"UTF-8\"?>
    <StyledLayerDescriptor xmlns=\"http://www.opengis.net/sld\" xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" version=\"1.1.0\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xsi:schemaLocation=\"http://www.opengis.net/sld http://schemas.opengis.net/sld/1.1.0/StyledLayerDescriptor.xsd\" xmlns:se=\"http://www.opengis.net/se\">
            <UserStyle>
                <FeatureTypeStyle>
                    <Rule>
                        <PolygonSymbolizer>
                <Stroke>
            <SvgParameter name=\"stroke\">#ff0000</SvgParameter>
            <SvgParameter name=\"stroke-opacity\">1</SvgParameter>
            <SvgParameter name=\"stroke-width\">2</SvgParameter>
        </Stroke>
        <Fill>
            <SvgParameter name=\"fill\">#ff0000</SvgParameter>
            <SvgParameter name=\"fill-opacity\">0.2</SvgParameter>
        </Fill>
            </PolygonSymbolizer>
                    </Rule>
                </FeatureTypeStyle>
            </UserStyle>
        </StyledLayerDescriptor>`,
            'simple_label': 'simple label',
            // Disabled because of the migration when project is saved with QGIS >= 3.32
            // 'multiline_label': 'Multiline label',
        })
        /* eslint-enable no-useless-escape, @stylistic/js/max-len */
        let name = 'Print requests redlining 1';
        let getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? ''
            , expectedParameters1
        );
        let expectedLength = 17;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 16;
        }
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            expectedLength,
            Object.keys(expectedParameters1)
        );

        let getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');

        // open draw panel
        await drawProject.openDrawPanel();
        // select point to draw
        await drawProject.selectGeometry('point');
        // Draw point
        await drawProject.clickOnMap(480, 300);

        // Toggle edit, two geometries are available, the text tools are not visible
        await drawProject.toggleEdit();

        // Edit second point By clicking on the map
        await page.waitForTimeout(1000);
        await drawProject.clickOnMap(480, 300);
        await drawProject.setTextContentValue('test');

        await printPage.openPrintPanel();
        await printPage.setPrintScale('72224');

        getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await printPage.launchPrint();

        // check request
        getPrintRequest = await getPrintPromise;

        // Expected GetPrint parameters
        const expectedParameters2 = Object.assign({}, expectedParameters, {
            'map0:EXTENT': /423093.\d+,5399873.\d+,439487.\d+,5410707.\d+/,
            'map0:SCALE': '72224',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'map0:HIGHLIGHT_SYMBOL': /.*/,
            'map0:HIGHLIGHT_GEOM': /CURVEPOLYGON\(CIRCULARSTRING\([\s\d.,]*\)\);POINT\([\s\d.,]*\)/,
            'map0:HIGHLIGHT_LABELSTRING': ' ;test',
            'map0:HIGHLIGHT_LABELSIZE': '10;10',
            'map0:HIGHLIGHT_LABELBUFFERCOLOR': '#FFFFFF;#FFFFFF',
            'map0:HIGHLIGHT_LABELBUFFERSIZE': '1.5;1.5',
            'map0:HIGHLIGHT_LABEL_ROTATION': '0;0',
            'map0:HIGHLIGHT_LABEL_HORIZONTAL_ALIGNMENT': 'center;center',
            'map0:HIGHLIGHT_LABEL_VERTICAL_ALIGNMENT': 'half;half',
        });

        name = 'Print requests redlining 2';
        getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? ''
            , expectedParameters2
        );
        expectedLength = 24;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 23;
        }
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            expectedLength,
            Object.keys(expectedParameters2)
        );

        getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');

        // open draw panel
        await drawProject.openDrawPanel();
        // Activate erase tool
        await drawProject.toggleErase();

        // Delete circle
        page.on('dialog', dialog => dialog.accept());
        await drawProject.clickOnMap(610, 302);
        await page.waitForTimeout(300);

        await printPage.openPrintPanel();
        await printPage.setPrintScale('72224');

        getPrintPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await printPage.launchPrint();

        // check request
        getPrintRequest = await getPrintPromise;

        // Expected GetPrint parameters
        const expectedParameters3 = Object.assign({}, expectedParameters, {
            'map0:EXTENT': /423093.\d+,5399873.\d+,439487.\d+,5410707.\d+/,
            'map0:SCALE': '72224',
            'map0:LAYERS': 'OpenStreetMap,quartiers,sousquartiers',
            'map0:STYLES': 'default,défaut,défaut',
            'map0:OPACITIES': '204,255,255',
            'map0:HIGHLIGHT_SYMBOL': /.*/,
            'map0:HIGHLIGHT_GEOM': /POINT\([\s\d.,]*\)/,
            'map0:HIGHLIGHT_LABELSTRING': 'test',
            'map0:HIGHLIGHT_LABELSIZE': '10',
            'map0:HIGHLIGHT_LABELBUFFERCOLOR': '#FFFFFF',
            'map0:HIGHLIGHT_LABELBUFFERSIZE': '1.5',
            'map0:HIGHLIGHT_LABEL_ROTATION': '0',
            'map0:HIGHLIGHT_LABEL_HORIZONTAL_ALIGNMENT': 'center',
            'map0:HIGHLIGHT_LABEL_VERTICAL_ALIGNMENT': 'half',
        });

        name = 'Print requests redlining 3';
        getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? ''
            , expectedParameters3
        );
        expectedLength = 24;
        if (await qgisVersionFromProjectApi(request, 'print') > 33200) {
            expectedLength = 23;
        }
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            expectedLength,
            Object.keys(expectedParameters3)
        );

        getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');
    });
});

test.describe('Print base layers', () => {
    test.beforeEach(async ({ page }) => {
        // Catch openstreetmap requests to mock them
        let GetTiles = [];
        await page.route('https://tile.openstreetmap.org/*/*/*.png', async (route) => {
            const request = route.request();
            GetTiles.push(request.url());

            // Build path file in mock directory
            const pathFile = playwrightTestFile('mock', 'base_layers', 'osm' , 'tiles' + (new URL(request.url()).pathname));
            if (UPDATE_MOCK_FILES && GetTiles.length <= 6) {
                // Save file in mock directory for 6 tiles maximum
                const response = await route.fetch();
                await fs.mkdir(dirname(pathFile), { recursive: true })
                await fs.writeFile(pathFile, await response.body())
            } else if (existsSync(pathFile)) {
                // fulfill route's request with mock file
                await route.fulfill({
                    path: pathFile
                })
            } else {
                // fulfill route's request with default transparent tile
                await route.fulfill({
                    path: playwrightTestFile('mock', 'transparent_tile.png')
                })
            }
        });

        const url = '/index.php/view/map/?repository=testsrepository&project=base_layers';
        await gotoMap(url, page)

        await page.locator('#button-print').click();

        await page.locator('#print-scale').selectOption('72224');

        while (GetTiles.length < 6) {
            await page.waitForTimeout(100);
        }
        await expect(GetTiles.length).toBeGreaterThanOrEqual(6);
        await page.unroute('https://tile.openstreetmap.org/*/*/*.png');
    });

    test('Print requests', async ({ page }) => {
        // Required GetPrint parameters
        const expectedParameters = {
            'SERVICE': 'WMS',
            'REQUEST': 'GetPrint',
            'VERSION': '1.3.0',
            'FORMAT': 'pdf',
            'TRANSPARENT': 'true',
            'CRS': 'EPSG:3857',
            'DPI': '100',
            'TEMPLATE': 'simple',
            'map0:EXTENT': /420548.\d+,5397710.\d+,441999.\d+,5412877.\d+/,
            'map0:SCALE': '72224',
        }
        // Print osm-mapnik
        let getPrintRequestPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        let getPrintRequest = await getPrintRequestPromise;
        // Extend GetPrint parameters
        const expectedParameters1 = Object.assign({}, expectedParameters, {
            'map0:LAYERS': 'osm-mapnik',
            'map0:STYLES': 'défaut',
            'map0:OPACITIES': '255',
        })
        let name = 'Print requests 1';
        let getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? '',
            expectedParameters1,
        );
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            13,
            Object.keys(expectedParameters1)
        );

        let getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');

        // Print osm-mapnik & quartiers
        let getMapRequestPromise = page.waitForRequest(/REQUEST=GetMap/);
        await page.getByLabel('quartiers').check();
        let getMapRequest = await getMapRequestPromise;
        await getMapRequest.response();

        getPrintRequestPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        getPrintRequest = await getPrintRequestPromise;
        // Extend and update GetPrint parameters
        const expectedParameters2 = Object.assign({}, expectedParameters, {
            'map0:LAYERS': 'osm-mapnik,quartiers',
            'map0:STYLES': 'défaut,default',
            'map0:OPACITIES': '255,255',
        })
        name = 'Print requests 2';
        getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? '',
            expectedParameters2
        );
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            13,
            Object.keys(expectedParameters2)
        );

        getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');

        // Print quartiers not open-topo-map
        // Catch opentopomap request to mock them
        let GetTiles = [];
        await page.route('https://*.tile.opentopomap.org/*/*/*.png', async (route) => {
            const request = route.request();
            GetTiles.push(request.url());

            // Build path file in mock directory
            const pathFile = playwrightTestFile('mock', 'base_layers', 'opentopomap' , 'tiles' + (new URL(request.url()).pathname));
            if (UPDATE_MOCK_FILES && GetTiles.length <= 6) {
                // Save file in mock directory for 6 tiles maximum
                try {
                    const response = await route.fetch();
                    await fs.mkdir(dirname(pathFile), { recursive: true })
                    await fs.writeFile(pathFile, await response.body())
                } catch {

                    // fulfill route's request with default transparent tile
                    await route.fulfill({
                        path: playwrightTestFile('mock', 'transparent_tile.png')
                    })
                }
            } else if (existsSync(pathFile)) {
                // fulfill route's request with mock file
                await route.fulfill({
                    path: pathFile
                })
            } else {
                // fulfill route's request with default transparent tile
                await route.fulfill({
                    path: playwrightTestFile('mock', 'transparent_tile.png')
                })
            }
        });
        await page.locator('#switcher-baselayer').getByRole('combobox').selectOption('open-topo-map');

        while (GetTiles.length < 6) {
            await page.waitForTimeout(100);
        }
        await expect(GetTiles.length).toBeGreaterThanOrEqual(6);
        await page.unroute('https://*.tile.opentopomap.org/*/*/*.png');

        getPrintRequestPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        getPrintRequest = await getPrintRequestPromise;
        // Extend and update GetPrint parameters
        const expectedParameters3 = Object.assign({}, expectedParameters, {
            'map0:LAYERS': 'quartiers',
            'map0:STYLES': 'default',
            'map0:OPACITIES': '255',
        })
        name = 'Print requests 3';
        getPrintParams = await expectParametersToContain(
            name,
            getPrintRequest.postData() ?? '',
            expectedParameters3
        );
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            13,
            Object.keys(expectedParameters3)
        );

        getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');

        // Print quartiers_baselayer & quartiers
        await page.locator('#switcher-baselayer').getByRole('combobox').selectOption('quartiers_baselayer');
        getMapRequest = await getMapRequestPromise;
        await getMapRequest.response();

        getPrintRequestPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetPrint') === true
        );

        // Launch print
        await page.locator('#print-launch').click();

        // check request
        getPrintRequest = await getPrintRequestPromise;
        // Extend and update GetPrint parameters
        const expectedParameters4 = Object.assign({}, expectedParameters, {
            'map0:LAYERS': 'quartiers_baselayer,quartiers',
            'map0:STYLES': 'default,default',
            'map0:OPACITIES': '255,255',
        })
        name = 'Print requests 4';
        getPrintParams = await expectParametersToContain(name, getPrintRequest.postData() ?? '', expectedParameters4)
        await expectToHaveLengthCompare(
            name,
            Array.from(getPrintParams.keys()),
            13,
            Object.keys(expectedParameters4)
        );

        getPrintResponse = await getPrintRequest.response();
        await expect(getPrintResponse?.status()).toBe(200)
        await expect(getPrintResponse?.headers()['content-type']).toBe('application/pdf');
    });
});

test.describe('Error while printing', () => {

    test.beforeEach(async ({ page }) => {
        const url = '/index.php/view/map/?repository=testsrepository&project=print';
        await gotoMap(url, page)
    });

    test('Print error', async ({ page }) => {
        await page.locator('#button-print').click();

        await page.locator('#print-scale').selectOption('100000');

        await page.route('**/service*', async route => {
            if (route.request()?.postData()?.includes('GetPrint'))
                await route.fulfill({
                    status: 404,
                    contentType: 'text/plain',
                    body: 'Not Found!'
                });
            else
                await route.continue();
        });

        await page.locator('#print-launch').click();

        await expect(
            page.getByText('The output is currently not available. Please contact the system administrator.')
        ).toBeVisible();

        await expect(page.locator("#message > div:last-child")).toHaveClass(/alert-danger/);
    });


    test('Print Atlas error', async ({ page }) => {

        let getFeatureInfoRequestPromise = page.waitForRequest(
            request =>
                request.method() === 'POST' &&
                request.postData()?.includes('GetFeatureInfo') === true
        );
        await page.locator('#newOlMap').click({ position: { x: 409, y: 186 } });
        let getFeatureInfoRequest = await getFeatureInfoRequestPromise;
        expect(getFeatureInfoRequest.postData()).toMatch(/GetFeatureInfo/);

        // Test `atlas_quartiers` print atlas request
        const featureAtlasQuartiers = page.locator(
            '#popupcontent lizmap-feature-toolbar[value="quartiers_cc80709a_cd4a_41de_9400_1f492b32c9f7.1"] .feature-atlas'
        );

        await page.route('**/service*', async route => {
            if (route.request()?.postData()?.includes('GetPrint'))
                await route.fulfill({
                    status: 404,
                    contentType: 'text/plain',
                    body: 'Not Found!'
                });
            else
                await route.continue();
        });

        await featureAtlasQuartiers.locator('button').click();

        await expect(page.getByText(
            'The output is currently not available. Please contact the system administrator.'
        )).toBeVisible();

        await expect(page.locator("#message > div:last-child")).toHaveClass(/alert-danger/);
    });

    test('Remove print overlay when switching to another minidock', async ({ page }) => {
        await page.locator('#button-print').click();

        await page.locator('#button-selectiontool').click();

        await expect(page.locator('.ol-unselectable > canvas')).toHaveCount(0);
    });
});
